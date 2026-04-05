<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandlordApplication extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'id_number',
        'trade_license',
        'document_type',
        'document_front_path',
        'document_back_path',
        'face_photo_path',
        'notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function verificationNumber(): ?string
    {
        return $this->id_number ?? $this->trade_license;
    }

    public function verificationLabel(): string
    {
        return match ($this->document_type) {
            'national_id' => 'National ID',
            'trade_license' => 'Trade license',
            default => $this->id_number ? 'National ID' : ($this->trade_license ? 'Trade license' : 'Document'),
        };
    }
}
