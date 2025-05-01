<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Tag;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToCollection, WithHeadingRow
{
    protected $sekolahId;
    protected $errors = [];

    public function __construct($sekolahId)
    {
        $this->sekolahId = $sekolahId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // Baris 1 header, jadi mulai dari 2

            try {
                if (empty($row['tag']) || empty($row['kode'])) {
                    $this->errors[] = "Baris {$rowNumber}: tag atau kode kosong.";
                    continue;
                }

                $tag = Tag::where('rfid_number', $row['tag'])->first();

                if (!$tag->exists()) {
                    $this->errors[] = "Baris {$rowNumber}: tag '{$row['tag']}' tidak ditemukan.";
                    continue;
                }

                if ($tag->kecamatan_id != Auth::user()->kecamatan_id) {
                    $this->errors[] = "Baris {$rowNumber}: tag '{$row['tag']}' tidak tersedia.";
                    continue;
                }

                $data = [
                    'rfid_number' => $row['tag'],
                    'sekolah_id' => $this->sekolahId,
                    'kode' => $row['kode'],
                    'name' => $row['name'],
                    'register' => $row['register'],
                    'merk' => $row['merk'],
                    'ukuran' => $row['ukuran'] ?? null,
                    'bahan' => $row['bahan'],
                    'tahun_pembelian' => $row['tahun_pembelian'],
                    'pabrik' => $row['pabrik'] ?? null,
                    'rangka' => $row['rangka'] ?? null,
                    'mesin' => $row['mesin'] ?? null,
                    'polisi' => $row['polisi'] ?? null,
                    'bpkb' => $row['bpkb'] ?? null,
                    'nip_pic' => $row['nip_pic'],
                    'nama_pic' => $row['nama_pic'],
                    'jabatan_pic' => $row['jabatan_pic'],
                    'telp_pic' => $row['telp_pic'],
                    'asal_perolehan' => $row['asal_perolehan'],
                    'nilai_perolehan' => $row['nilai_perolehan'],
                    'kondisi' => $row['kondisi'],
                    'tanggal_perawatan' => $row['tanggal_perawatan'],
                    'harga_perawatan' => $row['harga_perawatan'],
                    'waktu_perawatan' => $row['waktu_perawatan'],
                    'gedung' => $row['gedung'],
                    'lantai' => $row['lantai'],
                    'ruangan' => $row['ruangan'],
                    'detail' => $row['detail']
                ];

                $asset = Asset::create($data);

                // Update Tag
                Tag::where('rfid_number', $asset->rfid_number)->update([
                    'status' => 'used'
                ]);
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                continue;
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
