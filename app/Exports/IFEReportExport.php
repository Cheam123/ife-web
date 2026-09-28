<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class IFEReportExport implements FromArray, WithTitle, WithStyles, WithColumnFormatting, WithEvents, WithStrictNullComparison, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $reports;

    public function __construct(array $reports)
    {
        $this->reports = $reports;
    }

    public function title(): string
    {
        return 'IFE Report';
    }

    public function array(): array
    {
        return $this->reports;
    }

    public function styles(Worksheet $sheet)
    {
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_GENERAL, // Created By
            'B' => NumberFormat::FORMAT_GENERAL, // Company Name
            'C' => NumberFormat::FORMAT_GENERAL, // Nature of Business
            'D' => NumberFormat::FORMAT_GENERAL, // Status
            'E' => NumberFormat::FORMAT_GENERAL, // IFE Area
            'F' => NumberFormat::FORMAT_GENERAL, // Shop Name
            'G' => NumberFormat::FORMAT_GENERAL, // Problem Description
            'H' => NumberFormat::FORMAT_GENERAL, // Support Required
            'I' => NumberFormat::FORMAT_GENERAL, // Support Description
            'J' => NumberFormat::FORMAT_GENERAL, // Personal Remarks
            'K' => NumberFormat::FORMAT_GENERAL, // PIC Name
            'L' => NumberFormat::FORMAT_TEXT,    // Mobile Number
            'M' => NumberFormat::FORMAT_TEXT,    // Other Mobile Number
            'N' => NumberFormat::FORMAT_GENERAL, // Email
            'O' => NumberFormat::FORMAT_GENERAL, // Next Follow Up Date
            'P' => NumberFormat::FORMAT_GENERAL, // Next Follow Up Plan
            'Q' => NumberFormat::FORMAT_GENERAL, // Location
            'R' => NumberFormat::FORMAT_GENERAL, // Attachments
            'S' => NumberFormat::FORMAT_DATE_DATETIME, // Created At
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Style the header row
                $event->sheet->getStyle('A1:S1')->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('ECC608');

                $event->sheet->getDelegate()->getStyle('A1:S1')
                            ->getFont()
                            ->getColor()
                            ->setARGB('000000');
                
                $event->sheet->getDelegate()->getStyle('A1:S1')
                            ->getFont()
                            ->setBold(true);

                // Wrap text for all columns to ensure height autofit
                $event->sheet->getStyle('A:S')->getAlignment()->setWrapText(true);
                $event->sheet->getStyle('A:S')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            },
        ];
    }
}
