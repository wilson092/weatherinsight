<?php

namespace Database\Seeders;

use App\Models\RiskCategory;
use Illuminate\Database\Seeder;

class RiskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RiskCategory::create([
            'name' => 'Low Risk',
            'risk_level' => 'low',
            'min_temperature' => 0,
            'max_temperature' => 28,
            'color_badge' => 'success',
            'description' => 'Suhu udara dalam rentang aman, risiko rendah terhadap kesehatan dan aktivitas.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'Medium Risk',
            'risk_level' => 'medium',
            'min_temperature' => 29,
            'max_temperature' => 32,
            'color_badge' => 'warning',
            'description' => 'Suhu udara mulai panas, disarankan mengurangi aktivitas fisik berat di luar ruangan.',
            'is_active' => true,
        ]);

        RiskCategory::create([
            'name' => 'High Risk',
            'risk_level' => 'high',
            'min_temperature' => 33,
            'max_temperature' => null,
            'color_badge' => 'danger',
            'description' => 'Suhu udara sangat panas, risiko tinggi heat stroke. Hindari aktivitas di luar ruangan jika memungkinkan.',
            'is_active' => true,
        ]);
    }
}