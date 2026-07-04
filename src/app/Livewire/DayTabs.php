<?php

namespace App\Livewire;

use App\Services\Weather\TemperatureConverter;
use Livewire\Component;

class DayTabs extends Component
{
    public $forecast;
    public $selectedDayIndex = 0;
    public string $unit = 'C';

    protected $listeners = ['temperatureUnitChanged' => 'updateUnit'];

    public function mount($forecast)
    {
        $this->forecast = $forecast;
        $this->unit = session()->get('temperature_unit', 'C');
        
        // Dispatch the details of the initially selected day (today)
        if (isset($this->forecast['daily'][$this->selectedDayIndex])) {
            $this->dispatch('daySelected', $this->forecast['daily'][$this->selectedDayIndex]);
        }
    }

    public function updateUnit($unit)
    {
        $this->unit = $unit;
    }

    public function selectDay($index)
    {
        $this->selectedDayIndex = $index;
        
        // Dispatch an event with the selected day's forecast details
        if (isset($this->forecast['daily'][$index])) {
            $this->dispatch('daySelected', $this->forecast['daily'][$index]);
        }
    }

    public function getConvertedTemperature($temp)
    {
        if ($this->unit === 'F') {
            return TemperatureConverter::convert($temp, 'F');
        }
        return $temp;
    }

    public function render()
    {
        return view('livewire.day-tabs');
    }
}