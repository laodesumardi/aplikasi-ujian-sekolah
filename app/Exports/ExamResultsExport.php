<?php

namespace App\Exports;

use App\Models\Exam;
use App\Models\ExamResult;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExamResultsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $exam;
    protected $results;

    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
        $this->results = ExamResult::with('student')
            ->where('exam_id', $exam->id)
            ->where('status', 'completed')
            ->orderBy('score', 'desc')
            ->get();
    }

    public function collection()
    {
        return $this->results;
    }

    public function headings(): array
    {
        return [
            'No',
            'Rank',
            'Nama Siswa',
            'Kelas',
            'Nilai',
            'Total Poin',
            'Persentase (%)',
            'Status',
            'Waktu Pengerjaan',
            'Tanggal Mulai',
            'Tanggal Selesai',
        ];
    }

    public function map($result): array
    {
        static $rank = 0;
        $rank++;

        $isPassed = $result->percentage >= 60;
        $status = $isPassed ? 'Lulus' : 'Tidak Lulus';

        // Format waktu pengerjaan
        $timeTaken = '-';
        if ($result->time_taken) {
            $hours = floor($result->time_taken / 3600);
            $minutes = floor(($result->time_taken % 3600) / 60);
            $seconds = $result->time_taken % 60;
            $timeParts = [];
            if ($hours > 0) $timeParts[] = $hours . 'j';
            if ($minutes > 0) $timeParts[] = $minutes . 'm';
            if ($seconds > 0 || empty($timeParts)) $timeParts[] = $seconds . 's';
            $timeTaken = implode(' ', $timeParts);
        }

        return [
            $rank,
            $rank,
            $result->student->name ?? '-',
            $result->student->kelas ?? '-',
            $result->score ?? 0,
            $result->total_points ?? 0,
            number_format($result->percentage ?? 0, 2),
            $status,
            $timeTaken,
            $result->started_at ? $result->started_at->format('d/m/Y H:i') : '-',
            $result->submitted_at ? $result->submitted_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style header
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '003F88'], // Primary color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set header row height
        $sheet->getRowDimension('1')->setRowHeight(30);

        // Style data rows
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:K' . $highestRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align for numeric columns
            $sheet->getStyle('A2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E2:F' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H2:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Alternating row colors
        for ($row = 2; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            }
        }

        // Highlight top 3 rows
        if ($highestRow >= 2) {
            $sheet->getStyle('A2:K2')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF9C4'], // Light yellow
                ],
                'font' => ['bold' => true],
            ]);
        }
        if ($highestRow >= 3) {
            $sheet->getStyle('A3:K3')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8E8E8'], // Light gray
                ],
                'font' => ['bold' => true],
            ]);
        }
        if ($highestRow >= 4) {
            $sheet->getStyle('A4:K4')->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFE0B2'], // Light orange
                ],
                'font' => ['bold' => true],
            ]);
        }
    }

    public function title(): string
    {
        return 'Hasil Ujian';
    }
}

