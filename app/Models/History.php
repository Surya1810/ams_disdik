<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'requester_payload' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'old_values' => 'array'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * belum diubah, karena perlu ditelusuri
     * bagian mana saja yang sudah menggunakan ini
     */
    public function approval()
    {
        return $this->hasOne(Approval::class, 'asset_id', 'asset_id');
    }

    /**
     * sementara
     */
    public function approvaltemp()
    {
        return $this->hasOne(Approval::class, 'id', 'approval_id');
    }
}
