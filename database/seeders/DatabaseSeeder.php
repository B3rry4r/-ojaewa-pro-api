<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 5 fake users
        User::factory(5)->create();

        // Create admin and other records
        $this->call([
            AdminSeeder::class,
            SellerProfileSeeder::class, 
            ProductSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            BusinessProfileSeeder::class,
        ]);
    }
}
