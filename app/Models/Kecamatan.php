<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    public function sekolahs()
    {
        return $this->hasMany(Sekolah::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }
}
