<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SellerProfile;

class SellerProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first 3 users to link seller profiles to
        $users = User::take(3)->get();

        foreach ($users as $user) {
            SellerProfile::factory()->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
