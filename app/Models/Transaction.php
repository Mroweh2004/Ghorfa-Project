<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'type',
        'price',
        'unit_id',
        'status',
        'notes',
        'starting_time',
        'finishing_time',
        'paid_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'starting_time' => 'string',
        'finishing_time' => 'string',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property associated with the transaction.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the unit (currency) associated with the transaction.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if transaction is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is cancelled.
     */
    public function isCancelled(): bool
    {
        return in_array($this->status, ['cancelled_by_buyer', 'cancelled_by_seller']);
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Check if transaction is a buy type.
     */
    public function isBuy(): bool
    {
        return $this->type === 'buy';
    }

    /**
     * Check if transaction is a rent type.
     */
    public function isRent(): bool
    {
        return $this->type === 'rent';
    }

    /**
     * Check if transaction is a refund type.
     */
    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }
}
