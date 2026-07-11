<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherRule;

class WeatherRuleSeeder extends Seeder
{
    public function run(): void
    {
        WeatherRule::truncate();

        $rules = [
            [
                'name' => 'Suhu Tinggi',
                'rule_type' => 'temperature',
                'operator' => '>',
                'threshold_value' => 35,
                'description' => 'Suhu yang sangat tinggi dapat meningkatkan risiko dehidrasi dan heat stroke.',
                'is_active' => true,
            ],
            [
                'name' => 'Kelembapan Tinggi',
                'rule_type' => 'humidity',
                'operator' => '>',
                'threshold_value' => 85,
                'description' => 'Kelembapan yang sangat tinggi dapat membuat cuaca terasa lebih panas dan menyebabkan ketidaknyamanan.',
                'is_active' => true,
            ],
            [
                'name' => 'Angin Kencang',
                'rule_type' => 'wind_speed',
                'operator' => '>',
                'threshold_value' => 20,
                'description' => 'Angin kencang dapat berbahaya bagi struktur bangunan dan aktivitas di luar ruangan.',
                'is_active' => true,
            ],
            [
                'name' => 'Tekanan Udara Rendah',
                'rule_type' => 'pressure',
                'operator' => '<',
                'threshold_value' => 1000,
                'description' => 'Tekanan udara yang rendah seringkali mengindikasikan potensi cuaca buruk atau badai.',
                'is_active' => true,
            ],
            [
                'name' => 'Tekanan Udara Tinggi',
                'rule_type' => 'pressure',
                'operator' => '>',
                'threshold_value' => 1020,
                'description' => 'Tekanan udara yang sangat tinggi dapat mempengaruhi kondisi cuaca dan kesehatan beberapa individu.',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            WeatherRule::create($rule);
        }
    }
}