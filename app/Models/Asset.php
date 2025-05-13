<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['rfid_number', 'sekolah_id', 'kode', 'name', 'register', 'merk', 'ukuran', 'bahan', 'tahun_pembelian', 'pabrik', 'rangka', 'mesin', 'polisi', 'bpkb', 'nip_pic', 'nama_pic', 'jabatan_pic', 'telp_pic', 'asal_perolehan', 'nilai_perolehan', 'kondisi', 'tanggal_perawatan', 'harga_perawatan', 'waktu_perawatan', 'gedung', 'lantai', 'ruangan', 'detail', 'foto_awal', 'foto_kondisi', 'status', 'desc', 'is_there', 'tanggal_pembelian'];

    protected $casts = [
        'tanggal_perawatan' => 'datetime',
    ];

    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'rfid_number');
    }

    public function histories()
    {
        return $this->hasMany(History::class);
    }

    public function approval()
    {
        return $this->hasMany(Approval::class);
    }
}
