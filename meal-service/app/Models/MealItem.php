<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealItem extends Model
{
    use HasFactory;

    protected $fillable = ['meal_id', 'food_id', 'quantity'];

    protected $casts = [
        'quantity' => 'integer',
    ];
    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
