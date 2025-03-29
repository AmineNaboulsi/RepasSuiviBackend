<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'meal_type', 'meal_image'];

    public function foods()
    {
        return $this->belongsToMany(Food::class , 'meal_items');
    }
}
