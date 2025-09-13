<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'property_type',
        'listing_type',
        'country',
        'city',
        'address',
        'price',
        'area_m3',
        'room_nb',
        'bathroom_nb',
        'bedroom_nb',
        'user_id'
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_property');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'property_rule');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    /**
     * Get the users who have liked this property
     */
    public function likedBy()
    {
        return $this->belongsToMany(User::class, 'property_likes')
                    ->withTimestamps();
    }

    /**
     * Check if a specific user has liked this property
     */
    public function isLikedBy($userId)
    {
        return $this->likedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Get the total number of likes for this property
     */
    public function getLikesCountAttribute()
    {
        return $this->likedBy()->count();
    }

    // app/Models/Property.php
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating for this property
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    /**
     * Get the total number of reviews for this property
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Check if a user has reviewed this property
     */
    public function hasUserReviewed($userId)
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's review for this property
     */
    public function getUserReview($userId)
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }

}
