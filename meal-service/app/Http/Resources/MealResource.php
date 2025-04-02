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
                    'calories' => $meal->foods->sum(function($food){
                        return $food->calories * $food->pivot->quantity; 
                    }),
                    'protein' => $meal->foods->sum(function($food) {
                        return $food->proteins * $food->pivot->quantity;
                    }),
                    'carbs' => $meal->foods->sum(function($food) {
                        return $food->glucides * $food->pivot->quantity;
                    }),
                    'fat' => $meal->foods->sum(function($food) {
                        return $food->lipides * $food->pivot->quantity;
                    }),
                    'items' => $meal->foods->pluck('name')->toArray(),
                    'type' => $meal->meal_type
                ];
            });
        })->toArray();
    }
}
