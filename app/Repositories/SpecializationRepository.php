<?php

namespace App\Repositories;

use App\Models\Specialization;

class SpecializationRepository
{
    public function create(array $data): Specialization
    {
        return Specialization::create([
            'name' => $data['name']
        ]);
    }

    public function findOrCreate(string $name): Specialization
    {
        return Specialization::firstOrCreate([
            'name' => $name
        ]);
    }

    public function all()
    {
        return Specialization::all();
    }
}
