<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071426">
    <title>Weather Comparison - WeatherInsight</title>

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
    </style>
</head>
<body class="weather-dashboard min-h-screen text-slate-100 antialiased" x-data="{ mobileMenuOpen: false }">
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
                <a href="/" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">Dashboard</a>
                <a href="/comparison" class="relative flex h-full items-center text-sm font-bold text-white">
                    Comparison
                    <span class="absolute bottom-0 left-0 h-0.5 w-full bg-cyan-400"></span>
                </a>
                @auth
                    <a href="/leaderboard" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">Leaderboard</a>
                    <a href="/history" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">History</a>
                @else
                    <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.')" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">
                        Leaderboard
                    </button>
                    <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.')" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">
                        History
                    </button>
                @endauth
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @livewire('temperature-toggle')

                @auth
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
                @else
                    <div class="hidden items-center gap-2 sm:flex">
                        <a href="/login" class="inline-flex h-10 items-center rounded-full border border-white/15 px-4 text-sm font-semibold text-white/90 transition hover:border-white/30 hover:bg-white/5">
                            Login
                        </a>
                        <a href="/register" class="inline-flex h-10 items-center rounded-full bg-cyan-400 px-4 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                            Sign Up
                        </a>
                    </div>

                    <div class="flex items-center gap-2 sm:hidden">
                        <a href="/login" class="inline-flex h-9 items-center rounded-full border border-white/15 px-3 text-xs font-semibold text-white/90 transition hover:border-white/30 hover:bg-white/5">
                            Login
                        </a>
                        <a href="/register" class="inline-flex h-9 items-center rounded-full bg-cyan-400 px-3 text-xs font-semibold text-slate-950 transition hover:bg-cyan-300">
                            Sign Up
                        </a>
                    </div>
                @endauth

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
                @auth
                    <!-- Mobile User Info -->
                    <div class="mb-4 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/25 p-3 sm:hidden">
                        <x-heroicon-o-user-circle class="h-6 w-6 text-cyan-300" />
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">User</p>
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                @else
                    <a href="/login" class="mb-4 flex items-center justify-center gap-2 rounded-2xl border border-cyan-400/30 bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-200 transition hover:bg-cyan-400/20 sm:hidden">
                        Sign In
                    </a>
                @endauth

                <!-- Mobile Navigation Links -->
                <div class="space-y-1">
                    <a href="/" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Dashboard
                    </a>
                    <a href="/comparison" class="block rounded-xl bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
                        Comparison
                    </a>
                    @auth
                        <a href="/leaderboard" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            Leaderboard
                        </a>
                        <a href="/history" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            History
                        </a>
                    @else
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.'); mobileMenuOpen = false" class="block w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            Leaderboard
                        </button>
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.'); mobileMenuOpen = false" class="block w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            History
                        </button>
                    @endauth
                </div>

                @auth
                    <!-- Mobile Logout -->
                    <form method="POST" action="/logout" class="mt-4 sm:hidden">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm font-semibold text-rose-300 transition hover:bg-rose-500 hover:text-white">
                            <x-heroicon-o-arrow-right-start-on-rectangle class="h-5 w-5" />
                            <span>Logout</span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-white sm:text-4xl">Weather Comparison</h1>
            <p class="mt-2 text-slate-400">Compare weather conditions between two cities and analyze the differences</p>
        </div>

        <!-- Search Form -->
        <form method="GET" action="/comparison" class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5 mb-8">
            <div class="grid gap-4 sm:grid-cols-2">
                <!-- Primary City -->
                <div>
                    <label for="primary_city" class="mb-2 block text-sm font-semibold text-slate-300">Primary City</label>
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            id="primary_city"
                            name="primary_city"
                            value="{{ $primaryCity }}"
                            placeholder="Enter primary city"
                            class="w-full rounded-full border border-white/10 bg-slate-950/40 py-3.5 pl-12 pr-4 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                        >
                    </div>
                </div>

                <!-- Comparison City -->
                <div>
                    <label for="comparison_city" class="mb-2 block text-sm font-semibold text-slate-300">Comparison City</label>
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                        <input
                            type="text"
                            id="comparison_city"
                            name="comparison_city"
                            value="{{ $comparisonCity }}"
                            placeholder="Enter comparison city"
                            class="w-full rounded-full border border-white/10 bg-slate-950/40 py-3.5 pl-12 pr-4 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                        >
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-4 w-full rounded-lg bg-cyan-500 px-7 py-3.5 font-semibold text-white shadow-lg shadow-cyan-950/30 transition hover:bg-cyan-600 sm:w-auto">
                <span class="flex items-center justify-center gap-2">
                    <x-heroicon-o-arrows-right-left class="h-5 w-5" />
                    Compare Cities
                </span>
            </button>
        </form>

        @if($primaryWeather && $comparisonWeather)
            <!-- Comparison Grid -->
            <div class="mb-8 grid gap-6 lg:grid-cols-2">
                <!-- Primary City Card -->
                <x-weather.comparison-card 
                    :weather="$primaryWeather"
                    :analysis="$primaryAnalysis"
                    label="Primary City"
                    accentColor="cyan"
                />

                <!-- Comparison City Card -->
                <x-weather.comparison-card 
                    :weather="$comparisonWeather"
                    :analysis="$comparisonAnalysis"
                    label="Comparison City"
                    accentColor="teal"
                />
            </div>

            <!-- Comparison Summary -->
            <section class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-6 sm:p-8">
                <div>
                    @php
                        $temperatureDiff = $comparisonWeather->temperature - $primaryWeather->temperature;
                        $humidityDiff = $comparisonWeather->humidity - $primaryWeather->humidity;
                        $windDiff = $comparisonWeather->wind_speed - $primaryWeather->wind_speed;
                        $riskRank = ['Low' => 1, 'Medium' => 2, 'High' => 3, 'Extreme' => 4];
                        $primaryRisk = $primaryAnalysis['risk'] ?? $primaryWeather->risk_category?->name ?? ucfirst($primaryWeather->risk_level ?? 'low').' Risk';
                        $comparisonRisk = $comparisonAnalysis['risk'] ?? $comparisonWeather->risk_category?->name ?? ucfirst($comparisonWeather->risk_level ?? 'low').' Risk';
                        $riskKey = fn ($risk) => match (true) {
                            str_contains(strtolower($risk), 'extreme') => 'Extreme',
                            str_contains(strtolower($risk), 'high') => 'High',
                            str_contains(strtolower($risk), 'medium') => 'Medium',
                            default => 'Low',
                        };
                        $riskDiff = ($riskRank[$riskKey($comparisonRisk)] ?? 0) <=> ($riskRank[$riskKey($primaryRisk)] ?? 0);
                        $badgeItems = [
                            [
                                'icon' => 'heroicon-o-fire',
                                'label' => $temperatureDiff === 0
                                    ? 'Similar Temperatures'
                                    : (
                                        $temperatureDiff > 0
                                            ? $comparisonWeather->city . ' is ' . number_format(abs($temperatureDiff), 1) . '°C Warmer'
                                            : $comparisonWeather->city . ' is ' . number_format(abs($temperatureDiff), 1) . '°C Cooler'
                                    ),
                                'tone' => $temperatureDiff === 0
                                    ? 'border-slate-400/30 bg-slate-500/10 text-slate-300'
                                    : ($temperatureDiff > 0
                                        ? 'border-orange-400/30 bg-orange-500/10 text-orange-300'
                                        : 'border-cyan-400/30 bg-cyan-500/10 text-cyan-300'),
                            ],
                            [
                                'icon' => 'heroicon-o-beaker',
                                'label' => $humidityDiff === 0
                                    ? 'Similar Humidity'
                                    : (
                                        $humidityDiff > 0
                                            ? $comparisonWeather->city . ' is ' . number_format(abs($humidityDiff), 0) . '% More Humid'
                                            : $comparisonWeather->city . ' is ' . number_format(abs($humidityDiff), 0) . '% Drier'
                                    ),
                                'tone' => $humidityDiff === 0
                                    ? 'border-slate-400/30 bg-slate-500/10 text-slate-300'
                                    : ($humidityDiff > 0
                                        ? 'border-sky-400/30 bg-sky-500/10 text-sky-300'
                                        : 'border-amber-400/30 bg-amber-500/10 text-amber-300'),
                            ],
                            [
                                'icon' => 'heroicon-o-arrow-trending-up',
                                'label' => $windDiff === 0
                                    ? 'Similar Wind Speed'
                                    : (
                                        $windDiff > 0
                                            ? $comparisonWeather->city . ' has ' . number_format(abs($windDiff), 1) . ' m/s Stronger Wind'
                                            : $comparisonWeather->city . ' has ' . number_format(abs($windDiff), 1) . ' m/s Calmer Wind'
                                    ),
                                'tone' => $windDiff === 0
                                    ? 'border-slate-400/30 bg-slate-500/10 text-slate-300'
                                    : ($windDiff > 0
                                        ? 'border-teal-400/30 bg-teal-500/10 text-teal-300'
                                        : 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300'),
                            ],
                            [
                                'icon' => 'heroicon-o-shield-check',
                                'label' => $riskDiff === 0
                                    ? 'Same Risk Level'
                                    : (
                                        $riskDiff < 0
                                            ? $primaryWeather->city . ' has Safer Risk Level'
                                            : $comparisonWeather->city . ' has Higher Risk Level'
                                    ),
                                'tone' => $riskDiff === 0
                                    ? 'border-slate-400/30 bg-slate-500/10 text-slate-300'
                                    : ($riskDiff < 0
                                        ? 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300'
                                        : 'border-rose-400/30 bg-rose-500/10 text-rose-300'),
                            ],
                        ];
                        // Remove the risk level badge item
                        $badgeItems = array_filter($badgeItems, function ($item) {
                            return !str_contains($item['label'], 'Risk Level');
                        });
                    @endphp

                    <div class="mb-6 flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-purple-400/20 to-pink-400/10">
                            <x-heroicon-o-chart-bar class="h-6 w-6 text-purple-300" />
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-purple-300">Analysis</p>
                            <h2 class="text-2xl font-black text-white">Comparison Summary</h2>
                        </div>
                    </div>

                    <div class="mb-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach($badgeItems as $badge)
                            <div class="flex items-center gap-3 rounded-2xl border px-4 py-3 {{ $badge['tone'] }}">
                                <x-dynamic-component :component="$badge['icon']" class="h-5 w-5 flex-none" />
                                <span class="text-sm font-black">{{ $badge['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Temperature Comparison -->
                        <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-fire class="h-5 w-5 flex-none text-orange-300" />
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Temperature</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        {!! $temperatureDiff === 0
                                            ? 'Both cities have similar temperatures.'
                                            : (
                                                $temperatureDiff > 0
                                                    ? $comparisonWeather->city . ' is ' . number_format(abs($temperatureDiff), 1) . '°C warmer than ' . $primaryWeather->city . '.'
                                                    : $comparisonWeather->city . ' is ' . number_format(abs($temperatureDiff), 1) . '°C cooler than ' . $primaryWeather->city . '.'
                                            )
                                        !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Humidity Comparison -->
                        <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-beaker class="h-5 w-5 flex-none text-blue-300" />
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Humidity</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        {!! $humidityDiff === 0
                                            ? 'Both cities have similar humidity levels.'
                                            : (
                                                $humidityDiff > 0
                                                    ? $comparisonWeather->city . ' is ' . number_format(abs($humidityDiff), 0) . '% more humid than ' . $primaryWeather->city . '.'
                                                    : $comparisonWeather->city . ' is ' . number_format(abs($humidityDiff), 0) . '% drier than ' . $primaryWeather->city . '.'
                                            )
                                        !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Wind Speed Comparison -->
                        <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-arrow-trending-up class="h-5 w-5 flex-none text-teal-300" />
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Wind Speed</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        {!! $windDiff === 0
                                            ? 'Both cities have similar wind speeds.'
                                            : (
                                                $windDiff > 0
                                                    ? $comparisonWeather->city . ' has ' . number_format(abs($windDiff), 1) . ' m/s stronger winds than ' . $primaryWeather->city . '.'
                                                    : $comparisonWeather->city . ' has ' . number_format(abs($windDiff), 1) . ' m/s calmer winds than ' . $primaryWeather->city . '.'
                                            )
                                        !!}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Risk Comparison -->
                        <div class="rounded-2xl border border-white/10 bg-white/[.03] p-4">
                            <div class="flex items-start gap-3">
                                <x-heroicon-o-shield-exclamation class="h-5 w-5 flex-none text-rose-300" />
                                <div class="flex-1">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Risk Level</p>
                                    <p class="mt-1 text-sm leading-relaxed text-slate-300">
                                        {!! $riskDiff === 0
                                            ? 'Both cities have similar risk levels.'
                                            : (
                                                $riskDiff < 0
                                                    ? $primaryWeather->city . ' has a safer risk level than ' . $comparisonWeather->city . '.'
                                                    : $comparisonWeather->city . ' has a higher risk level than ' . $primaryWeather->city . '.'
                                            )
                                        !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @elseif($comparisonError)
            <!-- Error State -->
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-8">
                <div class="flex items-start gap-4">
                    <x-heroicon-o-exclamation-triangle class="h-8 w-8 flex-none text-rose-300" />
                    <div>
                        <h3 class="text-xl font-black text-rose-200">Unable to Fetch Data</h3>
                        <p class="mt-2 text-sm leading-relaxed text-rose-100/70">{{ $comparisonError }}</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-8 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-cyan-500/20">
                    <x-heroicon-o-arrows-right-left class="h-10 w-10 text-cyan-300" />
                </div>
                <h3 class="mt-6 text-xl font-black text-white">Ready for Comparison</h3>
                <p class="mt-2 text-slate-400">Enter two cities above to see a detailed weather comparison and analysis</p>
            </div>
        @endif

        <footer class="py-10 text-center text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
            Weather Intelligence Monitoring System
        </footer>
    </div>
</body>
</html>
