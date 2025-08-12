<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'path',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute()
    {
        return Storage::url($this->path);
    }

 
    public function setAsPrimary()
    {
        self::where('property_id', $this->property_id)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Delete the image file from storage when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if (Storage::exists($image->path)) {
                Storage::delete($image->path);
            }
        });
    }
}
