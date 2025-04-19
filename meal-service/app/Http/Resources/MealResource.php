<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, array>
     */
    public static function collectionGroupedByDate($meals)
    {
        return $meals->groupBy(function ($meal) {
            return Carbon::parse($meal->created_at)->format('Y-m-d');
        })->map(function ($groupedMeals) {
            return $groupedMeals->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'user_id' => $meal->user_id,
                    'time' => Carbon::parse($meal->created_at)->format('H:i'),
                    'name' => $meal->name,
                    'calories' => round($meal->foods->sum(function($food){
                        return $food->pivot->unite == 'piece' ? 
                            $food->calories * $food->pivot->quantity : 
                            $food->calories * ($food->pivot->quantity / 100);
                    }), 2),
                    'protein' => round($meal->foods->sum(function($food) {
                        return $food->pivot->unite == 'piece' ? 
                            $food->proteins * $food->pivot->quantity : 
                            $food->proteins * ($food->pivot->quantity / 100);
                    }), 2),
                    'carbs' => round($meal->foods->sum(function($food) {
                        return $food->pivot->unite == 'piece' ? 
                            $food->glucides * $food->pivot->quantity : 
                            $food->glucides * ($food->pivot->quantity / 100);
                    }), 2),
                    'fat' => round($meal->foods->sum(function($food) {
                        return $food->pivot->unite == 'piece' ? 
                            $food->lipides * $food->pivot->quantity : 
                            $food->lipides * ($food->pivot->quantity / 100);
                    }), 2),
                    'items' => $meal->foods->pluck('name')->toArray(),
                    'type' => $meal->meal_type
                ];
            });
        })->toArray();
    }
}
