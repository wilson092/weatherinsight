@props(['latest'])

<style>
    .weather-map-popup .leaflet-popup-content-wrapper {
        background: #162233;
        color: #f8fafc;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 12px;
    }
    .weather-map-popup .leaflet-popup-content {
        margin: 1rem;
        color: #f8fafc;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    .weather-map-popup .leaflet-popup-tip {
        background: #162233;
    }
    .weather-map-popup .leaflet-container a {
        color: #4fd1ff;
    }
    .weather-map-popup strong {
        font-weight: 900;
        font-size: 1.125rem;
        color: #fff;
    }
    .weather-map-popup span {
        display: block;
        margin-top: 0.25rem;
    }
</style>

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

    $locationRows = [
        ['label' => 'City', 'value' => $latest?->city ? \Illuminate\Support\Str::title($latest->city) : 'Unavailable', 'icon' => 'heroicon-o-building-office-2', 'tone' => 'text-cyan-300'],
        ['label' => 'Country', 'value' => $latest?->country ?? 'Unavailable', 'icon' => 'heroicon-o-flag', 'tone' => 'text-sky-300'],
        ['label' => 'Latitude', 'value' => $latest?->latitude !== null ? number_format($latest->latitude, 4) : 'Unavailable', 'icon' => 'heroicon-o-map-pin', 'tone' => 'text-teal-300'],
        ['label' => 'Longitude', 'value' => $latest?->longitude !== null ? number_format($latest->longitude, 4) : 'Unavailable', 'icon' => 'heroicon-o-globe-alt', 'tone' => 'text-blue-300'],
        ['label' => 'Timezone', 'value' => $timezoneLabel, 'icon' => 'heroicon-o-clock', 'tone' => 'text-slate-300'],
    ];
@endphp

<section
    x-data="{
        unit: 'C',
        latest: {{ json_encode($latest) }},
        convertTemp(temp) {
            if (this.unit === 'F') {
                return (temp * 9/5) + 32;
            }
            return temp;
        },
        get temperature() {
            const temp = this.latest?.temperature;
            if (temp === null || temp === undefined) return 'N/A';
            return this.convertTemp(temp).toFixed(1) + (this.unit === 'F' ? '°F' : '°C');
        }
    }"
    @temperature-unit-changed.window="unit = $event.detail"
    aria-labelledby="weather-map-title"
    class="glass-panel overflow-hidden rounded-3xl p-5 transition duration-300 hover:border-cyan-400/50 sm:p-6"
