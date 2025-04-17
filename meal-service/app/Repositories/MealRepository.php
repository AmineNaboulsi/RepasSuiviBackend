<?php

namespace App\Repositories;

use App\Http\Resources\mealsTrends;
use App\Models\Meal;
use App\Repositories\Interfaces\MealRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealRepository implements MealRepositoryInterface
{
    public function getAll()
    {
        return Meal::with('foods')->get();
    }

    public function getById($id)
    {
        return Meal::with('foods')->findOrFail($id);
    }

    public function create(array $data)
    {
        try {
            
            $dateFormatted = \Carbon\Carbon::parse($data["meal"]['date'])->format('Y-m-d');
            $meal = new Meal();
            $newmeal = [];
            $newmeal['user_id'] = $data["meal"]['user_id'];
            $newmeal['name'] = $data["meal"]['name'];
            $newmeal['meal_type'] = $data["meal"]['meal_type'];
            $newmeal['created_at'] = $dateFormatted . ' ' . \Carbon\Carbon::now()->format('H:i:s');
            
            $meal->forceFill($newmeal)->save();

            $foodData = [];
            foreach ($data['meal_items'] as $item) {
                $foodData[$item['id']] = [
                    'quantity' => $item['quantity'],
                    'unite' => $item['unite'],
                ];
            }

            $meal->foods()->attach($foodData);

            return $meal;

        } catch (\Exception $e) {
            throw new \Exception('Error creating meal: ' . $e->getMessage());
        }

    }

    public function getCaloroysTrendbyDate($date, $userId)
    {
        try {
            $parsedDate = Carbon::parse($date);
            $startOfWeek = $parsedDate->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek = $parsedDate->copy()->endOfWeek(Carbon::SUNDAY);
            
            return Meal::where('user_id', $userId)
                ->whereBetween('created_at', [
                    $startOfWeek->format('Y-m-d') . ' 00:00:00',
                    $endOfWeek->format('Y-m-d') . ' 23:59:59'
                ])
                ->with(['foods' => function($query) {
                    $query->withPivot('quantity','unite');
                }])
                ->orderBy('created_at')
                ->get();
        } catch (\Exception $e) {
            throw new \Exception('Error fetching meals: ' . $e->getMessage());
        }
    }

    // private function getMealType($date)
    // {
    //     $time = Carbon::parse($date);
    //     $ifExists = Meal::where('created_at', '>=', $time->copy()->startOfDay())
    //     ->where('created_at', '<=', $time->copy()->endOfDay());
    //     if($ifExists->exists()) {
    //         return "Snack";
    //     }
    //     if ($time->hour >= 5 && $time->hour < 11) {
    //         return 'Breakfast';
    //     } elseif ($time->hour >= 11 && $time->hour < 16) {
    //         return 'Lunch';
    //     } elseif ($time->hour >= 16 && $time->hour < 22) {
    //         return 'Dinner';
    //     } else {
    //         return 'Snack';
    //     }
    // }

    public function update(Meal $meal, array $data)
    {
        if (isset($data['meal_image'])) {
            if ($meal->meal_image) {
                Storage::disk('public')->delete($meal->meal_image);
            }
            $data['meal_image'] = $data['meal_image']->store('meals', 'public');
        }

        $meal->update($data);

        return $meal;
    }

    public function delete(Meal $meal)
    {
        if ($meal->meal_image) {
            Storage::disk('public')->delete($meal->meal_image);
        }

        $meal->delete();
    }
}
