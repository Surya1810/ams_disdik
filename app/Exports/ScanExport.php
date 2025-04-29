<?php

namespace App\Exports;

use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class ScanExport implements FromCollection, WithEvents, WithTitle
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status; // 'found' atau 'missing'
    }

    public function collection()
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        if ($roleId == 1) {
            $asset = Asset::where('is_there', $this->status == 'found' ? true : false)->with('sekolah')->get();
        } else {
            $asset = Asset::where('is_there', $this->status == 'found' ? true : false)
                ->whereHas('sekolah', function ($query) use ($kecamatanId) {
                    $query->where('kecamatan_id', $kecamatanId);
                })->with('sekolah')->get();
        }

        return $asset->map(function ($row) {
            return [
                $row->rfid_number,
                $row->kode,
                $row->name,
                $row->merk,
                $row->tahun_pembelian,
                $row->kondisi,
                $row->sekolah ? $row->sekolah->name : '-',
                $row->pinjaman,
                $row->room,
                $row->row,
                $row->rack,
                $row->box,
            ];
        });
    }

    public function title(): string
    {
        return $this->status == 'found' ? 'Berita Acara Penemuan' : 'Berita Acara Kehilangan';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'L'; // Kolom terakhir

                // 1. Sisipkan 3 baris di atas untuk header
                $sheet->insertNewRowBefore(1, 3);

                // 2. Baris 1 - Judul utama
                $sheet->setCellValue('A1', $this->title());
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // 3. Baris 2 - Group Heading
                // Kita bagi 12 kolom menjadi 3 group misal:
                // Informasi Barang (A-L dibagi: A-G, H-L)
                // Karena kolomnya 12, saya buat 2 group saja agar rapi

                $sheet->setCellValue('A2', 'Informasi Barang');
                $sheet->mergeCells('A2:G2'); // Kolom A sampai G

                $sheet->setCellValue('H2', 'Lokasi');
                $sheet->mergeCells('H2:L2'); // Kolom H sampai L

                $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                ]);
                $sheet->getRowDimension(2)->setRowHeight(25);

                // 4. Baris 3 - Sub Heading Detail
                $subHeadings = [
                    'RFID',
                    'Kode Barang',
                    'Nama/Jenis Barang',
                    'Merk/Type',
                    'Tahun Pembelian',
                    'Kondisi',
                    'Pinjaman',
                    'Tempat',
                    'Ruangan',
                    'Baris',
                    'Rak',
                    'Box'
                ];

                $col = 'A';
                foreach ($subHeadings as $heading) {
                    $sheet->setCellValue($col . '3', $heading);
                    $col++;
                }

                $sheet->getStyle("A3:{$lastColumn}3")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(25);

                // 5. Style isi data mulai dari baris 4 sampai akhir
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A4:{$lastColumn}{$highestRow}")->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // 6. Border untuk semua area (header + data)
                $sheet->getStyle("A1:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // 7. AutoSize semua kolom
                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // 8. Wrap Text header
                $sheet->getStyle("A1:{$lastColumn}3")->getAlignment()->setWrapText(true);

                // 9. Freeze pane agar baris header tetap terlihat saat scroll
                $sheet->freezePane('A4');
            }
        ];
    }
}
