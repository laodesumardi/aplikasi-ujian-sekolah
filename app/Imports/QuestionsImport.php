<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;

class QuestionsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;

    public function model(array $row)
    {
        // Normalize keys (handle case-insensitive and spaces)
        $row = array_change_key_case($row, CASE_LOWER);
        $normalized = [];
        foreach ($row as $key => $value) {
            $key = str_replace([' ', '_', '-'], '', $key);
            $normalized[$key] = $value;
        }

        // Find or get subject by name/code
        $subjectName = $normalized['matapelajaran'] ?? $normalized['subject'] ?? $normalized['mata'] ?? null;
        $subject = null;
        
        if ($subjectName) {
            $subjectName = trim($subjectName);
            $subject = Subject::where('name', $subjectName)
                ->orWhere('code', $subjectName)
                ->first();
        }

        // If subject not found, skip this row
        if (!$subject) {
            $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Mata pelajaran '{$subjectName}' tidak ditemukan di sistem.";
            $this->skipped++;
            return null; // Will be handled by validation errors
        }

        // Get question text
        $questionText = $normalized['pertanyaan'] ?? $normalized['question'] ?? '';
        if (empty($questionText)) {
            $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Pertanyaan kosong.";
            $this->skipped++;
            return null;
        }

        // Get question type
        $questionType = strtolower($normalized['tipesoal'] ?? $normalized['tipe'] ?? $normalized['questiontype'] ?? 'pilihan_ganda');
        $questionType = in_array($questionType, ['pilihan_ganda', 'essay']) ? $questionType : 'pilihan_ganda';

        // Prepare options for multiple choice
        $options = null;
        if ($questionType === 'pilihan_ganda') {
            // Get raw option values - try multiple possible column names
            // Handle case-insensitive and various formats (opsi a, opsi_a, opsi-a, opsia, optiona, a)
            $optionA = '';
            $optionB = '';
            $optionC = '';
            $optionD = '';
            
            // Try all possible column name variations for each option
            $possibleKeysA = ['opsia', 'optiona', 'a', 'opsia', 'opsi a', 'opsi_a', 'opsi-a', 'jawabana'];
            $possibleKeysB = ['opsib', 'optionb', 'b', 'opsib', 'opsi b', 'opsi_b', 'opsi-b', 'jawabanb'];
            $possibleKeysC = ['opsic', 'optionc', 'c', 'opsic', 'opsi c', 'opsi_c', 'opsi-c', 'jawabanc'];
            $possibleKeysD = ['opsid', 'optiond', 'd', 'opsid', 'opsi d', 'opsi_d', 'opsi-d', 'jawaband'];
            
            foreach ($possibleKeysA as $key) {
                if (isset($normalized[$key]) && empty($optionA)) {
                    $val = $normalized[$key];
                    $optionA = is_null($val) ? '' : trim((string)$val);
                }
            }
            
            foreach ($possibleKeysB as $key) {
                if (isset($normalized[$key]) && empty($optionB)) {
                    $val = $normalized[$key];
                    $optionB = is_null($val) ? '' : trim((string)$val);
                }
            }
            
            foreach ($possibleKeysC as $key) {
                if (isset($normalized[$key]) && empty($optionC)) {
                    $val = $normalized[$key];
                    $optionC = is_null($val) ? '' : trim((string)$val);
                }
            }
            
            foreach ($possibleKeysD as $key) {
                if (isset($normalized[$key]) && empty($optionD)) {
                    $val = $normalized[$key];
                    $optionD = is_null($val) ? '' : trim((string)$val);
                }
            }
            
            // Debug: Log what we found
            \Log::debug('Import options - Found values:', [
                'A' => $optionA,
                'B' => $optionB,
                'C' => $optionC,
                'D' => $optionD,
                'available_keys' => array_keys($normalized)
            ]);
            
            // Check if option A contains concatenated options (like "DeforestasiB. ReboisasiC. IndustrialisasiD. Urbanisasi")
            // If option A has content but B, C, D are empty, try to parse option A as concatenated string
            if (!empty($optionA) && empty($optionB) && empty($optionC) && empty($optionD)) {
                // Try to parse concatenated string (limit to A-D only)
                $pattern = '/([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/iu';
                if (preg_match_all($pattern, $optionA, $matches, PREG_SET_ORDER)) {
                    $parsedOptions = [];
                    foreach ($matches as $match) {
                        $key = strtoupper(trim($match[1]));
                        $value = trim($match[2]);
                        // Only process A, B, C, D
                        if (!empty($value) && in_array($key, ['A', 'B', 'C', 'D']) && !isset($parsedOptions[$key])) {
                            $parsedOptions[$key] = $value;
                        }
                    }
                    // If we successfully parsed multiple options (at least 2), use them
                    if (count($parsedOptions) >= 2) {
                        // Ensure all A-D keys exist
                        foreach (['A', 'B', 'C', 'D'] as $key) {
                            if (!isset($parsedOptions[$key])) {
                                $parsedOptions[$key] = '';
                            }
                        }
                        $options = $parsedOptions;
                        \Log::debug('Import options - Parsed from concatenated string:', $options);
                    } else {
                        // Otherwise, use original values
                        $options = [
                            'A' => $optionA,
                            'B' => $optionB,
                            'C' => $optionC,
                            'D' => $optionD,
                        ];
                    }
                } else {
                    // If parsing failed, use original values
                    $options = [
                        'A' => $optionA,
                        'B' => $optionB,
                        'C' => $optionC,
                        'D' => $optionD,
                    ];
                }
            } else {
                // Normal case: each option in separate column
                // Store all original values first
                $allOptions = [
                    'A' => $optionA,
                    'B' => $optionB,
                    'C' => $optionC,
                    'D' => $optionD,
                ];
                
                // Initialize options array
                $options = [];
                
                // First pass: check each option for concatenated values and parse them
                foreach ($allOptions as $key => $value) {
                    if (!empty($value)) {
                        // Check if this value contains concatenated options (limit to A-D only)
                        $pattern = '/([A-D])\s*[\.\)\:\-]?\s*([^A-E]+?)(?=\s*[A-D]\s*[\.\)\:\-]?|$)/iu';
                        if (preg_match_all($pattern, $value, $matches, PREG_SET_ORDER) && count($matches) > 1) {
                            // This value contains multiple options, parse them all
                            foreach ($matches as $match) {
                                $optKey = strtoupper(trim($match[1]));
                                $optValue = trim($match[2]);
                                // Only process A, B, C, D and ensure we don't overwrite existing values
                                if (!empty($optValue) && in_array($optKey, ['A', 'B', 'C', 'D']) && !isset($options[$optKey])) {
                                    $options[$optKey] = $optValue;
                                }
                            }
                        } else {
                            // Single option value - always assign to the column's key (A, B, C, or D)
                            // Even if it looks empty, preserve the original value
                            if (!isset($options[$key]) || empty($options[$key])) {
                                $options[$key] = $value;
                            }
                        }
                    }
                }
                
                // Second pass: assign original values to their column keys if not yet set
                // This ensures we don't lose options that weren't parsed from concatenated strings
                foreach ($allOptions as $key => $originalValue) {
                    if (!isset($options[$key]) || (empty($options[$key]) && !empty($originalValue))) {
                        // If this key is not set or empty, but original value exists, use it
                        $options[$key] = $originalValue;
                    }
                }
                
                // Third pass: ensure all A-D keys exist (even if empty)
                // This ensures we always have A, B, C, D structure
                foreach (['A', 'B', 'C', 'D'] as $key) {
                    if (!isset($options[$key])) {
                        $options[$key] = '';
                    } else {
                        // Trim the value
                        $options[$key] = trim((string)$options[$key]);
                    }
                }
                
                // Debug: Log final options
                \Log::debug('Import options - Final parsed:', $options);
            }
            
            // Validate options - at least one should be filled
            if (empty(array_filter($options))) {
                $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Soal pilihan ganda harus memiliki minimal satu opsi.";
                $this->skipped++;
                return null;
            }
            
            // Validate that we have at least 2 options for pilihan_ganda
            $validOptions = array_filter($options, function($opt) { return !empty(trim($opt)); });
            if (count($validOptions) < 2) {
                $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Soal pilihan ganda harus memiliki minimal 2 opsi.";
                $this->skipped++;
                return null;
            }
            
            // Ensure we have at least A, B, C, D keys (fill empty ones)
            foreach (['A', 'B', 'C', 'D'] as $key) {
                if (!isset($options[$key])) {
                    $options[$key] = '';
                }
            }
        }

        // Get correct answer
        $correctAnswer = $normalized['jawabanbenar'] ?? $normalized['correctanswer'] ?? $normalized['jawaban'] ?? '';
        if (empty($correctAnswer)) {
            $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Jawaban benar harus diisi.";
            $this->skipped++;
            return null;
        }
        
        // Validate correct answer for pilihan_ganda
        if ($questionType === 'pilihan_ganda') {
            $correctAnswer = strtoupper(trim($correctAnswer));
            if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Jawaban benar untuk pilihan ganda harus A, B, C, atau D. Ditemukan: '{$correctAnswer}'";
                $this->skipped++;
                return null;
            }
            
            // Check if the correct answer option exists
            if (!isset($options[$correctAnswer]) || empty(trim($options[$correctAnswer]))) {
                $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Jawaban benar '{$correctAnswer}' tidak ada dalam opsi yang tersedia.";
                $this->skipped++;
                return null;
            }
        }

        // Get difficulty
        $difficulty = strtolower($normalized['tingkatkesulitan'] ?? $normalized['difficulty'] ?? $normalized['kesulitan'] ?? 'sedang');
        $difficulty = in_array($difficulty, ['mudah', 'sedang', 'sulit']) ? $difficulty : 'sedang';

        try {
            $question = Question::create([
                'subject_id' => $subject->id,
                'created_by' => Auth::id(),
                'question_text' => $questionText,
                'question_type' => $questionType,
                'options' => $options,
                'correct_answer' => trim($correctAnswer),
                'level' => !empty(trim($normalized['tingkat'] ?? $normalized['level'] ?? '')) ? trim($normalized['tingkat'] ?? $normalized['level'] ?? '') : null,
                'topic' => !empty(trim($normalized['topik'] ?? $normalized['topic'] ?? '')) ? trim($normalized['topik'] ?? $normalized['topic'] ?? '') : null,
                'difficulty' => $difficulty,
                'points' => max(1, (int)($normalized['poin'] ?? $normalized['points'] ?? 1)),
                'explanation' => !empty(trim($normalized['penjelasan'] ?? $normalized['explanation'] ?? '')) ? trim($normalized['penjelasan'] ?? $normalized['explanation'] ?? '') : null,
            ]);
            $this->imported++;
            return $question;
        } catch (\Exception $e) {
            $this->errors[] = "Baris " . (($this->imported + $this->skipped) + 1) . ": Gagal menyimpan soal - " . $e->getMessage();
            $this->skipped++;
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.matapelajaran' => ['required'],
            '*.pertanyaan' => ['required'],
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }
    
    public function getImported()
    {
        return $this->imported;
    }
    
    public function getSkipped()
    {
        return $this->skipped;
    }
}
