<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionGoals extends Model
{
    use HasFactory;

    protected $fillable = [
        'dailyCalorieTarget',
        'proteinTarget',
        'carbTarget',
        'fatTarget',
        'startDate',
        'endDate',
        'user_id'
    ];
}
