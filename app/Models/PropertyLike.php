<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyLike extends Model
{
    protected $fillable = [
        'user_id',
        'property_id'
    ];

    /**
     * Get the user who liked the property
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property that was liked
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
