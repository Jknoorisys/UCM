<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Admin',
            'phone' => '9685741235',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'percent' => 10.00,
        ]);
    }
}
