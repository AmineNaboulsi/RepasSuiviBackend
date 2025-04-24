<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'dateActivity',
        'BurnedCatories',
        'timeStart',
        'timeEnd',
        'user_id'
    ];
}

