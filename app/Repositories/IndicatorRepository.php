<?php
namespace App\Repositories;


use App\Models\Indicator;

class IndicatorRepository
{
    public function getById($id )

    {
        return Indicator::find($id);
    }
}
