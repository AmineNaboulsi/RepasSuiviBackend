<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExercisesRecource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return collect($this->resource)->groupBy(function ($exercise) {
            return Carbon::parse($exercise->dateActivity)->format('Y-m-d');
        })
        ->map(function ($groupedExercises) {
            return $groupedExercises->map(function ($exercise) {
                return [
                    'time' => Carbon::parse($exercise->created_at)->format('H:i'),
                    'exerciseTime' => Carbon::parse($exercise->timeStart)->diffInMinutes(Carbon::parse($exercise->timeEnd)),
                    'exerciseType' => $exercise->type,
                    'BurnedCatories' => $exercise->BurnedCatories,
                ];
            });
        })
        ->toArray();
    }
}
