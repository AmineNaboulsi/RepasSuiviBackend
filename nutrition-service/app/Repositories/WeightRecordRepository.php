<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Repositories\Interfaces\WeightRecordRepositoryInterface;
use App\Models\WeightRecord;


class WeightRecordRepository implements WeightRecordRepositoryInterface
{
    protected $model;

    public function __construct(WeightRecord $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }
    public function DateFilter($userid , $date)
    {
        $now = \Carbon\Carbon::parse($date);
        $startOfMonth = $now->startOfMonth()->toDateString();
        $endOfMonth = $now->endOfMonth()->toDateString();
                          
        return $this->model->whereDate('created_at', '>=', $startOfMonth)
                            ->whereDate('created_at', '<=', $endOfMonth)
                            ->where('user_id',$userid)
                            ->orderBy('created_at', 'desc')
                            ->get();
    }
    public function SearchByDate($userid , $date)
    {
        $now = \Carbon\Carbon::parse($date)->toDateString();
        return $this->model->whereDate('created_at', '=', $now)->where('user_id',$userid)
                            ->get();
    }
    public function getUserWeightRecords(int $userId)
    {
        return $this->model->where('user_id', $userId)
                          ->whereDate('created_at', '>=', \Carbon\Carbon::now()->subDays(7))
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getById(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['date'])) {
            $dateFormatted = \Carbon\Carbon::parse($data['date'])->format('Y-m-d');
            $data['created_at'] = $dateFormatted . ' ' . \Carbon\Carbon::now()->format('H:i:s');
            
            $existingRecord = $this->model
                ->where('user_id', $data['user_id'])
                ->whereDate('created_at', $dateFormatted)
                ->first();

            if ($existingRecord) {
                $newWeight = [];
                $newWeight['weight'] = $data['weight'];
                if(isset($data['note'])) {
                    $newWeight['note'] = $data['note'];
                }
                $existingRecord->update($newWeight);
                return $existingRecord;
            }
            
            unset($data['date']);
        }
    
        return $this->model->forceFill($data)->save() ? $this->model : null;
    }

    public function delete(int $id): bool
    {
        return $this->getById($id)->delete();
    }
}