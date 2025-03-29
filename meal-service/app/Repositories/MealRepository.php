<?php

namespace App\Repositories;

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
        if (isset($data['meal_image'])) {
            $data['meal_image'] = $data['meal_image']->store('meals', 'public');
        }
        $data["meal"]['meal_type'] = $this->getMealType(Carbon::now());

        $meal = Meal::create($data["meal"]);
        $foodData = [];
        foreach ($data['meal_items'] as $item) {
            $foodData[$item['id']] = [
                'quantity' => $item['quantity'],
                'unite' => $item['unite']
            ];
        }

        
        $meal->foods()->attach($foodData);

        return $meal;
    }
    private function getMealType($date)
    {
        $time = Carbon::parse($date);
        $ifExists = Meal::where('created_at', '>=', $time->copy()->startOfDay())
        ->where('created_at', '<=', $time->copy()->endOfDay());
        if($ifExists->exists()) {
            return "Snack";
        }
        if ($time->hour >= 5 && $time->hour < 11) {
            return 'Breakfast';
        } elseif ($time->hour >= 11 && $time->hour < 16) {
            return 'Lunch';
        } elseif ($time->hour >= 16 && $time->hour < 22) {
            return 'Dinner';
        } else {
            return 'Snack';
        }
    }

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
