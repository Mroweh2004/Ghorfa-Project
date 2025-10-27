<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Facades\Storage;

class PropertyImageService
{
    /**
     * Get the appropriate image URL for a property
     * Priority: Primary image -> First available image -> Default image
     *
     * @param Property $property
     * @param string|null $defaultImage
     * @return string
     */
    public static function getImageUrl(Property $property, ?string $defaultImage = null): string
    {
        $defaultImage = $defaultImage ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267';
        
        // Try to get primary image first
        $primaryImage = $property->images->where('is_primary', true)->first();
        if ($primaryImage) {
            return Storage::url($primaryImage->path);
        }
        
        // If no primary image, get the first available image
        $firstImage = $property->images->first();
        if ($firstImage) {
            return Storage::url($firstImage->path);
        }
        
        // Return default image if no property images exist
        return $defaultImage;
    }

    /**
     * Get the appropriate image URL for a property using asset() helper
     * Priority: Primary image -> First available image -> Default image
     *
     * @param Property $property
     * @param string|null $defaultImage
     * @return string
     */
    public static function getImageAssetUrl(Property $property, ?string $defaultImage = null): string
    {
        $defaultImage = $defaultImage ?? 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267';
        
        // Try to get primary image first
        $primaryImage = $property->images->where('is_primary', true)->first();
        if ($primaryImage) {
            return asset('storage/' . $primaryImage->path);
        }
        
        // If no primary image, get the first available image
        $firstImage = $property->images->first();
        if ($firstImage) {
            return asset('storage/' . $firstImage->path);
        }
        
        // Return default image if no property images exist
        return $defaultImage;
    }

    /**
     * Check if property has any images
     *
     * @param Property $property
     * @return bool
     */
    public static function hasImages(Property $property): bool
    {
        return $property->images->count() > 0;
    }

    /**
     * Get the count of property images
     *
     * @param Property $property
     * @return int
     */
    public static function getImageCount(Property $property): int
    {
        return $property->images->count();
    }

    /**
     * Get all property images as URLs
     *
     * @param Property $property
     * @return \Illuminate\Support\Collection
     */
    public static function getAllImageUrls(Property $property)
    {
        return $property->images->map(function ($image) {
            return Storage::url($image->path);
        });
    }

    /**
     * Get all property images as asset URLs
     *
     * @param Property $property
     * @return \Illuminate\Support\Collection
     */
    public static function getAllImageAssetUrls(Property $property)
    {
        return $property->images->map(function ($image) {
            return asset('storage/' . $image->path);
        });
    }
}
