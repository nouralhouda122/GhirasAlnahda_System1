<?php
namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class IndicatorRepository
{
    public function getByDomain(string $domain)
    {
        return DB::table('indicators')
            ->where('domain', $domain)
            ->get();
    }
}
