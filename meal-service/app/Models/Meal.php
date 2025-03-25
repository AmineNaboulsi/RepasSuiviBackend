<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'calories',
        'glucides',
        'lipides',
        'category',
    ];

    public function foods()
    {
        return $this->belongsToMany(Food::class , 'meal_items');
    }
}
