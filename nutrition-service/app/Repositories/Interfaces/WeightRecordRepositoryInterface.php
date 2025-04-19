<?php

namespace App\Repositories\Interfaces;

interface WeightRecordRepositoryInterface
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
    public function delete(int $id): bool;
    public function SearchByDate(int $id , $date);
    public function DateFilter(int $id , $date);
    public function getUserWeightRecords(int $id);
}

