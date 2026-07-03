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
    
    // Calculate sunrise & sunset (mock data - in real app this would come from API)
    // OpenWeather API returns sys.sunrise and sys.sunset in Unix timestamp
    $sunrise = 'N/A';
    $sunset = 'N/A';
    
    // Note: In actual implementation, you would get this from the API response
    // For now, we'll use placeholder values
@endphp

<section aria-labelledby="weather-map-title" class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
    <!-- Background decorations -->
    <div class="absolute right-0 top-0 h-64 w-64 rounded-full bg-teal-400/10 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/4 h-56 w-56 rounded-full bg-cyan-400/10 blur-3xl"></div>

    <div class="relative">
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Location Intelligence</p>
                <h2 id="weather-map-title" class="mt-2 text-3xl font-black text-white sm:text-4xl">Weather Map</h2>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-cyan-300/20 bg-gradient-to-br from-cyan-400/20 to-teal-400/10 text-cyan-200 shadow-lg shadow-cyan-950/30">
                <x-heroicon-o-map class="h-7 w-7" />
            </div>
        </div>

        <!-- Map & Location Info Grid -->
        <div class="grid gap-6 lg:grid-cols-[1fr_380px]">
            <!-- Map Container -->
            <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-950/40 shadow-2xl shadow-slate-950/30">
                @if($hasLocation)
                    <div id="{{ $mapId }}" class="h-96 w-full lg:h-[32rem]"></div>
                @else
                    <div class="flex h-96 flex-col items-center justify-center px-6 text-center lg:h-[32rem]">
                        <div class="relative">
                            <div class="absolute inset-0 rounded-full bg-slate-600/20 blur-2xl"></div>
                            <x-heroicon-o-map-pin class="relative h-20 w-20 text-slate-600" />
                        </div>
                        <p class="mt-6 text-xl font-black text-white">Location Unavailable</p>
                        <p class="mt-3 max-w-md text-sm leading-6 text-slate-400">
                            Search for a city with valid coordinates to display the interactive weather map and location details.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Location Information Card -->
            <aside class="flex flex-col gap-4">
                <!-- City Header -->
                <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-slate-950/60 to-slate-900/40 p-6 backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-none items-center justify-center rounded-xl border border-teal-300/20 bg-gradient-to-br from-teal-400/20 to-cyan-400/10 text-teal-200">
                            <x-heroicon-o-map-pin class="h-6 w-6" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-cyan-300">Current Location</p>
                            <h3 class="mt-1 truncate text-2xl font-black text-white">{{ $latest?->city ?? 'No city data' }}</h3>
                            @if($latest?->country)
                                <p class="mt-1 text-sm font-semibold text-slate-400">{{ $latest->country }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Coordinates & Details -->
                <div class="space-y-3 rounded-3xl border border-white/10 bg-gradient-to-br from-slate-950/60 to-slate-900/40 p-6 backdrop-blur-sm">
                    <h4 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-400">Geographic Details</h4>
                    
                    <!-- Latitude -->
                    <div class="group rounded-2xl border border-white/10 bg-white/[.04] p-4 transition-all hover:border-cyan-300/30 hover:bg-white/[.08]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-sky-400/10 text-sky-300 transition-colors group-hover:bg-sky-400/20">
                                <x-heroicon-o-arrow-up class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Latitude</dt>
                                <dd class="mt-0.5 truncate text-base font-black text-white">
                                    {{ $latest?->latitude !== null ? number_format($latest->latitude, 5) : '--' }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Longitude -->
                    <div class="group rounded-2xl border border-white/10 bg-white/[.04] p-4 transition-all hover:border-cyan-300/30 hover:bg-white/[.08]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-teal-400/10 text-teal-300 transition-colors group-hover:bg-teal-400/20">
                                <x-heroicon-o-arrow-right class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Longitude</dt>
                                <dd class="mt-0.5 truncate text-base font-black text-white">
                                    {{ $latest?->longitude !== null ? number_format($latest->longitude, 5) : '--' }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- Timezone -->
                    <div class="group rounded-2xl border border-white/10 bg-white/[.04] p-4 transition-all hover:border-cyan-300/30 hover:bg-white/[.08]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-cyan-400/10 text-cyan-300 transition-colors group-hover:bg-cyan-400/20">
                                <x-heroicon-o-clock class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Timezone</dt>
                                <dd class="mt-0.5 truncate text-base font-black text-white">{{ $timezoneLabel }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Sunrise (Placeholder) -->
                    <div class="group rounded-2xl border border-white/10 bg-white/[.04] p-4 transition-all hover:border-cyan-300/30 hover:bg-white/[.08]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-orange-400/10 text-orange-300 transition-colors group-hover:bg-orange-400/20">
                                <x-heroicon-o-sun class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Sunrise</dt>
                                <dd class="mt-0.5 truncate text-base font-black text-white">{{ $sunrise }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- Sunset (Placeholder) -->
                    <div class="group rounded-2xl border border-white/10 bg-white/[.04] p-4 transition-all hover:border-cyan-300/30 hover:bg-white/[.08]">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 flex-none items-center justify-center rounded-xl bg-indigo-400/10 text-indigo-300 transition-colors group-hover:bg-indigo-400/20">
                                <x-heroicon-o-moon class="h-5 w-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <dt class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Sunset</dt>
                                <dd class="mt-0.5 truncate text-base font-black text-white">{{ $sunset }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
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
                '&': '&',
                '<': '<',
                '>': '>',
                '"': '"',
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