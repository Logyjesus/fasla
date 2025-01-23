<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'customer',
            'phone' => '0123456789',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('menamena'),
        ]);
    }
}