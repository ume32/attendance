<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => '管理者アカウント',
            'email' => 'admin@email.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
