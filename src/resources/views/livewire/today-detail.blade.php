@php
    $isToday = !$selectedDay;
    $displayData = null;

    if ($isToday && $latest) {
        // Use the comprehensive data for the current day
        $displayData = [
            'dt' => $latest->recorded_at->timestamp,
            'temp' => $latest->temperature,
            'feels_like' => $latest->feels_like,
            'description' => $latest->weather_description,
            'icon' => $latest->weather_icon,
            'humidity' => $latest->humidity,
            'pressure' => $latest->pressure,
            'wind_speed' => $latest->wind_speed,
            'visibility' => $latest->visibility,
            'uvi' => $latest->uvi,
            'dew_point' => $latest->dew_point,
        ];
    } elseif ($selectedDay) {
        // Use the simplified forecast data for a future day
        $displayData = [
            'dt' => $selectedDay['dt'],
            'temp' => $selectedDay['temp']['max'], // Show max temp for the day
            'feels_like' => null, // Not available in daily forecast
            'description' => $selectedDay['weather'][0]['description'],
            'icon' => $selectedDay['weather'][0]['icon'],
            'humidity' => null, // Not available
            'pressure' => null, // Not available
            'wind_speed' => null, // Not available
            'visibility' => null, // Not available
            'uvi' => null, // Not available
            'dew_point' => null, // Not available
        ];
    }
@endphp

<div class="relative flex h-full flex-col overflow-hidden rounded-2xl glass-panel">
    @if($displayData)
        <!-- Background Image -->
        <div
            class="absolute inset-0 bg-cover bg-center transition-all duration-500"
            style="background-image: url('{{ $this->imageUrl }}');"
        >
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-slate-950/10"></div>
        </div>

        <!-- Main Content -->
        <div class="relative flex flex-1 flex-col justify-between p-5 text-white">
            <!-- Top Info -->
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold">
                        {{ \Carbon\Carbon::createFromTimestamp($displayData['dt'])->format('l') }}
                    </h2>
                    <p class="text-sm text-slate-300">
                        {{ \Carbon\Carbon::createFromTimestamp($displayData['dt'])->format('d M Y') }}
                    </p>
                </div>
                @if($isToday)
                    <span class="text-sm font-semibold">{{ now()->format('H:i') }}</span>
                @endif
            </div>

            <!-- Center Info -->
            <div class="my-4">
                <div class="flex items-center gap-4">
                    <img
                        src="https://openweathermap.org/img/wn/{{ $displayData['icon'] }}@4x.png"
                        alt="{{ $displayData['description'] }}"
                        class="-ml-4 h-28 w-28"
                    >
                    <div>
                        <p class="text-6xl font-bold">
                            {{ round($this->getConvertedTemperature($displayData['temp'])) }}°
                        </p>
                        <p class="text-lg font-semibold capitalize text-slate-200">
                            {{ $displayData['description'] }}
                        </p>
                        @if($displayData['feels_like'] !== null)
                        <p class="text-sm text-slate-300">
                            Feels like {{ round($this->getConvertedTemperature($displayData['feels_like'])) }}°
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Details Grid -->
        <div class="relative grid grid-cols-2 gap-px border-t border-white/10 bg-slate-900/50 p-4 sm:grid-cols-3">
            @php
                $details = [
                    ['icon' => 'heroicon-o-sun', 'label' => 'UV Index', 'value' => is_numeric($displayData['uvi']) ? round($displayData['uvi'], 1) : 'N/A'],
                    ['icon' => 'heroicon-o-eye', 'label' => 'Visibility', 'value' => is_numeric($displayData['visibility']) ? ($displayData['visibility'] / 1000) . ' km' : 'N/A'],
                    ['icon' => 'heroicon-o-bars-3-bottom-left', 'label' => 'Wind', 'value' => is_numeric($displayData['wind_speed']) ? round($displayData['wind_speed']) . ' km/h' : 'N/A'],
                    ['icon' => 'heroicon-o-beaker', 'label' => 'Humidity', 'value' => is_numeric($displayData['humidity']) ? $displayData['humidity'] . '%' : 'N/A'],
                    ['icon' => 'heroicon-o-arrow-down-on-square', 'label' => 'Pressure', 'value' => is_numeric($displayData['pressure']) ? $displayData['pressure'] . ' hPa' : 'N/A'],
                    ['icon' => 'heroicon-o-cloud-arrow-down', 'label' => 'Dew Point', 'value' => is_numeric($displayData['dew_point']) ? round($this->getConvertedTemperature($displayData['dew_point'])) . '°' : 'N/A'],
                ];
            @endphp

            @foreach($details as $detail)
                <div class="flex items-center gap-3 p-2 @if($detail['value'] === 'N/A') opacity-50 @endif">
                    <x-dynamic-component :component="$detail['icon']" class="h-6 w-6 text-cyan-300" />
                    <div>
                        <p class="text-xs text-slate-400">{{ $detail['label'] }}</p>
                        <p class="font-semibold text-white">{{ $detail['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex h-full items-center justify-center p-5 text-center">
            <p class="text-slate-400">Weather data is currently unavailable.</p>
        </div>
    @endif
</div>
