<?php

namespace App\Repositories\Interfaces;

interface NutritionGoalsRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function delete(int $id);
}

