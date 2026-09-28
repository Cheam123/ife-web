<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FormSubmissionsExport implements FromArray, WithTitle, WithEvents, WithStrictNullComparison
{
    use RegistersEventListeners;

    protected array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function title(): string
    {
        return 'Form Submissions';
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastCol = 'N';
                $lastRow = count($this->rows);

                // ── Row 1: title bar ─────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1A5276'],
                    ],
                    'font' => [
                        'bold'  => true,
                        'size'  => 13,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ── Row 2: column headers ─────────────────────────────────────
                $sheet->getStyle("A2:{$lastCol}2")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF2C3E50'],
                    ],
                    'font' => [
                        'bold'  => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── Data rows: alternating shading + top‑align wrap ──────────
                for ($row = 3; $row <= $lastRow; $row++) {
                    $bg = ($row % 2 === 0) ? 'FFEAF4FB' : 'FFFFFFFF';
                    $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['argb' => $bg],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);
                }

                // ── Borders on the whole table ────────────────────────────────
                if ($lastRow >= 2) {
                    $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['argb' => 'FFCCCCCC'],
                            ],
                            'outline' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color'       => ['argb' => 'FF888888'],
                            ],
                        ],
                    ]);
                }

                // ── Fixed column widths ───────────────────────────────────────
                $widths = [
                    'A' => 6,   // #
                    'B' => 30,  // Form Name
                    'C' => 24,  // Submitted By
                    'D' => 20,  // Submitted At
                    'E' => 13,  // Status
                    'F' => 22,  // Tier 1 Approver
                    'G' => 20,  // Tier 1 Approval Date
                    'H' => 28,  // Tier 1 Remark
                    'I' => 22,  // Tier 2 Approver
                    'J' => 20,  // Tier 2 Approval Date
                    'K' => 28,  // Tier 2 Remark
                    'L' => 22,  // Rejected By
                    'M' => 28,  // Rejected Remark
                    'N' => 60,  // Form Responses
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Auto row height for data rows (content varies per submission)
                for ($row = 3; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                // Status column (E): centre-align data cells
                for ($row = 3; $row <= $lastRow; $row++) {
                    $sheet->getStyle("E{$row}")->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── Freeze pane below header ──────────────────────────────────
                $sheet->freezePane('A3');
            },
        ];
    }
}
