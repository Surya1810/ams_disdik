<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $fillable = [
        'name',
        'category',
        'kecamatan_id', // Tambahkan ini
    ];
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
