<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $guarded = ['id'];

    public function scannedTags() {
        return $this->hasMany(ScannedTag::class);
    }
}