>
    <div class="mb-4 flex items-center gap-2">
        <span class="flex h-8 w-8 items-center justify-center rounded-full border border-cyan-400/30 bg-cyan-400/10 text-cyan-300">
            <x-heroicon-o-map class="h-4 w-4" />
        </span>
        <h2 id="weather-map-title" class="text-base font-black text-white">Weather Map</h2>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_280px]">
        <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-950/40">
            @if($hasLocation)
                <div class="relative w-full" style="height: 365px;">
                    <div
                        id="{{ $mapId }}"
                        class="h-full w-full"
                        data-latitude="{{ (float) $latest->latitude }}"
                        data-longitude="{{ (float) $latest->longitude }}"
                        data-city="{{ $latest?->city ? \Illuminate\Support\Str::title($latest->city) : 'Unavailable' }}"
                        :data-temperature="temperature"
                        data-condition="{{ $latest->weather_description ?? $latest->weather_main ?? 'Unavailable' }}"
                        data-humidity="{{ number_format($latest->humidity, 0) }} %"
                        data-wind-speed="{{ number_format($latest->wind_speed, 1) }} m/s"
                    ></div>
                    <div data-map-status-for="{{ $mapId }}" class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center bg-slate-950/35 text-sm font-semibold text-slate-400">
                        Loading map...
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center px-6 text-center" style="height: 365px;">
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
        (() => {
            console.log('Loading map...');

            window.onerror = (message, source, lineno, colno, error) => {
                console.error('Weather map JavaScript error:', {
                    message,
                    source,
                    lineno,
                    colno,
                    error,
                });
            };

            const leafletCssUrl = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            const leafletJsUrl = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            const mapId = @js($mapId);
            const mapElement = document.getElementById(mapId);
            const statusElement = document.querySelector(`[data-map-status-for="${mapId}"]`);

            console.log('Weather map id:', mapId);
            console.log('Weather map element:', mapElement);
            console.log('Leaflet type before load:', typeof window.L);

            const setStatus = (message, isError = false) => {
                if (! statusElement) {
                    return;
                }

                statusElement.textContent = message;
                statusElement.classList.toggle('text-rose-300', isError);
                statusElement.classList.toggle('text-slate-400', ! isError);
                statusElement.classList.remove('hidden');
            };

            const hideStatus = () => {
                statusElement?.classList.add('hidden');
            };

            const ensureLeafletCss = () => {
                if (document.querySelector(`link[href="${leafletCssUrl}"]`)) {
                    return;
                }

                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = leafletCssUrl;
                link.crossOrigin = '';
                document.head.appendChild(link);
            };

            const ensureLeafletJs = () => new Promise((resolve, reject) => {
                if (window.L) {
                    resolve(window.L);
                    return;
                }

                const existingScript = document.querySelector(`script[src="${leafletJsUrl}"]`);

                if (existingScript) {
                    existingScript.addEventListener('load', () => resolve(window.L), { once: true });
                    existingScript.addEventListener('error', reject, { once: true });

                    let attempts = 0;
                    const interval = window.setInterval(() => {
                        attempts += 1;

                        if (window.L) {
                            window.clearInterval(interval);
                            resolve(window.L);
                        }

                        if (attempts > 60) {
                            window.clearInterval(interval);
                            reject(new Error('Leaflet did not become available.'));
                        }
                    }, 100);

                    return;
                }

                const script = document.createElement('script');
                script.src = leafletJsUrl;
                script.crossOrigin = '';
                script.onload = () => resolve(window.L);
                script.onerror = reject;
                document.head.appendChild(script);
            });

            const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
            }[character]));

            const initMap = async () => {
                console.log('initMap() called');

                if (! mapElement || mapElement.dataset.initialized === 'true') {
                    console.log('initMap() stopped:', {
                        mapElementFound: Boolean(mapElement),
                        initialized: mapElement?.dataset.initialized,
                    });
                    return;
                }

                setStatus('Loading map...');
                ensureLeafletCss();

                try {
                    await ensureLeafletJs();
                } catch (error) {
                    console.error('Leaflet failed to load:', error);
                    setStatus('Map library failed to load.', true);
                    return;
                }

                console.log('Leaflet type after load:', typeof window.L);

                if (! window.L) {
                    setStatus('Map library is unavailable.', true);
                    return;
                }

                mapElement.dataset.initialized = 'true';

                const weatherLocation = {
                    city: mapElement.dataset.city,
                    latitude: Number(mapElement.dataset.latitude),
                    longitude: Number(mapElement.dataset.longitude),
                    temperature: mapElement.dataset.temperature,
                    condition: mapElement.dataset.condition,
                    humidity: mapElement.dataset.humidity,
                    windSpeed: mapElement.dataset.windSpeed,
                };

                if (! Number.isFinite(weatherLocation.latitude) || ! Number.isFinite(weatherLocation.longitude)) {
                    console.error('Invalid weather map coordinates:', weatherLocation);
                    setStatus('Invalid map coordinates.', true);
                    return;
                }

                const map = window.L.map(mapElement, {
                    zoomControl: true,
                    scrollWheelZoom: false,
                }).setView([weatherLocation.latitude, weatherLocation.longitude], 11);

                console.log(map);

                const tileLayer = window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                console.log('Weather map tile layer created:', tileLayer);

                const popupContent = `
                    <div>
                        <strong>${escapeHtml(weatherLocation.city)}</strong>
                        <span>${escapeHtml(weatherLocation.temperature)}</span>
                        <span>${escapeHtml(weatherLocation.condition)}</span>
                        <span>Humidity: ${escapeHtml(weatherLocation.humidity)}</span>
                        <span>Wind: ${escapeHtml(weatherLocation.windSpeed)}</span>
                    </div>
                `;

                const marker = window.L.marker([weatherLocation.latitude, weatherLocation.longitude])
                    .addTo(map)
                    .bindPopup(popupContent, {
                        className: 'weather-map-popup'
                    })
                    .openPopup();

                console.log('Weather map marker created:', marker);

                window.setTimeout(() => {
                    map.invalidateSize();
                    hideStatus();
                }, 200);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMap, { once: true });
            } else {
                initMap();
            }
        })();
    </script>
@endif
