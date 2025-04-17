<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class mealsTrends extends JsonResource
{
    public function toArray(Request $request): array
    {
        $meals = collect($this->resource);
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $result = [];
        foreach ($daysOfWeek as $index => $day) {
            $dayMeals = $meals->filter(function ($meal) use ($index) {
                return Carbon::parse($meal->created_at)->dayOfWeek === ($index + 1) % 7;
            });

            $protein = 0;
            $carbs = 0;
            $fat = 0;
            $calories = 0;

            foreach ($dayMeals as $meal) {
                foreach ($meal->foods as $food) {
                    $quantity = $food->pivot->quantity ?? 1;
                    $unite = $food->pivot->unite ?? 'g';
                    
                    $multiplier = $unite === 'piece' ? $quantity : $quantity / 100;
                    
                    $protein += ($food->proteins ?? 0) * $multiplier;
                    $carbs += ($food->glucides ?? 0) * $multiplier;
                    $fat += ($food->lipides ?? 0) * $multiplier;
                    $calories += ($food->calories ?? 0) * $multiplier;
                }
            }

            $result[] = [
                'day' => $day,
                'protein' => $protein,
                'carbs' => $carbs,
                'fat' => $fat,
                'calories' => $calories,
            ];
        }

        return $result;
    }
}
