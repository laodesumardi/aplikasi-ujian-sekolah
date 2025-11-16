<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;

class DocUserParser
{
    /**
     * Parse DOCX file that contains a table with headers: name, email, role, kelas, password
     * Returns array of associative rows.
     */
    public function parse(string $filePath): array
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext !== 'docx') {
            return [];
        }

        $phpWord = IOFactory::load($filePath);
        $rowsOut = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // Detect tables with proper type check to satisfy static analyzers
                if ($element instanceof Table) {
                    $headerMap = [];
                    $isHeaderParsed = false;
                    foreach ($element->getRows() as $rIndex => $row) {
                        // Rows from Table::getRows() are \PhpOffice\PhpWord\Element\Row instances
                        $cells = $row->getCells();
                        $values = [];
                        foreach ($cells as $cell) {
                            $text = '';
                            foreach ($cell->getElements() as $child) {
                                $text .= $this->extractText($child);
                            }
                            $values[] = trim($text);
                        }

                        if (!$isHeaderParsed) {
                            // Build header map
                            foreach ($values as $i => $h) {
                                $key = strtolower(trim($h));
                                // Normalize common variations
                                $aliases = [
                                    'nama' => 'name',
                                    'name' => 'name',
                                    'email' => 'email',
                                    'role' => 'role',
                                    'peran' => 'role',
                                    'kelas' => 'kelas',
                                    'password' => 'password',
                                    'kata sandi' => 'password',
                                ];
                                $headerMap[$i] = $aliases[$key] ?? $key;
                            }
                            $isHeaderParsed = true;
                            continue;
                        }

                        // Map row values using header
                        $assoc = [];
                        foreach ($values as $i => $val) {
                            $key = $headerMap[$i] ?? ('col_' . $i);
                            $assoc[$key] = $val;
                        }
                        // Ensure minimum keys exist
                        if (!empty(array_filter($assoc))) {
                            $rowsOut[] = $assoc;
                        }
                    }
                }
            }
        }

        return $rowsOut;
    }

    private function extractText($element): string
    {
        // Handle direct text
        if ($element instanceof Text) {
            return (string) $element->getText();
        }
        // Handle nested run (contains Text elements)
        if ($element instanceof TextRun) {
            $txt = '';
            foreach ($element->getElements() as $child) {
                $txt .= $this->extractText($child);
            }
            return $txt;
        }
        // Fallback: try getText or recurse if it has children
        if (method_exists($element, 'getText')) {
            return (string) $element->getText();
        }
        if (method_exists($element, 'getElements')) {
            $txt = '';
            foreach ($element->getElements() as $child) {
                $txt .= $this->extractText($child);
            }
            return $txt;
        }
        return '';
    }
}