<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'meal_type',
        'meal_image',
    ];
    public function meals()
    {
        return $this->belongsToMany(Meal::class , 'meal_items');
    }
}
