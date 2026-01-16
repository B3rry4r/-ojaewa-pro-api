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
        // Always ensure test user has approved seller profile
        $testUser = User::where('email', 'test@ojaewa.com')->first();
        if ($testUser && !SellerProfile::where('user_id', $testUser->id)->exists()) {
            SellerProfile::factory()->create([
                'user_id' => $testUser->id,
                'registration_status' => 'approved',
                'active' => true,
            ]);
            $this->command->info('✓ Created approved seller profile for test@ojaewa.com');
        }

        // Create additional approved seller profiles for other users
        $users = User::where('email', '!=', 'test@ojaewa.com')->take(2)->get();

        foreach ($users as $user) {
            SellerProfile::factory()->create([
                'user_id' => $user->id,
                'registration_status' => 'approved',
                'active' => true,
            ]);
        }
    }
}
