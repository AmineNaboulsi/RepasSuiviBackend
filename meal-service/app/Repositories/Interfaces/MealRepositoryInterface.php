<?php

namespace App\Repositories\Interfaces;

use App\Models\Food;
use App\Models\Meal;

interface MealRepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function create(array $data);
    public function update(Meal $food, array $data);
    public function delete(Meal $food);
}

