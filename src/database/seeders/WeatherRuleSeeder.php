<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherRule;

class WeatherRuleSeeder extends Seeder
{
    public function run(): void
    {
        WeatherRule::create([
            'name' => 'Low Temperature',

            'min_temp' => 0,
            'max_temp' => 28,

            'risk_level' => 'LOW',

            'recommendation' => 'Cuaca normal untuk aktivitas harian',

            'insight' => 'Tidak ada risiko cuaca signifikan',

            'is_active' => true,
        ]);

        WeatherRule::create([
            'name' => 'Medium Temperature',

            'min_temp' => 29,
            'max_temp' => 32,

            'risk_level' => 'MEDIUM',

            'recommendation' => 'Gunakan pakaian ringan dan tetap terhidrasi',

            'insight' => 'Udara cukup panas dan lembab',

            'is_active' => true,
        ]);

        WeatherRule::create([
            'name' => 'High Temperature',

            'min_temp' => 33,
            'max_temp' => 100,

            'risk_level' => 'HIGH',

            'recommendation' => 'Hindari aktivitas di luar ruangan saat siang hari',

            'insight' => 'Suhu sangat panas dan berisiko menyebabkan dehidrasi',

            'is_active' => true,
        ]);
    }
}