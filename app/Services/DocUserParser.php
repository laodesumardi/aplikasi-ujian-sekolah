<?php

namespace App\Services;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;

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
                                if (method_exists($child, 'getText')) {
                                    $text .= $child->getText();
                                }
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
                                    'kelas' => 'kelas',
                                    'password' => 'password',
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
}