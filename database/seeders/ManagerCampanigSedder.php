<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManagerCampanigSedder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'ManagerCampanigSedder',
            'email' => 'LujenChaban1234@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => '2026-04-09',
        ]);
    }
}
