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
                    'calories' => $meal->foods->sum('calories'),
                    'protein' => $meal->foods->sum('proteins'),
                    'carbs' => $meal->foods->sum('glucides'),
                    'fat' => $meal->foods->sum('lipides'),
                    'items' => $meal->foods->pluck('name')->toArray(),
                    'meal_type' => $meal->meal_type
                ];
            });
        })->toArray();
    }
}
