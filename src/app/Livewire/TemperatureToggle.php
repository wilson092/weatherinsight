<?php

namespace App\Livewire;

use Livewire\Component;

class TemperatureToggle extends Component
{
    public string $unit = 'C';

    public function mount()
    {
        $this->unit = session()->get('temperature_unit', 'C');
    }

    public function toggleUnit()
    {
        $this->unit = ($this->unit === 'C') ? 'F' : 'C';
        session()->put('temperature_unit', $this->unit);
        $this->dispatch('temperatureUnitChanged', unit: $this->unit);
    }

    public function render()
    {
        return view('livewire.temperature-toggle');
    }
}
