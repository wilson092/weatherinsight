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
        ];
    } elseif ($selectedDay) {
        // Use the simplified forecast data for a future day
        $displayData = [
            'dt' => $selectedDay['dt'],
            'temp' => $selectedDay['temp']['current'], // Show representative temp for the day
            'feels_like' => null, // Not available in daily forecast
            'description' => $selectedDay['weather'][0]['description'],
            'icon' => $selectedDay['weather'][0]['icon'],
            'humidity' => $selectedDay['humidity'],
            'pressure' => $selectedDay['pressure'],
            'wind_speed' => $selectedDay['wind_speed'],
            'visibility' => null, // Not available, as per instruction to remove it
        ];
    }

    $timezoneString = null;
    if ($isToday && $latest) {
        if (is_numeric($latest->timezone)) {
            $offsetInSeconds = (int) $latest->timezone;
            $hours = intdiv($offsetInSeconds, 3600);
            $minutes = intdiv($offsetInSeconds % 3600, 60);
            $timezoneString = sprintf('%s%02d:%02d', $offsetInSeconds >= 0 ? '+' : '-', abs($hours), abs($minutes));
        } elseif (is_string($latest->timezone)) {
            // Handle if it's already a valid timezone string
            $timezoneString = $latest->timezone;
        }
    }

    // --- Risk badge resolution (driven by admin-managed Risk Categories) ---
    $riskBadge = null;
    if ($isToday && $latest && $latest->risk_level) {
        $riskKey = match (true) {
            str_contains(strtolower($latest->risk_level), 'high') || str_contains(strtolower($latest->risk_level), 'tinggi') => 'high',
            str_contains(strtolower($latest->risk_level), 'medium') || str_contains(strtolower($latest->risk_level), 'sedang') => 'medium',
            str_contains(strtolower($latest->risk_level), 'low') || str_contains(strtolower($latest->risk_level), 'rendah') => 'low',
            default => null,
        };

        if ($riskKey) {
            $riskBadge = match ($riskKey) {
                'high' => ['label' => 'High Risk', 'class' => 'bg-red-500/20 text-red-300'],
                'medium' => ['label' => 'Medium Risk', 'class' => 'bg-yellow-500/20 text-yellow-300'],
                'low' => ['label' => 'Low Risk', 'class' => 'bg-green-500/20 text-green-300'],
            };
        }
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
        <div class="relative flex flex-1 flex-col p-6 text-white">
            <!-- Top Info -->
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        {{ \Carbon\Carbon::createFromTimestamp($displayData['dt'])->format('l') }}
                    </h2>
                    <p class="text-sm text-slate-400">
                        {{ \Carbon\Carbon::createFromTimestamp($displayData['dt'])->format('d M Y') }}
                    </p>
                </div>
                @if($isToday && $latest)
                    <div class="flex flex-col items-end">
                        <span class="text-lg font-semibold text-slate-300">{{ now($this->getCarbonTimezoneString($latest->timezone))->format('H:i') }}</span>
                        <span class="text-sm text-slate-400">{{ $this->formatUtcOffset($latest->timezone) }}</span>
                    </div>
                @endif
            </div>

            <!-- Center 2-Column Layout -->
            <div class="flex-1 my-4 grid grid-cols-[1.2fr_0.8fr] items-center gap-4">
                <!-- Left Column: Weather Info -->
                <div class="flex items-center gap-4">
                    <img
                        src="https://openweathermap.org/img/wn/{{ $displayData['icon'] }}@4x.png"
                        alt="{{ $displayData['description'] }}"
                        class="-ml-4 h-36 w-36"
                    >
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                            <p class="text-7xl font-bold leading-none">
                                {{ round($this->getConvertedTemperature($displayData['temp'])) }}°
                            </p>
                            @if($riskBadge)
                                <span class="inline-block whitespace-nowrap rounded-full px-3 py-1 text-sm font-semibold {{ $riskBadge['class'] }}">
                                    {{ $riskBadge['label'] }}
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-xl font-semibold capitalize text-slate-200">
                            {{ $displayData['description'] }}
                        </p>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="border-l border-slate-700/50 pl-6">
                    @php
                        $details = [
                            ['icon' => 'heroicon-o-beaker', 'label' => 'Humidity', 'value' => is_numeric($displayData['humidity']) ? $displayData['humidity'] . '%' : 'N/A'],
                            ['icon' => 'heroicon-o-arrow-down-on-square', 'label' => 'Pressure', 'value' => is_numeric($displayData['pressure']) ? $displayData['pressure'] . ' hPa' : 'N/A'],
                            ['icon' => 'heroicon-o-bars-3-bottom-left', 'label' => 'Wind', 'value' => is_numeric($displayData['wind_speed']) ? round($displayData['wind_speed']) . ' km/h' : 'N/A'],
                        ];
                    @endphp

                    <div class="space-y-4">
                        @foreach($details as $index => $detail)
                            <div class="flex items-center gap-4 @if($detail['value'] === 'N/A') opacity-50 @endif">
                                <x-dynamic-component :component="$detail['icon']" class="h-7 w-7 text-cyan-300" />
                                <div class="flex-1">
                                    <p class="text-sm text-slate-400">{{ $detail['label'] }}</p>
                                    <p class="text-lg font-semibold text-white">{{ $detail['value'] }}</p>
                                </div>
                            </div>
                            @if($index < count($details) - 1)
                                <div class="border-b border-slate-800"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer: Sunrise/Sunset -->
    @else
        <div class="flex h-full items-center justify-center p-5 text-center">
            <p class="text-slate-400">Weather data is currently unavailable.</p>
        </div>
    @endif
</div>