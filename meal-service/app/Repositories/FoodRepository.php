<?php

namespace App\Repositories;

use App\Models\Food;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoodRepository implements FoodRepositoryInterface
{
    public function getAll()
    {
        return Food::all();
    }

    public function getById($id)
    {
        return Food::findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->storeImage($data['image'], $data['name']);
        }
        return Food::create($data);
    }

    public function update(Food $food, array $data)
    {
        $food->update($data);
        return $food;
    }

    public function delete(Food $food)
    {
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }
        $food->delete();
    }

    public function updatefoodimage(Food $food, $image)
    {
        if ($image->isValid() && in_array($image->getMimeType(), ['image/jpeg', 'image/png'])) {
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            
            $imagePath = $this->storeImage($image, $food->name);
            
            $food->update(['image' => $imagePath]);
            
            return $food;
        }
    }

    private function storeImage($image, $foodName)
    {
        $timestamp = now()->timestamp;
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = Str::slug($foodName) . '-' . $timestamp . '.' . $fileExtension;

        return $image->storeAs('foods', $fileName, 'public'); 
    }
}
