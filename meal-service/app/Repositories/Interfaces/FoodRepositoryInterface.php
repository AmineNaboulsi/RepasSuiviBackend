<?php

namespace App\Repositories\Interfaces;

use App\Models\Food;

interface FoodRepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update(Food $food, array $data);
    public function delete(Food $food);
    public function updatefoodimage(Food $food , $image);
}

