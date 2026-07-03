<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071426">
    <title>Weather Intelligence Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            background-color: #020817;
        }

        body.weather-dashboard {
            background-color: #020817;
            background-image:
                linear-gradient(rgba(56, 189, 248, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, .035) 1px, transparent 1px),
                linear-gradient(150deg, #020817 0%, #071426 46%, #042f3f 100%);
            background-size: 48px 48px, 48px 48px, auto;
            color: #e2e8f0;
        }

        .glass-panel {
            background: linear-gradient(145deg, rgba(15, 23, 42, .84), rgba(15, 23, 42, .54));
            border: 1px solid rgba(255, 255, 255, .10);
            box-shadow: 0 22px 60px rgba(2, 8, 23, .34);
            backdrop-filter: blur(18px);
        }

        [x-cloak] {
            display: none !important;
        }

        .leaflet-container {
            background: #0f172a;
            color: #0f172a;
            font-family: inherit;
            height: 100%;
            overflow: hidden;
            position: relative;
            touch-action: pan-x pan-y;
            width: 100%;
        }

        .leaflet-pane,
        .leaflet-tile,
        .leaflet-marker-icon,
        .leaflet-marker-shadow,
        .leaflet-tile-container,
        .leaflet-pane > svg,
        .leaflet-pane > canvas,
        .leaflet-zoom-box,
        .leaflet-image-layer,
        .leaflet-layer {
            position: absolute;
            left: 0;
            top: 0;
        }

        .leaflet-tile,
        .leaflet-marker-icon,
        .leaflet-marker-shadow {
            user-select: none;
            -webkit-user-drag: none;
        }

        .leaflet-tile {
            filter: saturate(.9) contrast(.96) brightness(.92);
            max-width: none !important;
            max-height: none !important;
        }

        .leaflet-pane {
            z-index: 400;
        }

        .leaflet-map-pane {
            z-index: 400;
        }

        .leaflet-tile-pane {
            z-index: 200;
        }

        .leaflet-overlay-pane {
            z-index: 400;
        }

        .leaflet-shadow-pane {
            z-index: 500;
        }

        .leaflet-marker-pane {
            z-index: 600;
        }

        .leaflet-tooltip-pane {
            z-index: 650;
        }

        .leaflet-popup-pane {
            z-index: 700;
        }

        .leaflet-control {
            position: relative;
            z-index: 800;
            pointer-events: auto;
        }

        .leaflet-top,
        .leaflet-bottom {
            position: absolute;
            z-index: 1000;
            pointer-events: none;
        }

        .leaflet-top {
            top: 0;
        }

        .leaflet-right {
            right: 0;
        }

        .leaflet-bottom {
            bottom: 0;
        }

        .leaflet-left {
            left: 0;
        }

        .leaflet-control-container .leaflet-top,
        .leaflet-control-container .leaflet-bottom {
            transform: translate3d(0, 0, 0);
        }

        .leaflet-left .leaflet-control {
            margin-left: 10px;
        }

        .leaflet-top .leaflet-control {
            margin-top: 10px;
        }

        .leaflet-right .leaflet-control {
            margin-right: 10px;
        }

        .leaflet-bottom .leaflet-control {
            margin-bottom: 10px;
        }

        .leaflet-control-zoom a {
            background: rgba(15, 23, 42, .88);
            border-bottom: 1px solid rgba(255, 255, 255, .12);
            color: #e2e8f0;
            display: block;
            height: 30px;
            line-height: 30px;
            text-align: center;
            text-decoration: none;
            width: 30px;
        }

        .leaflet-control-attribution {
            background: rgba(15, 23, 42, .78) !important;
            color: #cbd5e1 !important;
        }

        .leaflet-control-attribution a {
            color: #67e8f9 !important;
        }

        .leaflet-popup-content-wrapper,
        .leaflet-popup-tip {
            background: rgba(15, 23, 42, .95);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, .14);
            box-shadow: 0 20px 45px rgba(2, 8, 23, .35);
        }

        .weather-map-popup {
            display: grid;
            gap: .25rem;
            min-width: 10rem;
        }

        .weather-map-popup strong {
            color: #fff;
            font-size: .95rem;
        }

        .weather-map-popup span {
            color: #cbd5e1;
            font-size: .78rem;
            text-transform: capitalize;
        }

    </style>
