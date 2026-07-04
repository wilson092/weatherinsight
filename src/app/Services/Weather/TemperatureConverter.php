<?php

namespace App\Services\Weather;

class TemperatureConverter
{
    public static function convert(float $temperature, string $toUnit, string $fromUnit = 'C'): float
    {
        if ($fromUnit === $toUnit) {
            return $temperature;
        }

        if ($toUnit === 'F') {
            // Celsius to Fahrenheit
            return ($temperature * 9/5) + 32;
        }

        if ($toUnit === 'C') {
            // Fahrenheit to Celsius
            return ($temperature - 32) * 5/9;
        }

        return $temperature;
    }
}