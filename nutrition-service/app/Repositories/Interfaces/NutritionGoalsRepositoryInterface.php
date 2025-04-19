<?php

namespace App\Repositories\Interfaces;

interface NutritionGoalsRepositoryInterface
{
    public function getAllById($userId,$date);
    public function create(array $data);
    public function delete(int $id);
    public function getUserGoals(int $userId);
    public function getCurrentGoal(int $userId);
}

