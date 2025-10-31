<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Imports\QuestionsImport;
use App\Services\DocQuestionParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

class BankSoalController extends Controller
{
    public function index(Request $request)
    {
        // Get subjects that have questions created by this teacher
        $subjectsWithQuestions = Subject::whereHas('questions', function($q) {
                $q->where('created_by', Auth::id());
            })
            ->withCount(['questions' => function($q) {
                $q->where('created_by', Auth::id());
            }])
            ->orderBy('name')
            ->get();
        
        return view('teacher.bank', compact('subjectsWithQuestions'));
    }
    
    public function detail(Request $request, Subject $subject)
    {
        // Check if subject has questions created by this teacher
        $hasQuestions = Question::where('subject_id', $subject->id)
            ->where('created_by', Auth::id())
            ->exists();
        
        if (!$hasQuestions) {
            return redirect()->route('guru.bank')->with('error', 'Mata pelajaran tidak memiliki soal.');
        }
        
        $query = Question::with('subject', 'creator')
            ->where('subject_id', $subject->id)
            ->where('created_by', Auth::id());

        // Filter by level
        if ($request->has('level') && $request->level !== 'all' && $request->level) {
            $query->where('level', $request->level);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                  ->orWhere('topic', 'like', "%{$search}%");
            });
        }

        $questions = $query->orderBy('created_at', 'desc')->paginate(15);
        $subjects = Subject::orderBy('name')->get();
        
        return view('teacher.bank_detail', compact('questions', 'subject', 'subjects'));
    }

    public function store(Request $request)
    {
        $rules = [
            'subject_id' => ['required', 'exists:subjects,id'],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:pilihan_ganda,essay'],
            'correct_answer' => ['required', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'in:mudah,sedang,sulit'],
            'points' => ['nullable', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
        ];

        if ($request->question_type === 'pilihan_ganda') {
            $rules['options'] = ['required', 'array'];
            $rules['options.A'] = ['required', 'string'];
            $rules['options.B'] = ['required', 'string'];
            $rules['options.C'] = ['required', 'string'];
            $rules['options.D'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $options = null;
        if ($request->question_type === 'pilihan_ganda' && isset($validated['options'])) {
            $options = $validated['options'];
        }

        $question = Question::create([
            'subject_id' => $validated['subject_id'],
            'created_by' => Auth::id(),
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'level' => $validated['level'] ?? null,
            'topic' => $validated['topic'] ?? null,
            'difficulty' => $validated['difficulty'],
            'points' => $validated['points'] ?? 1,
            'explanation' => $validated['explanation'] ?? null,
        ]);

        // Redirect to detail page if coming from detail, otherwise to index
        $redirectTo = $request->input('redirect_to', 'bank');
        if ($redirectTo === 'detail' || $request->has('subject_id')) {
            return redirect()->route('guru.bank.detail', $validated['subject_id'])->with('success', 'Soal berhasil ditambahkan.');
        }
        
        return redirect()->route('guru.bank')->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, Question $question)
    {
        $rules = [
            'subject_id' => ['required', 'exists:subjects,id'],
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:pilihan_ganda,essay'],
            'correct_answer' => ['required', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'topic' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['required', 'in:mudah,sedang,sulit'],
            'points' => ['nullable', 'integer', 'min:1'],
            'explanation' => ['nullable', 'string'],
        ];

        if ($request->question_type === 'pilihan_ganda') {
            $rules['options'] = ['required', 'array'];
            $rules['options.A'] = ['required', 'string'];
            $rules['options.B'] = ['required', 'string'];
            $rules['options.C'] = ['required', 'string'];
            $rules['options.D'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        $options = null;
        if ($request->question_type === 'pilihan_ganda' && isset($validated['options'])) {
            $options = $validated['options'];
        }

        $subjectId = $question->subject_id;
        $question->update([
            'subject_id' => $validated['subject_id'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $options,
            'correct_answer' => $validated['correct_answer'],
            'level' => $validated['level'] ?? null,
            'topic' => $validated['topic'] ?? null,
            'difficulty' => $validated['difficulty'],
            'points' => $validated['points'] ?? 1,
            'explanation' => $validated['explanation'] ?? null,
        ]);

        // Redirect to detail page if coming from detail, otherwise to index
        $redirectTo = $request->input('redirect_to', 'bank');
        if ($redirectTo === 'detail' || $request->has('subject_id')) {
            return redirect()->route('guru.bank.detail', $validated['subject_id'])->with('success', 'Soal berhasil diperbarui.');
        }
        
        return redirect()->route('guru.bank')->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $subjectId = $question->subject_id;
        $question->delete();

        // Redirect to detail page if subject has questions, otherwise to index
        $hasQuestions = Question::where('subject_id', $subjectId)
            ->where('created_by', Auth::id())
            ->exists();
        
        if ($hasQuestions) {
            return redirect()->route('guru.bank.detail', $subjectId)->with('success', 'Soal berhasil dihapus.');
        }
        
        return redirect()->route('guru.bank')->with('success', 'Soal berhasil dihapus.');
    }

    public function deleteAllBySubject(Subject $subject)
    {
        // Check if teacher has questions for this subject
        $questionsCount = Question::where('subject_id', $subject->id)
            ->where('created_by', Auth::id())
            ->count();
        
        if ($questionsCount == 0) {
            return redirect()->route('guru.bank')->with('error', 'Tidak ada soal yang dapat dihapus untuk mata pelajaran ini.');
        }
        
        // Delete all questions from this subject created by current teacher
        Question::where('subject_id', $subject->id)
            ->where('created_by', Auth::id())
            ->delete();
        
        return redirect()->route('guru.bank')->with('success', "Semua soal ({$questionsCount} soal) dari mata pelajaran {$subject->name} berhasil dihapus.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls,doc,docx', 'max:5120'], // Max 5MB
        ]);

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());
            $imported = 0;
            $errors = [];
            $message = '';

            // Handle DOC/DOCX files
            if (in_array($extension, ['doc', 'docx'])) {
                // Validate subject_id for DOC files
                $request->validate([
                    'subject_id' => ['required', 'exists:subjects,id'],
                ]);
                // Store file temporarily
                $tempPath = $file->store('temp', 'local');
                $fullPath = Storage::path($tempPath);
                
                try {
                    $parser = new DocQuestionParser();
                    $questions = $parser->parse($fullPath);
                    
                    $subjectId = $request->subject_id;
                    
                    foreach ($questions as $questionData) {
                        // Validate question data
                        if (empty($questionData['question_text'])) {
                            continue;
                        }
                        
                        // For multiple choice, ensure options and correct answer exist
                        if ($questionData['question_type'] === 'pilihan_ganda') {
                            if (empty($questionData['options']) || empty($questionData['correct_answer'])) {
                                $errors[] = "Soal dengan pertanyaan: " . substr($questionData['question_text'], 0, 50) . "... tidak memiliki pilihan lengkap";
                                continue;
                            }
                        }
                        
                        try {
                            Question::create([
                                'subject_id' => $subjectId,
                                'created_by' => Auth::id(),
                                'question_text' => $questionData['question_text'],
                                'question_type' => $questionData['question_type'] ?? 'pilihan_ganda',
                                'options' => $questionData['options'] ?? null,
                                'correct_answer' => $questionData['correct_answer'] ?? '',
                                'level' => $questionData['level'] ?? null,
                                'topic' => $questionData['topic'] ?? null,
                                'difficulty' => $questionData['difficulty'] ?? 'sedang',
                                'points' => $questionData['points'] ?? 1,
                                'explanation' => $questionData['explanation'] ?? null,
                            ]);
                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = "Gagal menyimpan soal: " . substr($questionData['question_text'], 0, 50) . "... - " . $e->getMessage();
                        }
                    }
                    
                    // Clean up temp file
                    Storage::delete($tempPath);
                } catch (\Exception $e) {
                    Storage::delete($tempPath);
                    throw $e;
                }
                
                if ($imported > 0) {
                    $message = "Berhasil mengimpor {$imported} soal dari file DOC.";
                    if (!empty($errors)) {
                        $message .= " " . count($errors) . " soal dilewati karena error.";
                    }
                } else {
                    $message = "Tidak ada soal yang berhasil diimpor dari file DOC. ";
                    if (!empty($errors)) {
                        $message .= count($errors) . " soal dilewati karena error.";
                    }
                }
                
                // Add detailed errors if available (max 10 errors)
                if (!empty($errors) && count($errors) > 0) {
                    $errorDetails = array_slice($errors, 0, 10);
                    $message .= "\n\nError detail:\n" . implode("\n", $errorDetails);
                    if (count($errors) > 10) {
                        $message .= "\n... dan " . (count($errors) - 10) . " error lainnya.";
                    }
                }
            } 
            // Handle Excel/CSV files
            else {
                $import = new QuestionsImport();
                
                try {
                    Excel::import($import, $file);
                } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                    $failures = $e->failures();
                    foreach ($failures as $failure) {
                        $row = $failure->row();
                        $errors[] = "Baris {$row}: " . implode(', ', $failure->errors());
                    }
                }

                $imported = $import->getImported();
                $skipped = $import->getSkipped();
                $errors = array_merge($errors, $import->getErrors());
                
                if ($imported > 0) {
                    $message = "Berhasil mengimpor {$imported} soal.";
                    if ($skipped > 0 || !empty($errors)) {
                        $message .= " {$skipped} baris dilewati karena error.";
                    }
                } else {
                    $message = "Tidak ada soal yang berhasil diimpor. ";
                    if ($skipped > 0 || !empty($errors)) {
                        $message .= "Semua baris ({$skipped}) dilewati karena error.";
                    }
                }
                
                // Add detailed errors if available (max 10 errors)
                if (!empty($errors) && count($errors) > 0) {
                    $errorDetails = array_slice($errors, 0, 10);
                    $message .= "\n\nError detail:\n" . implode("\n", $errorDetails);
                    if (count($errors) > 10) {
                        $message .= "\n... dan " . (count($errors) - 10) . " error lainnya.";
                    }
                }
            }

            return redirect()->route('guru.bank')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('guru.bank')->with('error', 'Gagal mengimpor soal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Mata Pelajaran',
            'Pertanyaan',
            'Tipe Soal',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Jawaban Benar',
            'Tingkat',
            'Topik',
            'Tingkat Kesulitan',
            'Poin',
            'Penjelasan'
        ];

        $examples = [
            [
                'Matematika',
                'Berapa hasil dari 2 + 2?',
                'pilihan_ganda',
                '3',
                '4',
                '5',
                '6',
                'B',
                'X',
                'Aritmatika',
                'mudah',
                '1',
                '2 + 2 = 4'
            ],
            [
                'Bahasa Indonesia',
                'Jelaskan makna puisi berikut...',
                'essay',
                '',
                '',
                '',
                '',
                'Jawaban yang benar adalah...',
                'XI',
                'Puisi',
                'sedang',
                '5',
                'Penjelasan jawaban'
            ]
        ];

        $data = array_merge([$headers], $examples);

        $filename = 'template_import_soal_' . date('Y-m-d') . '.csv';
        
        // Use Laravel's response to handle CSV properly
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadDocTemplate()
    {
        $phpWord = new PhpWord();
        
        // Set document properties
        $properties = $phpWord->getDocInfo();
        $appName = \App\Models\AppSetting::getValue('app_name', 'Aplikasi Ujian Sekolah');
        $properties->setCreator($appName);
        $properties->setTitle('Template Import Soal - Format DOC');
        $properties->setDescription('Template untuk import soal dalam format Microsoft Word (DOC/DOCX)');
        
        // Add section
        $section = $phpWord->addSection([
            'marginTop' => 1440,    // 1 inch = 1440 twips
            'marginBottom' => 1440,
            'marginLeft' => 1440,
            'marginRight' => 1440,
        ]);
        
        // Title
        $section->addText('TEMPLATE IMPORT SOAL - FORMAT DOC/DOCX', [
            'bold' => true,
            'size' => 14,
        ], [
            'alignment' => 'center',
            'spaceAfter' => 240,
        ]);
        
        // Instructions
        $section->addText('Petunjuk:', [
            'bold' => true,
            'size' => 12,
        ], [
            'spaceBefore' => 240,
            'spaceAfter' => 120,
        ]);
        
        $instructions = [
            '1. Nomor soal diikuti titik dan spasi (contoh: 1. )',
            '2. Pertanyaan ditulis setelah nomor soal (bisa beberapa baris)',
            '3. Pilihan jawaban A, B, C, D ditulis di baris terpisah dengan format: A. [jawaban]',
            '4. Baris kosong antara pertanyaan dan pilihan diperbolehkan',
            '5. Jawaban benar ditulis dengan format: Jawaban: X. [teks jawaban]',
            '6. Antara soal dapat dipisahkan dengan garis (opsional)',
        ];
        
        foreach ($instructions as $instruction) {
            $section->addText($instruction, [
                'size' => 11,
            ], [
                'indentation' => ['firstLine' => 360],
                'spaceAfter' => 60,
            ]);
        }
        
        $section->addText('', [], ['spaceAfter' => 240]);
        
        // Example questions
        $section->addText('Contoh Format Soal:', [
            'bold' => true,
            'size' => 12,
        ], [
            'spaceBefore' => 240,
            'spaceAfter' => 120,
        ]);
        
        // Example 1
        $section->addText('1. Indonesia terletak di antara dua benua dan dua samudra. Hal ini menyebabkan Indonesia disebut sebagai negara yang memiliki letak...', [
            'size' => 11,
        ], [
            'spaceAfter' => 120,
        ]);
        
        $section->addText('A. Astronomis', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('B. Geografis', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('C. Geologis', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('D. Ekonomis', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 120,
        ]);
        
        $section->addText('Jawaban: B. Geografis', [
            'bold' => true,
            'size' => 11,
        ], [
            'spaceAfter' => 240,
        ]);
        
        // Separator
        $section->addText('________________________________________', [
            'size' => 10,
            'color' => 'CCCCCC',
        ], [
            'alignment' => 'center',
            'spaceBefore' => 120,
            'spaceAfter' => 240,
        ]);
        
        // Example 2
        $section->addText('2. Benua yang berada di sebelah barat Indonesia adalah...', [
            'size' => 11,
        ], [
            'spaceAfter' => 120,
        ]);
        
        $section->addText('A. Asia', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('B. Australia', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('C. Amerika', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 60,
        ]);
        
        $section->addText('D. Afrika', [
            'size' => 11,
        ], [
            'indentation' => ['left' => 360],
            'spaceAfter' => 120,
        ]);
        
        $section->addText('Jawaban: D. Afrika', [
            'bold' => true,
            'size' => 11,
        ], [
            'spaceAfter' => 240,
        ]);
        
        // Save file
        $filename = 'template_import_soal_doc_' . date('Y-m-d') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'template_');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
