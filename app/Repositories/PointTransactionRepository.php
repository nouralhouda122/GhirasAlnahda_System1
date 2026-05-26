<?php


namespace App\Repositories;


use App\Models\Campaign;
use App\Models\PointTransaction;

class PointTransactionRepository
{
    public function create(array $data)
    {
        return PointTransaction::create($data);

    }
}
