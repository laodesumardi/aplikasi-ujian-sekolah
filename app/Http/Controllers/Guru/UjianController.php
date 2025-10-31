<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Kelas;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UjianController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with('subject', 'classRelation', 'creator');

        // Filter by subject
        if ($request->has('subject') && $request->subject !== 'all' && $request->subject) {
            $query->where('subject_id', $request->subject);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all' && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhereHas('subject', function($subQuery) use ($search) {
                      $subQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('classRelation', function($classQuery) use ($search) {
                      $classQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $exams = $query->with('questions')->orderBy('exam_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        // Sync question stats for all exams (in case they're out of sync)
        foreach ($exams->items() as $exam) {
            if ($exam->total_questions != $exam->questions()->count()) {
                $exam->syncQuestionStats();
            }
        }
        
        $subjects = Subject::orderBy('name')->get();
        $classes = Kelas::orderBy('level')->orderBy('name')->get();
        
        return view('teacher.exams', compact('exams', 'subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1'],
        ]);

        // Ensure at least one of class_id or kelas is provided
        if (empty($validated['class_id']) && empty($validated['kelas'])) {
            return redirect()->route('guru.exams')->with('error', 'Pilih kelas atau masukkan nama kelas.');
        }

        $exam = Exam::create([
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'exam_date' => $validated['exam_date'],
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'],
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        // Automatically attach all questions from the selected subject
        $questions = Question::where('subject_id', $validated['subject_id'])
            ->where('created_by', Auth::id())
            ->orderBy('id')
            ->get();

        $questionsCount = 0;
        if ($questions->count() > 0) {
            $order = 1;
            foreach ($questions as $question) {
                $exam->questions()->attach($question->id, ['order' => $order++]);
            }
            $questionsCount = $questions->count();
        }
        
        // Always sync question stats after attaching
        $exam->syncQuestionStats();

        $message = 'Jadwal ujian berhasil dibuat.';
        if ($questionsCount > 0) {
            $message .= " {$questionsCount} soal dari mata pelajaran ini otomatis ditambahkan ke ujian.";
        } else {
            $message .= " Perhatian: Tidak ada soal untuk mata pelajaran ini. Silakan tambahkan soal di Bank Soal terlebih dahulu.";
        }

        return redirect()->route('guru.exams')->with('success', $message);
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'class_id' => ['nullable', 'exists:classes,id'],
            'kelas' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'exam_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:draft,scheduled,active,completed,cancelled'],
            'question_ids' => ['nullable', 'array'],
            'question_ids.*' => ['exists:questions,id'],
        ]);

        // Ensure at least one of class_id or kelas is provided
        if (empty($validated['class_id']) && empty($validated['kelas'])) {
            return redirect()->route('guru.exams')->with('error', 'Pilih kelas atau masukkan nama kelas.');
        }

        $oldSubjectId = $exam->subject_id;
        $exam->update([
            'subject_id' => $validated['subject_id'],
            'class_id' => $validated['class_id'] ?? null,
            'kelas' => $validated['kelas'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'exam_date' => $validated['exam_date'],
            'start_time' => $validated['start_time'],
            'duration' => $validated['duration'],
            'status' => $validated['status'],
        ]);

        // Update questions if provided
        if (isset($validated['question_ids'])) {
            $exam->questions()->detach();
            $order = 1;
            foreach ($validated['question_ids'] as $questionId) {
                $question = Question::find($questionId);
                if ($question) {
                    $exam->questions()->attach($questionId, ['order' => $order++]);
                }
            }
        } else {
            // If no question_ids provided, sync with all questions from the selected subject
            // This ensures that newly added questions are automatically included
            // Only sync if subject changed or if exam has no questions yet
            if ($oldSubjectId != $validated['subject_id'] || $exam->questions()->count() == 0) {
                $exam->questions()->detach();
                
                // Get all questions from the selected subject by this teacher
                $questions = Question::where('subject_id', $validated['subject_id'])
                    ->where('created_by', Auth::id())
                    ->orderBy('id')
                    ->get();

                if ($questions->count() > 0) {
                    $order = 1;
                    foreach ($questions as $question) {
                        $exam->questions()->attach($question->id, ['order' => $order++]);
                    }
                }
            }
        }
        
        // Always sync question stats after updating
        $exam->syncQuestionStats();

        return redirect()->route('guru.exams')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(Exam $exam)
    {
        $exam->questions()->detach();
        $exam->delete();

        return redirect()->route('guru.exams')->with('success', 'Jadwal ujian berhasil dihapus.');
    }

    public function getQuestions(Request $request)
    {
        $subjectId = $request->subject_id;
        
        if (!$subjectId) {
            return response()->json(['questions' => []]);
        }

        $questions = Question::where('subject_id', $subjectId)
            ->where('created_by', Auth::id())
            ->select('id', 'question_text', 'question_type', 'points', 'difficulty')
            ->orderBy('id')
            ->get()
            ->map(function($q) {
                return [
                    'id' => $q->id,
                    'text' => Str::limit($q->question_text, 100),
                    'type' => $q->question_type,
                    'points' => $q->points,
                    'difficulty' => $q->difficulty,
                ];
            });

        return response()->json(['questions' => $questions]);
    }

    /**
     * Sinkronkan soal ujian dengan Bank Soal berdasarkan mata pelajaran
     * dan pemilik soal (guru pembuat ujian).
     */
    public function syncQuestions(Request $request, Exam $exam)
    {
        // Hanya pembuat ujian atau admin yang boleh sinkronisasi
        $user = Auth::user();
        if ($exam->created_by !== $user->id && ($user->role ?? 'guru') !== 'admin') {
            return redirect()->route('guru.exams')->with('error', 'Anda tidak memiliki izin untuk sinkronisasi ujian ini.');
        }

        // Ambil semua soal dari mata pelajaran ujian yang dibuat oleh pembuat ujian
        $questions = Question::where('subject_id', $exam->subject_id)
            ->where('created_by', $exam->created_by)
            ->orderBy('id')
            ->get();

        // Detach lalu attach ulang agar urutan segar
        $exam->questions()->detach();
        $count = 0;
        if ($questions->count() > 0) {
            $order = 1;
            foreach ($questions as $q) {
                $exam->questions()->attach($q->id, ['order' => $order++]);
            }
            $count = $questions->count();
        }

        // Update metrik total soal/poin
        $exam->syncQuestionStats();

        $message = 'Soal ujian berhasil disinkronkan dengan Bank Soal.';
        if ($count > 0) {
            $message .= " {$count} soal ditautkan sesuai mata pelajaran dan pemilik soal.";
        } else {
            $message .= ' Tidak ada soal pada mata pelajaran ini di Bank Soal Anda.';
        }

        return redirect()->route('guru.exams')->with('success', $message);
    }
}
