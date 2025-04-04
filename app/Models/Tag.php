<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $primaryKey = 'rfid_number'; // Karena primary key bukan 'id'
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['rfid_number', 'status', 'kecamatan_id'];

    // Tambahkan relasi ke Kecamatan
    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}
