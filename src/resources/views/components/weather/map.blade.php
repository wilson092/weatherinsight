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
@endphp

<section aria-labelledby="weather-map-title" class="glass-panel overflow-hidden rounded-3xl p-6 sm:p-8">
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Location Intelligence</p>
            <h2 id="weather-map-title" class="mt-2 text-2xl font-black text-white sm:text-3xl">Weather Map</h2>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-cyan-300/20 bg-cyan-400/10 text-cyan-200">
            <x-heroicon-o-map class="h-6 w-6" />
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <div class="relative min-h-80 overflow-hidden rounded-3xl border border-white/10 bg-slate-950/40 shadow-2xl shadow-slate-950/30 sm:min-h-96 lg:min-h-[28rem]">
            @if($hasLocation)
                <div id="{{ $mapId }}" class="h-80 w-full sm:h-96 lg:h-[28rem]"></div>
            @else
                <div class="flex h-80 flex-col items-center justify-center px-6 text-center sm:h-96 lg:h-[28rem]">
                    <x-heroicon-o-map-pin class="h-14 w-14 text-slate-600" />
                    <p class="mt-4 text-lg font-black text-white">Location unavailable</p>
                    <p class="mt-2 max-w-md text-sm leading-6 text-slate-500">
                        Search a city with valid OpenWeather coordinates to show the interactive map.
                    </p>
                </div>
            @endif
        </div>

        <aside class="rounded-3xl border border-white/10 bg-slate-950/25 p-5">
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-teal-300/20 bg-teal-400/10 text-teal-200">
                    <x-heroicon-o-information-circle class="h-5 w-5" />
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Map Info</p>
                    <h3 class="font-black text-white">{{ $latest?->city ?? 'No city data' }}</h3>
                </div>
            </div>

            <dl class="space-y-3">
                @foreach([
                    ['label' => 'Latitude', 'value' => $latest?->latitude !== null ? number_format($latest->latitude, 5) : '--'],
                    ['label' => 'Longitude', 'value' => $latest?->longitude !== null ? number_format($latest->longitude, 5) : '--'],
                    ['label' => 'Timezone', 'value' => $timezoneLabel],
                    ['label' => 'Country', 'value' => $latest?->country ?? '--'],
                ] as $item)
                    <div class="rounded-2xl border border-white/10 bg-white/[.035] px-4 py-3">
                        <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $item['label'] }}</dt>
                        <dd class="mt-1 break-words text-sm font-black text-white">{{ $item['value'] }}</dd>
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
                windSpeed: @js(number_format($latest->wind_speed, 1).' m/s'),
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
