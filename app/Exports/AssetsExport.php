<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Asset;

// Excel
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssetsExport implements FromCollection, WithEvents, WithTitle
{
    protected $kondisi;
    protected $sekolahId; // tempat
    protected $tahunPembelian;

    public function __construct($kondisi = null, $sekolahId = null, $tahunPembelian = null)
    {
        $this->kondisi = $kondisi;
        $this->sekolahId = $sekolahId;
        $this->tahunPembelian = $tahunPembelian;
    }

    public function collection()
    {
        $kecamatanId = Auth::user()->kecamatan_id;
        $roleId = Auth::user()->role_id;

        if ($roleId == 1) {
            $asset = Asset::with('sekolah.kecamatan');
        } else {
            $asset = Asset::whereHas('sekolah', function ($query) use ($kecamatanId) {
                $query->where('kecamatan_id', $kecamatanId);
            })->with('sekolah.kecamatan');
        }

        // Filter jika ada
        if ($this->kondisi) {
            $asset->where('kondisi', $this->kondisi);
        }

        if ($this->sekolahId) {
            $asset->where('tempat', $this->sekolahId);
        }

        if ($this->tahunPembelian) {
            $asset->where('tahun_pembelian', $this->tahunPembelian);
        }

        $asset = $asset->get();
        $asset = $asset->map(function ($row) {
            return [
                // Informasi Barang
                $row->rfid_number, $row->kode, $row->name, $row->register, $row->merk, $row->ukuran ?? '-', $row->bahan ?? '-', $row->tahun_pembelian, $row->pabrik ?? '-',

                // Nomor Barang
                $row->rangka ?? '-', $row->mesin ?? '-', $row->polisi ?? '-', $row->bpkb ?? '-',

                // Perawatan Barang
                $row->asal_perolehan, formatRupiah($row->nilai_perolehan), $row->kondisi, $row->tanggal_perawatan->format('Y-m-d'), formatRupiah($row->harga_perawatan), ($row->waktu_perawatan . ' Bulan'),

                // Lokasi
                $row->sekolah ? $row->sekolah->kecamatan->name : '-', $row->sekolah ? $row->sekolah->name : '-', $row->gedung, $row->lantai, $row->ruangan, $row->detail
            ];
        });

        return $asset;
    }

    public function title(): string
    {
        return 'List Data Aset';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'Y'; // Sampai kolom Y, kolom terakhir

                // 1. Sisipkan 3 baris di atas (karena kita akan butuh baris 1-3 untuk header)
                $sheet->insertNewRowBefore(1, 3);

                // 2. Baris 1 - Laporan Data Asset
                $sheet->setCellValue('A1', 'List Data Aset');
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle("A1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                ]);

                // 3. Baris 2 - Group heading
                $sheet->setCellValue('A2', 'Informasi Barang');
                $sheet->mergeCells('A2:I2'); // Kolom A sampai I

                $sheet->setCellValue('J2', 'Nomor Barang');
                $sheet->mergeCells('J2:M2'); // Kolom J sampai M

                $sheet->setCellValue('N2', 'Perawatan Barang');
                $sheet->mergeCells('N2:S2'); // Kolom N sampai S

                $sheet->setCellValue('T2', 'Lokasi');
                $sheet->mergeCells('T2:Y2'); // Kolom T sampai Y

                $sheet->getStyle('A2:Y2')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                ]);

                // 4. Baris 3 - Sub Heading Detail
                $subHeadings = [
                    'RFID', 'Kode Barang', 'Nama/Jenis Barang', 'Nomor Register', 'Merk', 'Ukuran', 'Bahan', 'Tahun Pembelian', 'Pabrik',
                    'Rangka', 'Mesin', 'Polisi', 'BPKB',
                    'Asal-usul Perolehan', 'Nilai Perolehan', 'Kondisi', 'Tanggal Perawatan', 'Harga Perawatan', 'Jangka Waktu Perawatan',
                    'Kecamatan', 'Tempat', 'Gedung', 'Lantai', 'Ruangan', 'Detail'
                ];
                $col = 'A';
                foreach ($subHeadings as $heading) {
                    $sheet->setCellValue($col.'3', $heading);
                    $col++;
                }

                $sheet->getStyle('A3:Y3')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F81BD']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Mengatur isi agar rata tengah
                $sheet->getStyle("A4:$lastColumn" . $sheet->getHighestRow())->applyFromArray([
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
                ]);

                // 5. Border untuk semua area
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:Y{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // 6. AutoSize semua kolom
                foreach (range('A', $lastColumn) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // 7. Wrap Text heading
                $sheet->getStyle('A1:Y3')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
