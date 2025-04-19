<?php

namespace App\Repositories;

use App\Models\Exercise;
use App\Repositories\Interfaces\ExerciseRepositoryInterface;
use Carbon\Carbon;
use DateTime;

class ExerciseRepository implements ExerciseRepositoryInterface
{
    protected $model;

    public function __construct(Exercise $model)
    {
        $this->model = $model;
    }

    public function getMonthTrained($userId, $date)
    {
        try{
            $dateCarbon = Carbon::parse($date);
            $startOfMonth = $dateCarbon->copy()->startOfMonth();
            $endOfMonth = $dateCarbon->copy()->endOfMonth();
            
            $allDayExerices = Exercise::where("user_id", $userId)
            ->whereBetween("dateActivity", [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d')
            ])
            ->get();
            return $allDayExerices;
        }catch(\Exception $e){
            throw new \Exception("Error retrieving today's training data " . $e->getMessage());
        }
    }
    public function getAllWeekTrained($userId, $date)
    {
        try {
            $date = $date ?? Carbon::now()->format('Y-m-d');
            $dateCarbon = Carbon::parse($date);
            
            $startOfWeek = $dateCarbon->copy()->startOfWeek();
            $endOfWeek = $dateCarbon->copy()->endOfWeek();
            
            $weekExercises = Exercise::where("user_id", $userId)
            ->whereBetween("dateActivity", [
                $startOfWeek->format('Y-m-d'),
                $endOfWeek->format('Y-m-d')
            ])
            ->get();
            
            return $weekExercises;
        } catch(\Exception $e) {
            throw new \Exception("Error retrieving week's training data " . $e->getMessage());
        }
    }
    

    public function add_TrainingTime($data)
    {   
        try{
            $exercise = new Exercise();
            $exercise->type = $data['type'] ?? null;
            $exercise->BurnedCatories = $data['BurnedCatories'] ?? null;
            $exercise->dateActivity = Carbon::parse($data['dateActivity'])->format('Y-m-d');
            $exercise->timeStart = $data['timeStart'];
            $exercise->timeEnd = $data['timeEnd'];
            $exercise->user_id = $data['user_id'];
            $exercise->save();
            return $exercise ;
        }catch(\Exception $e){
            throw new \Exception("Error adding training time " . $e->getMessage());
        }
    }

    public function delete_TrainingTime(int $userId)
    {

    }

    public function clear_AllTrainingTime(int $id, $date,$userId)
    {
        
    }
}