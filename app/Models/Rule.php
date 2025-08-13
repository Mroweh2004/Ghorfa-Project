<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_rule');
    }
}
