<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSedder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      //  Admin::truncate();
        User::query()->create([
            'name' => 'GeneralManager',
            'email' => 'RahafAlghalini1234@gmail.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => '2026-04-09',
        ]);

    }

}
