<?php

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SellerSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Seller::create([
            'name' => 'Seller',
            'store_name' => 'Seller Store',
            'phone' => '08123456789',
            'address' => 'Jl. Seller No. 1',
            'email' => 'seller@gmail.com',
            'password' => bcrypt('menamena'),
        ]);
    }
}
