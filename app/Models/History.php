<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'old_value',
        'new_value',
        'created_by'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approval()
    {
        return $this->hasOne(Approval::class, 'asset_id', 'asset_id');
    }
}
