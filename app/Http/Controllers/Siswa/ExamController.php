<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function show(Exam $exam)
    {
        $user = Auth::user();
        
        // Check if student has access to this exam
        // Check class match
        $hasAccess = false;
        if ($exam->status === 'active') {
            if (empty($exam->kelas) && empty($exam->class_id)) {
                // Exam is for all classes
                $hasAccess = true;
            } elseif ($user->kelas) {
                $studentKelas = trim($user->kelas);
                $examKelas = trim($exam->kelas ?? '');
                
                // Check if classes match
                if (strtolower($studentKelas) === strtolower($examKelas)) {
                    $hasAccess = true;
                } elseif ($exam->classRelation && strtolower($studentKelas) === strtolower($exam->classRelation->name)) {
                    $hasAccess = true;
                }
            }
        }
        
        if (!$hasAccess) {
            abort(403, 'Anda tidak memiliki akses ke ujian ini.');
        }
        
        // Get or create exam result
        $examResult = ExamResult::firstOrCreate(
            [
                'exam_id' => $exam->id,
                'student_id' => $user->id,
            ],
            [
                'answers' => [],
                'status' => 'ongoing',
                'started_at' => Carbon::now(),
            ]
        );
        
        // If exam result exists but not started yet, update started_at
        if ($examResult->status === 'ongoing' && !$examResult->started_at) {
            $examResult->started_at = Carbon::now();
            $examResult->save();
        }
        
        // Check if exam time has already expired
        if ($examResult->started_at && $examResult->status === 'ongoing') {
            $durationSeconds = $exam->duration * 60;
            $endTime = Carbon::parse($examResult->started_at)->addSeconds($durationSeconds);
            
            // If time has expired, auto-submit
            if (Carbon::now()->gte($endTime)) {
                // Auto-submit the exam
                $this->autoSubmit($exam, $examResult);
                return redirect()->route('siswa.exam.result', $exam->id);
            }
        }
        
        // Load questions with order - always get fresh data from database
        // Use fresh() to ensure we get the latest question data including updated options
        $questions = $exam->questions()
            ->orderBy('exam_question.order')
            ->get()
            ->map(function($question) {
                // Refresh the question model to ensure we have latest data
                $question->refresh();
                return $question;
            });
        
        // Check if exam has questions
        if ($questions->isEmpty()) {
            return redirect()->route('siswa.ujian-aktif')
                ->with('error', 'Ujian ini belum memiliki soal. Silakan hubungi guru Anda.');
        }
        
        // Get current answers
        $answers = $examResult->answers ?? [];
        
        // Prepare questions data for JavaScript
        $questionsData = $questions->map(function($q) {
            // Reload the question to ensure we have the latest data including options
            // This ensures that if the question was updated in bank soal, we get the latest version
            $freshQuestion = Question::find($q->id);
            if (!$freshQuestion) {
                return null;
            }
            
            // Ensure options is properly formatted from fresh question
            $options = $freshQuestion->options ?? null;
            
            // For pilihan_ganda, ensure options is an associative array/object
            if ($freshQuestion->question_type === 'pilihan_ganda') {
                    // Check if options is a string (concatenated) and needs parsing (limit to A-D only)
                    if (is_string($options)) {
                        // Parse concatenated string like "A. Astronomis B. Geografis C) Geologis D: Ekonomis" (supports ., ), :, -, or spaces)
                        // Limit to A-D only
                        $parsedOptions = [];
                        $pattern = '/([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/i';
                        if (preg_match_all($pattern, $options, $matches, PREG_SET_ORDER)) {
                            foreach ($matches as $match) {
                                $key = strtoupper(trim($match[1]));
                                $value = trim($match[2]);
                                // Only process A, B, C, D and limit to 4 options
                                if (!empty($value) && in_array($key, ['A', 'B', 'C', 'D']) && count($parsedOptions) < 4) {
                                    $parsedOptions[$key] = $value;
                                }
                            }
                        }
                        $options = !empty($parsedOptions) ? $parsedOptions : [];
                } elseif (empty($options) || !is_array($options)) {
                    $options = [];
                } else {
                    // Ensure options is an associative array with A, B, C, D keys (limit to D only)
                    // Convert indexed array to associative if needed
                    $optionKeys = array_keys($options);
                    if (!empty($optionKeys) && is_numeric($optionKeys[0])) {
                        // It's an indexed array, convert to associative (limit to first 4 items only)
                        $newOptions = [];
                        $keys = ['A', 'B', 'C', 'D'];
                        $optionsArray = is_array($options) ? array_values($options) : [];
                        foreach (array_slice($optionsArray, 0, 4) as $index => $value) {
                            if (isset($keys[$index]) && !empty($value)) {
                                $value = is_string($value) ? trim($value) : $value;
                                // Check if value is concatenated string (limit to A-D only)
                                if (is_string($value) && preg_match('/^[A-D](\s*[\.\)\:\-])?/i', $value)) {
                                    // Value might be concatenated, try to parse (supports ., ), :, -, or spaces)
                                    $pattern = '/([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/i';
                                    if (preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
                                        foreach ($matches as $match) {
                                            $key = strtoupper(trim($match[1]));
                                            $val = trim($match[2]);
                                            // Only process A, B, C, D and limit to 4 options
                                            if (!empty($val) && in_array($key, ['A', 'B', 'C', 'D']) && count($newOptions) < 4) {
                                                $newOptions[$key] = $val;
                                            }
                                        }
                                    }
                                } else {
                                    $newOptions[$keys[$index]] = $value;
                                }
                            }
                        }
                        $options = $newOptions;
                    }
                    
                    // Clean up options - ensure all A-D keys exist (limit to A-D only)
                    $cleanedOptions = [];
                    foreach (['A', 'B', 'C', 'D'] as $key) {
                        if (isset($options[$key])) {
                            $value = is_string($options[$key]) ? trim($options[$key]) : $options[$key];
                            // Check if value contains concatenated options (limit to A-D only)
                            if (is_string($value) && preg_match('/[A-D](\s*[\.\)\:\-])?/i', $value)) {
                                $pattern = '/([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/i';
                                if (preg_match_all($pattern, $value, $matches, PREG_SET_ORDER)) {
                                    // Parse concatenated string
                                    foreach ($matches as $match) {
                                        $optionKey = strtoupper(trim($match[1]));
                                        $optionValue = trim($match[2]);
                                        // Only process A, B, C, D and limit to 4 options
                                        if (!empty($optionValue) && in_array($optionKey, ['A', 'B', 'C', 'D']) && !isset($cleanedOptions[$optionKey]) && count($cleanedOptions) < 4) {
                                            $cleanedOptions[$optionKey] = $optionValue;
                                        }
                                    }
                                }
                            }
                            // Always add the option even if empty (let JavaScript handle display logic)
                            if (!isset($cleanedOptions[$key])) {
                                $cleanedOptions[$key] = !empty($value) && $value !== 'null' && $value !== 'undefined' ? $value : '';
                            }
                        } else {
                            // Ensure all keys A-D exist, even if empty
                            $cleanedOptions[$key] = '';
                        }
                    }
                    // Always return all A-D options (even if empty) so JavaScript can display them all
                    $options = $cleanedOptions;
                }
            } else {
                // For essay, options should be null
                $options = null;
            }
            
            return [
                'id' => $freshQuestion->id,
                'text' => $freshQuestion->question_text,
                'type' => $freshQuestion->question_type,
                'options' => $options,
                'correct_answer' => $freshQuestion->correct_answer,
            ];
        })->filter()->values()->toArray(); // Filter out null values
        
        return view('student.exam', compact('exam', 'questions', 'examResult', 'answers', 'questionsData'));
    }
    
    public function saveAnswer(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'nullable|string',
        ]);
        
        // Get or create exam result
        $examResult = ExamResult::firstOrCreate(
            [
                'exam_id' => $exam->id,
                'student_id' => $user->id,
            ],
            [
                'answers' => [],
                'status' => 'ongoing',
                'started_at' => Carbon::now(),
            ]
        );
        
        // Update answers
        $answers = $examResult->answers ?? [];
        $answers[$request->question_id] = $request->answer;
        $examResult->answers = $answers;
        $examResult->save();
        
        return response()->json(['success' => true]);
    }
    
    private function autoSubmit(Exam $exam, ExamResult $examResult)
    {
        // Calculate score
        $answers = $examResult->answers ?? [];
        $score = 0;
        $totalPoints = 0;
        
        foreach ($exam->questions as $question) {
            $totalPoints += $question->points ?? 1;
            $studentAnswer = $answers[$question->id] ?? null;
            
            if ($studentAnswer && $question->correct_answer) {
                // For multiple choice, check if answer matches
                if ($question->question_type === 'pilihan_ganda') {
                    if (trim(strtolower($studentAnswer)) === trim(strtolower($question->correct_answer))) {
                        $score += $question->points ?? 1;
                    }
                } else {
                    // For essay, you might want to mark manually later
                    // For now, we'll just check if answer exists
                    if (!empty($studentAnswer)) {
                        $score += $question->points ?? 1;
                    }
                }
            }
        }
        
        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
        
        // Calculate time taken
        $timeTaken = null;
        if ($examResult->started_at) {
            $durationSeconds = $exam->duration * 60;
            $timeTaken = $durationSeconds; // Use full duration for auto-submit
        }
        
        // Update exam result
        $examResult->score = $score;
        $examResult->total_points = $totalPoints;
        $examResult->percentage = round($percentage, 2);
        $examResult->status = 'completed';
        $examResult->submitted_at = Carbon::now();
        $examResult->time_taken = $timeTaken;
        $examResult->save();
    }

    public function submit(Request $request, Exam $exam)
    {
        $user = Auth::user();
        
        $examResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();
        
        if (!$examResult) {
            return redirect()->route('siswa.ujian-aktif')->with('error', 'Tidak ada data ujian.');
        }
        
        if ($examResult->status === 'completed') {
            return redirect()->route('siswa.riwayat')->with('info', 'Ujian ini sudah diselesaikan.');
        }
        
        // Check if time has expired
        if ($examResult->started_at) {
            $durationSeconds = $exam->duration * 60;
            $endTime = Carbon::parse($examResult->started_at)->addSeconds($durationSeconds);
            
            if (Carbon::now()->gte($endTime)) {
                // Time has expired, auto-submit
                $this->autoSubmit($exam, $examResult);
                return redirect()->route('siswa.exam.result', $exam->id);
            }
        }
        
        // Calculate score
        $answers = $examResult->answers ?? [];
        $score = 0;
        $totalPoints = 0;
        
        foreach ($exam->questions as $question) {
            $totalPoints += $question->points ?? 1;
            $studentAnswer = $answers[$question->id] ?? null;
            
            if ($studentAnswer && $question->correct_answer) {
                // For multiple choice, check if answer matches
                if ($question->question_type === 'pilihan_ganda') {
                    if (trim(strtolower($studentAnswer)) === trim(strtolower($question->correct_answer))) {
                        $score += $question->points ?? 1;
                    }
                } else {
                    // For essay, you might want to mark manually later
                    // For now, we'll just check if answer exists
                    if (!empty($studentAnswer)) {
                        $score += $question->points ?? 1;
                    }
                }
            }
        }
        
        $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
        
        // Calculate time taken
        $timeTaken = null;
        if ($examResult->started_at) {
            $timeTaken = Carbon::now()->diffInSeconds($examResult->started_at);
        }
        
        // Update exam result
        $examResult->score = $score;
        $examResult->total_points = $totalPoints;
        $examResult->percentage = round($percentage, 2);
        $examResult->status = 'completed';
        $examResult->submitted_at = Carbon::now();
        $examResult->time_taken = $timeTaken;
        $examResult->save();
        
        return redirect()->route('siswa.exam.result', $exam->id);
    }

    public function result(Exam $exam)
    {
        $user = Auth::user();
        
        // Eager load relationships to avoid N+1 queries
        $exam->load('subject', 'classRelation', 'questions');
        
        // Get exam result with proper relationships
        $examResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $user->id)
            ->first();
        
        if (!$examResult) {
            return redirect()->route('siswa.ujian-aktif')->with('error', 'Tidak ada data ujian.');
        }
        
        // Ensure exam result belongs to the authenticated student
        if ($examResult->student_id !== $user->id) {
            abort(403, 'Anda tidak memiliki izin untuk melihat hasil ujian ini.');
        }
        
        if ($examResult->status !== 'completed') {
            return redirect()->route('siswa.exam', $exam->id)->with('info', 'Ujian belum selesai. Silakan selesaikan ujian terlebih dahulu.');
        }
        
        // Ensure score and percentage are set (fallback if somehow missing)
        if (is_null($examResult->score) || is_null($examResult->percentage) || $examResult->total_points == 0) {
            // Recalculate if missing
            $answers = $examResult->answers ?? [];
            $score = 0;
            $totalPoints = 0;
            
            // Load questions if not already loaded
            if (!$exam->relationLoaded('questions')) {
                $exam->load('questions');
            }
            
            foreach ($exam->questions as $question) {
                $totalPoints += $question->points ?? 1;
                $studentAnswer = $answers[$question->id] ?? null;
                
                if ($studentAnswer && $question->correct_answer) {
                    if ($question->question_type === 'pilihan_ganda') {
                        if (trim(strtolower($studentAnswer)) === trim(strtolower($question->correct_answer))) {
                            $score += $question->points ?? 1;
                        }
                    }
                }
            }
            
            $percentage = $totalPoints > 0 ? ($score / $totalPoints) * 100 : 0;
            
            $examResult->score = $score;
            $examResult->total_points = $totalPoints;
            $examResult->percentage = round($percentage, 2);
            $examResult->save();
        }
        
        return view('student.exam_result', compact('exam', 'examResult'));
    }
}

