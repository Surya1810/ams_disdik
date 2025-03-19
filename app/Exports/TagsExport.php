<?php

namespace App\Exports;

use App\Models\Tag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TagsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        return Tag::select('rfid_number', 'status', 'created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'rfid_number' => $row->rfid_number,
                    'status' => $row->status,
                    'created_at' => $row->created_at->format('Y-m-d'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            ["Laporan Data Tag RFID"],
            ['RFID Number', 'Status', 'Created At']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'C';

        $sheet->mergeCells("A1:$lastColumn" . "1")
            ->getStyle("A1")->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ]);

        $sheet->getStyle("A2:$lastColumn" . "2")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
        ]);

        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle("A2:$lastColumn" . $sheet->getHighestRow())->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => '000000']]]
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => fn(AfterSheet $event) =>
            $event->sheet->getDelegate()->getDefaultRowDimension()->setRowHeight(-1)
        ];
    }
}
