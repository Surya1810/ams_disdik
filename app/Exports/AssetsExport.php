<?php

namespace App\Exports;

use App\Models\Asset;

use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AssetsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public function collection()
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        if ($roleId == 1) {
            $asset = Asset::with('sekolah')->get();
        } else {
            $asset = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })->with('sekolah')->get();
        }

        $asset = $asset->map(function ($row) {
            return [
                'rfid_number' => $row->rfid_number,
                'kode' => $row->kode,
                'name' => $row->name,
                'merk' => $row->merk,
                'tahun_pembelian' => $row->tahun_pembelian,
                'kondisi' => $row->kondisi,
                'sekolah' => $row->sekolah ? $row->sekolah->name : null
            ];
        });

        return $asset;
    }

    public function headings(): array
    {
        return [
            ["Laporan Data Asset"],
            ['RFID Number', 'Kode Barang', 'Nama/Jenis Barang', 'Merk/Type', 'Tahun Pembelian', 'Kondisi', 'Tempat']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = 'G';

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

        // style untuk isi data
        $sheet->getStyle("A3:$lastColumn" . "3")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
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
