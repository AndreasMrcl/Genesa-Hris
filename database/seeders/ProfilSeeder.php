<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profil;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class ProfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::firstOrCreate(
        //     ['email' => 'admin@gmail.com'],
        //     [
        //         'name' => 'Admin',
        //         'password' => bcrypt('123456'),
        //     ]
        // );

        User::firstOrCreate(
            ['email' => 'adi@gmail.com'],
            [
                'name' => 'Adi',
                'password' => bcrypt('123456'),
            ]
        );

        // Employee::firstOrCreate(
        //     ['email' => 'afyww18@gmail.com'],
        //     [
        //         'name' => 'Afy Wahyu',
        //         'password' => bcrypt('123456'),
        //     ]
        // );

    }
}
