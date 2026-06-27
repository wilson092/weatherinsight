<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherRule extends Model
{
    protected $fillable = [
        'name',
        'rule_type',
        'operator',
        'threshold_value',
        'min_value',
        'max_value',
        'score_weight',
        'description',
        'is_active',
    ];

    protected $appends = ['kondisi'];

    public function getKondisiAttribute(): string
    {
        $parameter = match ($this->rule_type) {
            'temperature' => 'Suhu',
            'humidity' => 'Kelembapan',
            'wind_speed' => 'Kecepatan Angin',
            'pressure' => 'Tekanan Udara',
            default => $this->rule_type,
        };

        $unit = match ($this->rule_type) {
            'temperature' => '°C',
            'humidity' => '%',
            'wind_speed' => 'm/s',
            'pressure' => 'hPa',
            default => '',
        };

        switch ($this->operator) {
            case '>':
                return "{$parameter} di atas {$this->threshold_value}{$unit}";
            case '<':
                return "{$parameter} di bawah {$this->threshold_value}{$unit}";
            case 'between':
                return "{$parameter} di antara {$this->min_value}{$unit} dan {$this->max_value}{$unit}";
            default:
                return 'Kondisi tidak valid';
        }
    }
}
