<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\SustainabilityInitiative;

class SustainabilityInitiativeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();
        
        if (!$admin) {
            $this->command->warn('No admin found, skipping sustainability initiatives seeding');
            return;
        }

        $initiatives = [
            [
                'title' => 'Zero Waste Fashion Initiative',
                'description' => 'Promoting sustainable fashion practices and reducing textile waste in the fashion industry. This initiative aims to educate consumers and producers about the environmental impact of fashion and provide alternatives.',
                'category' => 'environmental',
                'target_amount' => 1000000,
                'current_amount' => 350000,
            ],
            [
                'title' => 'Artisan Training & Empowerment Program',
                'description' => 'Training local artisans in sustainable production methods and modern design techniques while preserving traditional craftsmanship.',
                'category' => 'social',
                'target_amount' => 500000,
                'current_amount' => 200000,
            ],
            [
                'title' => 'Eco-Friendly Packaging Project',
                'description' => 'Transitioning all marketplace packaging to biodegradable and recyclable materials to reduce environmental impact.',
                'category' => 'environmental',
                'target_amount' => 750000,
                'current_amount' => 500000,
            ],
            [
                'title' => 'Renewable Energy for Artisans',
                'description' => 'Providing solar power solutions to artisan workshops and small businesses to reduce carbon footprint and electricity costs.',
                'category' => 'environmental',
                'target_amount' => 800000,
                'current_amount' => 150000,
            ],
        ];

        foreach ($initiatives as $initiativeData) {
            SustainabilityInitiative::create([
                'created_by' => $admin->id,
                'title' => $initiativeData['title'],
                'description' => $initiativeData['description'],
                'image_url' => 'https://via.placeholder.com/600x400?text=' . urlencode($initiativeData['title']),
                'category' => $initiativeData['category'],
                'status' => 'active',
                'target_amount' => $initiativeData['target_amount'],
                'current_amount' => $initiativeData['current_amount'],
                'impact_metrics' => rand(50, 200) . ' artisans trained, ' . rand(1000, 5000) . ' kg waste reduced',
                'start_date' => now()->subMonths(rand(3, 12)),
                'end_date' => now()->addMonths(rand(6, 18)),
                'partners' => json_encode(['Lagos State Government', 'Green NGO', 'Fashion Alliance', 'UN Environment']),
                'participant_count' => rand(50, 300),
                'progress_notes' => 'Making excellent progress towards our sustainability goals. Community engagement has been overwhelming.',
            ]);
        }

        $this->command->info('✓ Created ' . count($initiatives) . ' sustainability initiatives');
    }
}
