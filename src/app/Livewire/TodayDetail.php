<?php

namespace App\Livewire;

use App\Services\Weather\TemperatureConverter;
use App\Services\Weather\WeatherImageService;
use Livewire\Component;
use Livewire\Attributes\On;

class TodayDetail extends Component
{
    public $latest;
    public $selectedDay;
    public string $unit = 'C';

    protected $listeners = ['temperatureUnitChanged' => 'updateUnit'];

    public function mount($latest)
    {
        $this->latest = $latest;
        $this->selectedDay = null; // Initially, no day is selected from the tabs
        $this->unit = session()->get('temperature_unit', 'C');
    }

    public function updateUnit($unit)
    {
        $this->unit = $unit;
    }

    #[On('daySelected')]
    public function handleDaySelected($day)
    {
        $this->selectedDay = $day;
    }

    public function getImageUrlProperty(): string
    {
        $main = null;
        $description = null;

        if ($this->selectedDay) {
            $weather = $this->selectedDay['weather'][0] ?? null;
            if ($weather) {
                $main = $weather['main'];
                $description = $weather['description'];
            }
        } elseif ($this->latest) {
            $main = $this->latest->weather_main;
            $description = $this->latest->weather_description;
        }

        if (!$main) {
            return asset('/images/weather-bg/default.jpg');
        }

        return WeatherImageService::getImageUrl($main, $description);
    }

    public function getConvertedTemperature($temp)
    {
        if ($this->unit === 'F') {
            return TemperatureConverter::convert($temp, 'F');
        }
        return $temp;
    }

    public function formatUtcOffset(int $offsetInSeconds): string
    {
        $sign = $offsetInSeconds >= 0 ? '+' : '-';
        $absOffset = abs($offsetInSeconds);
        $hours = floor($absOffset / 3600);
        $minutes = floor(($absOffset % 3600) / 60);

        return sprintf('UTC%s%02d:%02d', $sign, $hours, $minutes);
    }

    public function getCarbonTimezoneString(int $offsetInSeconds): string
    {
        $sign = $offsetInSeconds >= 0 ? '+' : '-';
        $absOffset = abs($offsetInSeconds);
        $hours = floor($absOffset / 3600);
        $minutes = floor(($absOffset % 3600) / 60);

        return sprintf('%s%02d:%02d', $sign, $hours, $minutes);
    }

    public function render()
    {
        return view('livewire.today-detail');
    }
}
