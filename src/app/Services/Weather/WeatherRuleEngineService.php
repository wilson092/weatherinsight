<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;

class WeatherRuleEngineService
{
    public function analyze(WeatherHistory $weather): array
    {
        $recommendation = 'Cuaca normal';
        $risk = 'low';
        $insight = 'Tidak ada risiko signifikan';

        // HOT WEATHER
        if ($weather->temperature > 35) {
            $recommendation = 'Hindari aktivitas siang hari dan perbanyak minum';
            $risk = 'high';
            $insight = 'Risiko heatstroke akibat suhu tinggi';
        }

        // HIGH HUMIDITY
        if ($weather->humidity > 80) {
            $recommendation = 'Gunakan pakaian ringan dan tetap terhidrasi';
            $risk = 'medium';
            $insight = 'Udara terasa panas dan lembab';
        }

        // STRONG WIND
        if ($weather->wind_speed > 30) {
            $recommendation = 'Waspada angin kencang dan amankan barang ringan';
            $risk = 'medium';
            $insight = 'Angin kencang dapat mengganggu aktivitas';
        }

        // IDEAL WEATHER
        if (
            $weather->temperature >= 20 &&
            $weather->temperature <= 30 &&
            $weather->humidity < 70
        ) {
            $recommendation = 'Sangat cocok untuk aktivitas outdoor';
            $risk = 'low';
            $insight = 'Cuaca ideal untuk aktivitas luar ruangan';
        }

        return [
            'recommendation' => $recommendation,
            'risk' => $risk,
            'insight' => $insight,
        ];
    }
}
