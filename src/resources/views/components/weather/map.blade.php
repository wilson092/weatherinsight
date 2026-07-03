@props(['latest'])

@php
    $hasLocation = $latest?->latitude !== null && $latest?->longitude !== null;
    $mapId = 'weather-map-'.($latest?->id ?? 'empty');
    $timezoneOffset = $latest?->timezone;
    $timezoneLabel = 'Unavailable';

    if ($timezoneOffset !== null) {
        $sign = $timezoneOffset >= 0 ? '+' : '-';
        $absoluteOffset = abs((int) $timezoneOffset);
        $hours = intdiv($absoluteOffset, 3600);
        $minutes = intdiv($absoluteOffset % 3600, 60);
        $timezoneLabel = 'UTC'.$sign.str_pad((string) $hours, 2, '0', STR_PAD_LEFT).':'.str_pad((string) $minutes, 2, '0', STR_PAD_LEFT);
    }

    $sunrise = $latest?->getAttribute('sunrise');
    $sunset = $latest?->getAttribute('sunset');
    $locationRows = [
        ['label' => 'City', 'value' => $latest?->city ?? 'Unavailable', 'icon' => 'heroicon-o-building-office-2', 'tone' => 'text-cyan-300'],
        ['label' => 'Country', 'value' => $latest?->country ?? 'Unavailable', 'icon' => 'heroicon-o-flag', 'tone' => 'text-sky-300'],
        ['label' => 'Latitude', 'value' => $latest?->latitude !== null ? number_format($latest->latitude, 4) : 'Unavailable', 'icon' => 'heroicon-o-map-pin', 'tone' => 'text-teal-300'],
        ['label' => 'Longitude', 'value' => $latest?->longitude !== null ? number_format($latest->longitude, 4) : 'Unavailable', 'icon' => 'heroicon-o-globe-alt', 'tone' => 'text-blue-300'],
        ['label' => 'Timezone', 'value' => $timezoneLabel, 'icon' => 'heroicon-o-clock', 'tone' => 'text-slate-300'],
        ['label' => 'Sunrise', 'value' => $sunrise ? \Carbon\Carbon::parse($sunrise)->format('H:i') : 'Unavailable', 'icon' => 'heroicon-o-sun', 'tone' => 'text-amber-300'],
        ['label' => 'Sunset', 'value' => $sunset ? \Carbon\Carbon::parse($sunset)->format('H:i') : 'Unavailable', 'icon' => 'heroicon-o-moon', 'tone' => 'text-indigo-300'],
    ];
@endphp

<section aria-labelledby="weather-map-title" class="glass-panel overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6">
    <div class="mb-4 flex items-center gap-2">
        <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
            <x-heroicon-o-map class="h-4 w-4" />
        </span>
        <h2 id="weather-map-title" class="text-base font-black text-white">Weather Map</h2>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40">
            @if($hasLocation)
                <div id="{{ $mapId }}" class="h-[300px] w-full sm:h-[330px]"></div>
            @else
                <div class="flex h-[300px] flex-col items-center justify-center px-6 text-center sm:h-[330px]">
                    <x-heroicon-o-map-pin class="h-14 w-14 text-slate-600" />
                    <p class="mt-4 text-lg font-black text-white">Location Unavailable</p>
                    <p class="mt-2 max-w-md text-sm leading-6 text-slate-400">
                        Search for a city with valid coordinates to display the interactive weather map.
                    </p>
                </div>
            @endif
        </div>

        <aside class="rounded-2xl border border-white/10 bg-slate-950/28 p-4">
            <h3 class="mb-4 text-sm font-black text-white">Location Information</h3>
            <dl class="space-y-2.5">
                @foreach($locationRows as $row)
                    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-xl px-2 py-1.5 transition hover:bg-white/[.04]">
                        <dt class="flex h-7 w-7 items-center justify-center rounded-full bg-white/[.06]">
                            <x-dynamic-component :component="$row['icon']" class="h-4 w-4 {{ $row['tone'] }}" />
                        </dt>
                        <dd class="text-xs text-slate-400">{{ $row['label'] }}</dd>
                        <dd class="max-w-32 truncate text-right text-xs font-bold text-white">{{ $row['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </aside>
    </div>
</section>

@if($hasLocation)
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (! window.L) {
                return;
            }

            const mapElement = document.getElementById(@js($mapId));

            if (! mapElement || mapElement.dataset.initialized === 'true') {
                return;
            }

            mapElement.dataset.initialized = 'true';

            const weatherLocation = {
                city: @js($latest->city),
                latitude: @js((float) $latest->latitude),
                longitude: @js((float) $latest->longitude),
                temperature: @js(number_format($latest->temperature, 1).' °C'),
                condition: @js($latest->weather_description ?? $latest->weather_main ?? 'Unavailable'),
                humidity: @js(number_format($latest->humidity, 0).' %'),
                windSpeed: @js(number_format($latest->wind_speed * 3.6, 0).' km/h'),
            };

            const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[character]));

            const map = L.map(mapElement, {
                zoomControl: true,
                scrollWheelZoom: false,
            }).setView([weatherLocation.latitude, weatherLocation.longitude], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            const popupContent = `
                <div class="weather-map-popup">
                    <strong>${escapeHtml(weatherLocation.city)}</strong>
                    <span>${escapeHtml(weatherLocation.temperature)}</span>
                    <span>${escapeHtml(weatherLocation.condition)}</span>
                    <span>Humidity: ${escapeHtml(weatherLocation.humidity)}</span>
                    <span>Wind: ${escapeHtml(weatherLocation.windSpeed)}</span>
                </div>
            `;

            L.marker([weatherLocation.latitude, weatherLocation.longitude])
                .addTo(map)
                .bindPopup(popupContent)
                .openPopup();

            window.setTimeout(() => map.invalidateSize(), 150);
        });
    </script>
@endif
