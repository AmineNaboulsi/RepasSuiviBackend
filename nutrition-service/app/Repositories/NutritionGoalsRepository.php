<?php

namespace App\Repositories;

use App\Models\NutritionGoals;
use App\Repositories\Interfaces\NutritionGoalsRepositoryInterface;

class NutritionGoalsRepository implements NutritionGoalsRepositoryInterface
{
    protected $model;

    public function __construct(NutritionGoals $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        $existingGoal = $this->model
            ->where('user_id', $data['user_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('startDate', [$data['startDate'], $data['endDate']])
                    ->orWhereBetween('endDate', [$data['startDate'], $data['endDate']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('startDate', '<=', $data['startDate'])
                            ->where('endDate', '>=', $data['endDate']);
                    });
            })
            ->first();
            
        if ($existingGoal) {
            throw new \Exception('A nutrition goal already exists within this date range.');
        }
        
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $goal = $this->getById($id);
        $goal->update($data);
        return $goal;
    }

    
    public function getUserGoals(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }
    
    public function getCurrentGoal(int $userId)
    {
        $today = now()->toDateString();
        return $this->model
            ->where('user_id', $userId)
            ->where('startDate', '<=', $today)
            ->where('endDate', '>=', $today)
            ->first();
    }
    
    public function delete(int $id)
    {
        $goal = $this->getById($id);
        return $goal->delete();
    }
}