<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
