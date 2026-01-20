<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    // Table name is "units" by convention, so no need to set $table

    protected $fillable = [
        'name',
        'code',              // e.g. USD, LBP
        'symbol',            // e.g. $, ل.ل
        'price_in_dollar',   // 1.0 for USD, 0.2723 for AED, etc.
    ];

    protected $casts = [
        'price_in_dollar' => 'float',
    ];

    /**
     * A currency unit can be used by many properties.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'unit_id');
    }

    /**
     * A currency unit can be used by many transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'unit_id');
    }

    /**
     * Accessor: a convenient display label like "US Dollar ($)".
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->symbol})";
    }

    /**
     * Mutator: always store code uppercase (usd -> USD).
     */
    public function setCodeAttribute($value): void
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * Quick finder by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', strtoupper($code));
    }
}
