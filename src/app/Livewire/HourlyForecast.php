<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class HourlyForecast extends Component
{
    public array $forecast;
    public string $unit = 'C';

    #[On('temperature-unit-changed')]
    public function updateUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function render()
    {
        return view('livewire.hourly-forecast');
    }
}