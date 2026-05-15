<?php

namespace App\Services\Weather;

use App\Models\WeatherHistory;

class WeatherRuleEngineService
{
    public function analyze(WeatherHistory $weather): array
{
    $temp = $weather->temperature;

    /*
    |--------------------------------------------------------------------------
    | HIGH RISK
    |--------------------------------------------------------------------------
    */

    if ($temp >= 33) {

        return [
            'risk' => 'HIGH',
            'recommendation' => 'Hindari aktivitas di luar ruangan saat siang hari',
            'insight' => 'Suhu sangat panas dan berisiko menyebabkan dehidrasi',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MEDIUM RISK
    |--------------------------------------------------------------------------
    */

    if ($temp >= 30) {

        return [
            'risk' => 'MEDIUM',
            'recommendation' => 'Gunakan pakaian ringan dan tetap terhidrasi',
            'insight' => 'Udara terasa panas dan cukup lembab',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | LOW RISK
    |--------------------------------------------------------------------------
    */

    return [
        'risk' => 'LOW',
        'recommendation' => 'Cuaca normal untuk aktivitas harian',
        'insight' => 'Tidak ada risiko cuaca signifikan',
    ];
}
}