</head>
<body
    class="weather-dashboard min-h-screen text-slate-100 antialiased"
    x-data="{
        mobileMenuOpen: false,
        locating: false,
        detectLocation() {
            if (!navigator.geolocation || this.locating) return;
            this.locating = true;
            navigator.geolocation.getCurrentPosition(async (position) => {
                try {
                    const { latitude, longitude } = position.coords;
                    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}`);
                    const payload = await response.json();
                    const address = payload.address || {};
                    const city = address.city || address.town || address.municipality || address.county || address.state;
                    if (city) {
                        const input = document.querySelector('[data-primary-city-input]');
                        input.value = city;
                        input.form.submit();
                    }
                } finally {
                    this.locating = false;
                }
            }, () => {
                this.locating = false;
            });
        }
    }"
>
    <!-- Modern Navbar -->
    <nav class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
        <div class="mx-auto flex h-[72px] max-w-[1440px] items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="/" class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 flex-none items-center justify-center rounded-2xl bg-cyan-400 text-slate-950 shadow-lg shadow-cyan-950/40">
                    <x-heroicon-o-cloud class="h-5 w-5" />
                </div>
                <span class="truncate text-lg font-black text-white">WeatherInsight</span>
            </a>

            <div class="hidden h-full items-center gap-7 lg:flex">
                <a href="/" class="relative flex h-full items-center text-sm font-bold text-white">
                    Dashboard
                    <span class="absolute bottom-0 left-0 h-0.5 w-full bg-cyan-400"></span>
                </a>
                <a href="/comparison" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">Comparison</a>
                <a href="/leaderboard" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">Leaderboard</a>
                <a href="/history" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">History</a>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                <form method="GET" action="/" class="relative hidden md:block">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        name="city"
                        value="{{ request('city', 'Jakarta') }}"
                        placeholder="Search city..."
                        class="h-10 w-48 rounded-full border border-white/10 bg-slate-900/70 pl-10 pr-10 text-sm text-white placeholder:text-slate-500 transition focus:w-64 focus:border-cyan-400 focus:ring-cyan-400"
                    >
                    <button type="submit" class="absolute right-1.5 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-full bg-slate-800 text-slate-300 transition hover:bg-cyan-400 hover:text-slate-950" title="Search">
                        <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                    </button>
                </form>

                <button type="button" class="relative hidden h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-900/70 text-slate-300 transition hover:border-cyan-400 hover:text-cyan-300 sm:flex" title="Notifications">
                    <x-heroicon-o-bell class="h-5 w-5" />
                    <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-400"></span>
                </button>

                <div class="hidden items-center gap-2 sm:flex">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-900 text-sm font-bold text-slate-300">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden max-w-24 truncate text-sm font-bold text-white xl:block">{{ auth()->user()->name }}</span>
                </div>

                <form method="POST" action="/logout" class="hidden sm:block">
                    @csrf
                    <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-900/70 text-slate-300 transition hover:border-rose-400/50 hover:text-rose-300" title="Logout">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="h-5 w-5" />
                    </button>
                </form>

                <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-900/70 text-white transition hover:border-cyan-400 lg:hidden" title="Menu">
                    <x-heroicon-o-bars-3 x-show="!mobileMenuOpen" class="h-5 w-5" />
                    <x-heroicon-o-x-mark x-show="mobileMenuOpen" x-cloak class="h-5 w-5" />
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="fixed inset-x-0 top-20 z-40 px-4 sm:px-6 lg:hidden">
        <div class="glass-panel mx-auto max-w-7xl overflow-hidden rounded-3xl">
            <div class="flex flex-col p-4">
                <!-- Mobile Search -->
                <form method="GET" action="/" class="relative mb-4 md:hidden">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        name="city"
                        value="{{ request('city', 'Jakarta') }}"
                        placeholder="Search city..."
                        class="w-full rounded-full border border-white/10 bg-slate-950/40 py-2 pl-9 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400"
                    >
                </form>

                <!-- Mobile User Info -->
                <div class="mb-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/25 p-3 sm:hidden">
                    <x-heroicon-o-user-circle class="h-6 w-6 text-cyan-300" />
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">User</p>
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    </div>
                </div>

                <!-- Mobile Navigation Links -->
                <div class="space-y-1">
                    <a href="/" class="block rounded-xl bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
                        Dashboard
                    </a>
                    <a href="/comparison" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Comparison
                    </a>
                    <a href="/leaderboard" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Leaderboard
                    </a>
                    <a href="/history" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        History
                    </a>
                </div>

                <!-- Mobile Logout -->
                <form method="POST" action="/logout" class="mt-4 sm:hidden">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-300 transition hover:bg-rose-500 hover:text-white">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="h-5 w-5" />
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-[1440px] px-4 py-5 sm:px-6 lg:px-8">
        <form method="GET" action="/" class="glass-panel mb-4 rounded-3xl p-2.5">
            @if(request('compare_city'))
                <input type="hidden" name="compare_city" value="{{ request('compare_city') }}">
            @endif
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <label class="relative flex-1">
                    <span class="sr-only">Search primary city</span>
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        name="city"
                        data-primary-city-input
                        value="{{ request('city', 'Jakarta') }}"
                        placeholder="Search city..."
                        class="h-12 w-full rounded-2xl border border-transparent bg-slate-950/40 pl-13 pr-4 text-sm text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                    >
                </label>
                <button type="button" @click="detectLocation()" class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl px-5 text-sm font-bold text-cyan-300 transition hover:bg-cyan-400/10 hover:text-cyan-200">
                    <x-heroicon-o-map-pin class="h-5 w-5" />
                    <span x-text="locating ? 'Detecting...' : 'Detect My Location'"></span>
                </button>
                <button type="submit" class="inline-flex h-12 items-center justify-center rounded-2xl bg-cyan-400 px-6 text-sm font-black text-slate-950 shadow-lg shadow-cyan-950/30 transition hover:bg-cyan-300">
                    Search
                </button>
            </div>
        </form>

        <div class="mb-4 flex items-center justify-end">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-950/40 px-3 py-1.5 text-xs text-slate-400">
                <x-heroicon-o-clock class="h-4 w-4 text-cyan-300" />
                <span>Last update:</span>
                <strong class="font-semibold text-white">{{ $latest?->recorded_at?->format('d M Y, H:i') ?? 'Unavailable' }}</strong>
            </div>
        </div>

        <main class="space-y-5">
            <!-- Hero Section: Current Weather + Hourly Forecast -->
            <div class="grid gap-5 xl:grid-cols-[1.02fr_1fr]">
                <x-weather.current-weather :latest="$latest" />
                <x-weather.hourly-forecast :forecast="$forecast" />
            </div>

            <div class="grid gap-5 xl:grid-cols-[1.55fr_1fr]">
                <x-weather.forecast :forecast="$forecast" />
                <x-weather.risk-analysis :latest="$latest" :analysis="$riskAnalysis" />
            </div>

            <div class="grid gap-5 xl:grid-cols-[1.55fr_1fr]">
                <x-weather.map :latest="$latest" />
                <x-weather.alert-center :alerts="$alerts" :riskAnalysis="$riskAnalysis" :latest="$latest" />
            </div>
        </main>

        <footer class="py-8 text-center text-sm text-slate-500">
            &copy; {{ now()->year }} WeatherInsight. All rights reserved.
        </footer>
    </div>
</body>
</html>
