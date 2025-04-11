<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'change_type',
        'changed_fields',
        'old_values',
        'new_values',
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
