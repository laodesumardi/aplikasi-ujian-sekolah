<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;

class DocQuestionParser
{
    public function parse($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Get text content from DOC/DOCX
        $text = '';
        
        if ($extension === 'docx') {
            $phpWord = IOFactory::load($filePath);
            $sections = $phpWord->getSections();
            
            foreach ($sections as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $child) {
                            if (method_exists($child, 'getText')) {
                                $text .= $child->getText() . "\n";
                            }
                        }
                    }
                }
            }
        } elseif ($extension === 'doc') {
            // For .doc files (older format), try to read as plain text
            // Note: PhpWord might not fully support .doc, so we'll try to read it
            try {
                $phpWord = IOFactory::load($filePath);
                $sections = $phpWord->getSections();
                
                foreach ($sections as $section) {
                    $elements = $section->getElements();
                    foreach ($elements as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
            } catch (\Exception $e) {
                // If DOC reading fails, try to read as text file
                $text = file_get_contents($filePath);
            }
        }
        
        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = explode("\n", $text);
        
        // Parse questions
        $questions = [];
        $currentQuestion = null;
        $collectingQuestion = false;
        $collectingOptions = false;
        
        foreach ($lines as $lineNum => $line) {
            $originalLine = $line;
            $line = trim($line);
            
            // Skip empty lines (but don't reset state)
            if (empty($line)) {
                continue;
            }
            
            // Check for separator line (like "________________________________________")
            if (preg_match('/^[_\-\=]+$/', $line)) {
                // This is a separator, ignore it but don't change state
                continue;
            }
            
            // Check if line starts with number pattern (e.g., "1.", "2.", etc.)
            if (preg_match('/^(\d+)\.\s*(.+)$/u', $line, $matches)) {
                // Save previous question if exists
                if ($currentQuestion && !empty($currentQuestion['question_text'])) {
                    $questions[] = $currentQuestion;
                }
                
                // Start new question
                $currentQuestion = [
                    'question_text' => $matches[2],
                    'options' => [],
                    'correct_answer' => null,
                    'question_type' => 'pilihan_ganda',
                ];
                $collectingQuestion = true;
                $collectingOptions = false;
            }
            // Check if line starts with option letter (A., B., C., D.) - support various formats (A. A) A- A:)
            elseif (preg_match('/^([ABCD])[\.\)\:\-]\s*(.+)$/ui', $line, $matches)) {
                $optionLetter = strtoupper(trim($matches[1]));
                $optionText = trim($matches[2]);
                
                if ($currentQuestion) {
                    // If optionText contains concatenated options (like "DeforestasiB. ReboisasiC. IndustrialisasiD. Urbanisasi")
                    $pattern = '/([ABCD])\s*[\.\)\:\-]?\s*([^ABCD]+?)(?=\s*[ABCD]\s*[\.\)\:\-]?\s*|$)/iu';
                    if (preg_match_all($pattern, $optionText, $optionMatches, PREG_SET_ORDER) && count($optionMatches) > 1) {
                        // Parse all concatenated options
                        foreach ($optionMatches as $match) {
                            $optKey = strtoupper(trim($match[1]));
                            $optValue = trim($match[2]);
                            if (!empty($optValue)) {
                                $currentQuestion['options'][$optKey] = $optValue;
                            }
                        }
                    } else {
                        // Single option
                        $currentQuestion['options'][$optionLetter] = $optionText;
                    }
                    $collectingOptions = true;
                    $collectingQuestion = false;
                }
            }
            // Check if line contains "Jawaban:" pattern
            elseif (preg_match('/^(?:Jawaban|JAWABAN|jawaban):\s*([ABCD])[\.\)\:\-]?\s*(.+)?$/ui', $line, $matches)) {
                if ($currentQuestion) {
                    $currentQuestion['correct_answer'] = strtoupper(trim($matches[1]));
                    // Optionally store explanation from answer text
                    if (isset($matches[2]) && !empty(trim($matches[2]))) {
                        $currentQuestion['explanation'] = trim($matches[2]);
                    }
                    // Mark that we've found the answer, question is complete
                    $collectingOptions = false;
                    $collectingQuestion = false;
                }
            }
            // If we're collecting question text, append to it (only if not an option or answer)
            elseif ($collectingQuestion && $currentQuestion && !$collectingOptions) {
                // Check if line might be an option (starts with A, B, C, or D followed by separator)
                if (preg_match('/^([ABCD])[\.\)\:\-]/ui', $line)) {
                    // This looks like an option, process it
                    if (preg_match('/^([ABCD])[\.\)\:\-]\s*(.+)$/ui', $line, $optMatches)) {
                        $optionLetter = strtoupper(trim($optMatches[1]));
                        $optionText = trim($optMatches[2]);
                        $currentQuestion['options'][$optionLetter] = $optionText;
                        $collectingOptions = true;
                        $collectingQuestion = false;
                    }
                } else {
                    // Append to question text
                    $currentQuestion['question_text'] .= ' ' . $line;
                }
            }
            // If we're collecting options, check if it's a new option or continuation of last option
            elseif ($collectingOptions && $currentQuestion) {
                // Check if this is actually a new option (starts with A, B, C, or D)
                if (preg_match('/^([ABCD])[\.\)\:\-]\s*(.+)$/ui', $line, $optMatches)) {
                    $optionLetter = strtoupper(trim($optMatches[1]));
                    $optionText = trim($optMatches[2]);
                    $currentQuestion['options'][$optionLetter] = $optionText;
                } elseif (!preg_match('/^(?:Jawaban|JAWABAN|jawaban):/ui', $line)) {
                    // Append to last collected option (multi-line option text)
                    $lastOptionKey = array_key_last($currentQuestion['options']);
                    if ($lastOptionKey && !empty($lastOptionKey)) {
                        $currentQuestion['options'][$lastOptionKey] .= ' ' . $line;
                    }
                }
            }
        }
        
        // Don't forget the last question
        if ($currentQuestion && !empty($currentQuestion['question_text'])) {
            $questions[] = $currentQuestion;
        }
        
        // Clean up questions
        foreach ($questions as &$question) {
            $question['question_text'] = trim($question['question_text']);
            
            // Ensure we have all required options for multiple choice
            if ($question['question_type'] === 'pilihan_ganda') {
                $requiredOptions = ['A', 'B', 'C', 'D'];
                foreach ($requiredOptions as $opt) {
                    if (!isset($question['options'][$opt])) {
                        $question['options'][$opt] = '';
                    } else {
                        $question['options'][$opt] = trim($question['options'][$opt]);
                    }
                }
            }
        }
        
        return $questions;
    }
}
