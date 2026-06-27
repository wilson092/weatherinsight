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
                'score_weight' => 20,
                'description' => 'Suhu di atas 35°C menambah 20 poin risiko.',
                'is_active' => true,
            ],
            [
                'name' => 'Kelembapan Tinggi',
                'rule_type' => 'humidity',
                'operator' => '>',
                'threshold_value' => 85,
                'score_weight' => 25,
                'description' => 'Kelembaban di atas 85% menambah 25 poin risiko.',
                'is_active' => true,
            ],
            [
                'name' => 'Angin Kencang',
                'rule_type' => 'wind_speed',
                'operator' => '>',
                'threshold_value' => 20,
                'score_weight' => 15,
                'description' => 'Kecepatan angin di atas 20 m/s menambah 15 poin risiko.',
                'is_active' => true,
            ],
            [
                'name' => 'Tekanan Udara Rendah',
                'rule_type' => 'pressure',
                'operator' => '<',
                'threshold_value' => 1000,
                'score_weight' => 10,
                'description' => 'Tekanan udara di bawah 1000 hPa menambah 10 poin risiko.',
                'is_active' => true,
            ],
            [
                'name' => 'Tekanan Udara Tinggi',
                'rule_type' => 'pressure',
                'operator' => '>',
                'threshold_value' => 1020,
                'score_weight' => 10,
                'description' => 'Tekanan udara di atas 1020 hPa menambah 10 poin risiko.',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            WeatherRule::create($rule);
        }
    }
}