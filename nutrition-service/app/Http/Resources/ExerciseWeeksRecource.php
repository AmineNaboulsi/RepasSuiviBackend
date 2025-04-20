<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseWeeksRecource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $activities = collect($this->resource);
        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $result = [];

        foreach ($daysOfWeek as $index => $day) {
            $dayActivities = $activities->filter(function ($activity) use ($index) {
                return Carbon::parse($activity->dateActivity)->dayOfWeek === ($index + 1) % 7;
            });

            $totalMinutes = 0;
            $totalCalories = 0;

            foreach ($dayActivities as $activity) {
                $start = Carbon::parse($activity->timeStart);
                $end = Carbon::parse($activity->timeEnd);

                $minutes = $end->diffInMinutes($start);
                $calories = $activity->BurnedCatories ?? 0;

                $totalMinutes += $minutes;
                $totalCalories += $calories;
            }

            $result[] = [
                'day' => $day,
                'minutes' => $totalMinutes,
                'calories' => $totalCalories,
            ];
        }

        return $result;
    }
}
