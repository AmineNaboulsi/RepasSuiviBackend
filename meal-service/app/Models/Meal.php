<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'meal_type' ,'user_id'];

    // public function foods()
    // {
    //     return $this->belongsToMany(Food::class , 'meal_items');
    // }
    public function foods()
    {
        return $this->belongsToMany(Food::class, 'meal_items')
            ->withPivot('quantity', 'unite') ;
    }
}
