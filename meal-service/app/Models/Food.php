<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'name', 'calories', 'proteins', 'glucides', 'lipides', 'category', 'image'
    ];

    protected $casts = [
        'calories' => 'float',
        'proteins' => 'float',
        'glucides' => 'float',
        'lipides' => 'float',
    ];
    public function meals()
    {
        return $this->belongsToMany(Meal::class , 'meal_items');
    }
}
