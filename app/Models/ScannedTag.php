<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScannedTag extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_there' => 'boolean'];
}
