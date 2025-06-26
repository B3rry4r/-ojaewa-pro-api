<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@ojaewa.com'],
            [
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'password' => Hash::make('Admin@1234'),
            ]
        );
    }
}
