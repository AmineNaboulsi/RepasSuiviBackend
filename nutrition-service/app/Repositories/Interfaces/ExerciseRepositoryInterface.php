<?php

namespace App\Repositories\Interfaces;

use App\Models\Exercise;

interface ExerciseRepositoryInterface
{
    public function getMonthTrained($userId, $date);
    public function getAllWeekTrained($userId, $date);
    public function add_TrainingTime($data);
    public function delete_TrainingTime(int $userId);
    public function clear_AllTrainingTime(int $id, $date,$userId);
}