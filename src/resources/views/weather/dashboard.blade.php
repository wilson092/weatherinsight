<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071426">
    <title>Weather Intelligence Dashboard</title>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    />
    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
        defer
    ></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html {
            background-color: #06111f;
        }

        body.weather-dashboard {
            background-color: #06111f;
            background-image:
                linear-gradient(rgba(148, 163, 184, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, .035) 1px, transparent 1px),
                radial-gradient(circle at 10% 10%, rgba(14, 165, 233, .18), transparent 28rem),
                radial-gradient(circle at 90% 20%, rgba(45, 212, 191, .14), transparent 30rem),
                linear-gradient(145deg, #06111f 0%, #0b1f35 48%, #071426 100%);
            background-size: 42px 42px, 42px 42px, auto, auto, auto;
            color: #e2e8f0;
        }

        .glass-panel {
            background: linear-gradient(135deg, rgba(255, 255, 255, .10), rgba(255, 255, 255, .045));
            border: 1px solid rgba(255, 255, 255, .13);
            box-shadow: 0 24px 70px rgba(2, 8, 23, .28);
            backdrop-filter: blur(20px);
        }

        .leaflet-container {
            background: #0f172a;
            color: #0f172a;
            font-family: inherit;
            overflow: hidden;
            touch-action: pan-x pan-y;
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
<body class="weather-dashboard min-h-screen text-slate-100 antialiased">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        <header class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-teal-400 shadow-lg shadow-cyan-950/40">
                    <x-heroicon-o-cloud class="h-8 w-8 text-slate-950" />
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Monitoring Console</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight text-white sm:text-3xl">WeatherInsight</h1>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="glass-panel flex items-center gap-3 rounded-2xl px-4 py-3">
                    <x-heroicon-o-user-circle class="h-6 w-6 text-cyan-300" />
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Analyst</p>
                        <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    </div>
                </div>

                <div class="glass-panel flex items-center gap-3 rounded-2xl px-4 py-3">
                    <x-heroicon-o-clock class="h-6 w-6 text-teal-300" />
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last update</p>
                        <p class="text-sm font-semibold text-white">{{ $latest?->recorded_at?->format('d M Y, H:i') ?? 'Unavailable' }}</p>
                    </div>
                </div>

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-2xl border border-rose-400/20 bg-rose-500/10 text-rose-300 transition hover:bg-rose-500 hover:text-white" title="Logout">
                        <x-heroicon-o-arrow-right-start-on-rectangle class="h-6 w-6" />
                    </button>
                </form>
            </div>
        </header>

        <form method="GET" action="/" class="glass-panel mb-8 rounded-3xl p-3 sm:p-4">
            @if(request('compare_city'))
                <input type="hidden" name="compare_city" value="{{ request('compare_city') }}">
            @endif
            <div class="flex flex-col gap-3 sm:flex-row">
                <label class="relative flex-1">
                    <span class="sr-only">Search primary city</span>
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input
                        type="text"
                        name="city"
                        value="{{ request('city', 'Jakarta') }}"
                        placeholder="Search a monitored city"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 py-3.5 pl-12 pr-4 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                    >
                </label>
                <button type="submit" class="rounded-2xl bg-gradient-to-r from-cyan-400 to-teal-400 px-7 py-3.5 font-bold text-slate-950 shadow-lg shadow-cyan-950/30 transition hover:brightness-110">
                    Update Intelligence
                </button>
            </div>
        </form>

        <main class="space-y-8">
            <x-weather.current-weather :latest="$latest" />
            <x-weather.risk-analysis :latest="$latest" :analysis="$riskAnalysis" />
            <x-weather.alert-center :alerts="$alerts" />
            <x-weather.comparison :latest="$latest" :comparison="$comparison" :city="$city" />
            <x-weather.forecast :forecast="$forecast" />
            <x-weather.map :latest="$latest" />
            <x-weather.leaderboard :leaderboards="$leaderboards" />
            <x-weather.history :history="$history" />
        </main>

        <footer class="py-10 text-center text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
            Weather Intelligence Monitoring System
        </footer>
    </div>
</body>
</html>
