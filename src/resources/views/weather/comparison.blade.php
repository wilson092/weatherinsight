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
    </style>
</head>
<body class="weather-dashboard min-h-screen text-slate-100 antialiased" x-data="{ mobileMenuOpen: false }">
    <!-- Modern Navbar -->
    <nav class="sticky top-0 z-50 px-4 pt-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div class="glass-panel flex h-16 items-center justify-between gap-4 rounded-full px-4 sm:h-[70px] sm:px-6 lg:gap-8 lg:px-8">
                
                <!-- Logo Section -->
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-400 to-teal-400 shadow-lg shadow-cyan-950/40 sm:h-11 sm:w-11">
                        <x-heroicon-o-cloud class="h-6 w-6 text-slate-950 sm:h-7 sm:w-7" />
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-black tracking-tight text-white lg:text-xl">WeatherInsight</h1>
                    </div>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden items-center gap-1 lg:flex">
                    <a href="/" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>Dashboard</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    <a href="/comparison" class="group relative px-4 py-2 text-sm font-semibold text-white transition-colors hover:text-cyan-300">
                        <span>Comparison</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-100 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    <a href="/leaderboard" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>Leaderboard</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    <a href="/history" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>History</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                </div>

                <!-- Right Section: User -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- User Info (Hidden on mobile) -->
                    <div class="hidden items-center gap-2 rounded-full border border-white/10 bg-slate-950/25 px-3 py-2 sm:flex lg:px-4">
                        <x-heroicon-o-user-circle class="h-5 w-5 text-cyan-300" />
                        <span class="text-sm font-semibold text-white">{{ auth()->user()->name }}</span>
                    </div>

                    <!-- Logout Button (Hidden on mobile) -->
                    <form method="POST" action="/logout" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full border border-rose-400/20 bg-rose-500/10 text-rose-300 transition hover:bg-rose-500 hover:text-white" title="Logout">
                            <x-heroicon-o-arrow-right-start-on-rectangle class="h-5 w-5" />
                        </button>
                    </form>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-slate-950/25 text-white transition hover:bg-slate-950/40 lg:hidden">
                        <x-heroicon-o-bars-3 x-show="!mobileMenuOpen" class="h-5 w-5" />
                        <x-heroicon-o-x-mark x-show="mobileMenuOpen" x-cloak class="h-5 w-5" />
                    </button>
                </div>
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
         class="fixed inset-x-0 top-20 z-40 px-4 sm:top-[88px] sm:px-6 lg:hidden">
        <div class="glass-panel mx-auto max-w-7xl overflow-hidden rounded-3xl">
            <div class="flex flex-col p-4">
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
                    <a href="/" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Dashboard
                    </a>
                    <a href="/comparison" class="block rounded-xl bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
                        Comparison
                    </a>
                    <a href="/leaderboard" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Leaderboard
                    </a>
                    <a href="/history" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
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

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <section class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8 mb-8">
            <div class="absolute -right-16 top-0 h-56 w-56 rounded-full bg-cyan-400/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/4 h-48 w-48 rounded-full bg-teal-400/10 blur-3xl"></div>
            
            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-[0.25em] text-cyan-300">Side-by-Side Intelligence</p>
                <h1 class="mt-2 text-3xl font-black text-white sm:text-4xl">Weather Comparison</h1>
                <p class="mt-2 text-slate-400">Compare weather conditions between two cities and analyze the differences</p>
            </div>
        </section>

        <!-- Search Form -->
        <form method="GET" action="/comparison" class="glass-panel mb-8 rounded-3xl p-4 sm:p-6">
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
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 py-3.5 pl-12 pr-4 text-white placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
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
                            class="w-full rounded-2xl border border-white/10 bg-slate-950/40 py-3.5 pl-12 pr-4 text-white placeholder:text-slate-500 focus:border-teal-400 focus:ring-teal-400"
                        >
                    </div>
                </div>
            </div>

            <button type="submit" class="mt-4 w-full rounded-2xl bg-gradient-to-r from-cyan-400 to-teal-400 px-7 py-3.5 font-bold text-slate-950 shadow-lg shadow-cyan-950/30 transition hover:brightness-110 sm:w-auto">
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
            <section class="glass-panel relative overflow-hidden rounded-3xl p-6 sm:p-8">
                <div class="absolute -right-16 top-0 h-56 w-56 rounded-full bg-purple-400/10 blur-3xl"></div>
                
                <div class="relative">
                    @php
                        $temperatureDiff = $comparisonWeather->temperature - $primaryWeather->temperature;
                        $humidityDiff = $comparisonWeather->humidity - $primaryWeather->humidity;
                        $windDiff = $comparisonWeather->wind_speed - $primaryWeather->wind_speed;
                        $riskRank = ['Low' => 1, 'Medium' => 2, 'High' => 3, 'Extreme' => 4];
                        $primaryRisk = $primaryAnalysis['risk'] ?? $primaryWeather->risk_level ?? 'Low';
                        $comparisonRisk = $comparisonAnalysis['risk'] ?? $comparisonWeather->risk_level ?? 'Low';
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
                                'label' => number_format(abs($temperatureDiff), 1).'°C '.($temperatureDiff >= 0 ? 'Warmer' : 'Cooler'),
                                'tone' => $temperatureDiff >= 0 ? 'border-orange-400/30 bg-orange-500/10 text-orange-300' : 'border-cyan-400/30 bg-cyan-500/10 text-cyan-300',
                            ],
                            [
                                'icon' => 'heroicon-o-beaker',
                                'label' => number_format(abs($humidityDiff), 0).'% '.($humidityDiff >= 0 ? 'More Humid' : 'Drier'),
                                'tone' => $humidityDiff >= 0 ? 'border-sky-400/30 bg-sky-500/10 text-sky-300' : 'border-amber-400/30 bg-amber-500/10 text-amber-300',
                            ],
                            [
                                'icon' => 'heroicon-o-arrow-trending-up',
                                'label' => number_format(abs($windDiff), 1).' m/s '.($windDiff >= 0 ? 'Stronger Wind' : 'Calmer Wind'),
                                'tone' => $windDiff >= 0 ? 'border-teal-400/30 bg-teal-500/10 text-teal-300' : 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300',
                            ],
                            [
                                'icon' => 'heroicon-o-shield-check',
                                'label' => $riskDiff === 0 ? 'Same Risk Level' : ($riskDiff < 0 ? 'Safer Risk Level' : 'Higher Risk Level'),
                                'tone' => $riskDiff <= 0 ? 'border-emerald-400/30 bg-emerald-500/10 text-emerald-300' : 'border-rose-400/30 bg-rose-500/10 text-rose-300',
                            ],
                        ];
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
                                        {!! $summary['temperature'] !!}
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
                                        {!! $summary['humidity'] !!}
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
                                        {!! $summary['wind'] !!}
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
                                        {!! $summary['risk'] !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @elseif($comparisonError)
            <!-- Error State -->
            <div class="glass-panel rounded-3xl border border-rose-400/20 bg-gradient-to-br from-rose-500/10 to-red-500/5 p-8">
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
            <div class="glass-panel rounded-3xl p-12 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400/20 to-teal-400/10">
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
