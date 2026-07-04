<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071426">
    <title>Extreme Weather Leaderboard - WeatherInsight</title>

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
                <a href="/comparison" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">Comparison</a>
                <a href="/leaderboard" class="relative flex h-full items-center text-sm font-bold text-white">
                    Leaderboard
                    <span class="absolute bottom-0 left-0 h-0.5 w-full bg-cyan-400"></span>
                </a>
                <a href="/history" class="flex h-full items-center text-sm font-semibold text-slate-300 transition hover:text-cyan-300">History</a>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @livewire('temperature-toggle')

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
                    <a href="/" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Dashboard
                    </a>
                    <a href="/comparison" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Comparison
                    </a>
                    <a href="/leaderboard" class="block rounded-xl bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
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
        <!-- Header Section -->
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg shadow-amber-950/40">
                    <x-heroicon-o-trophy class="h-7 w-7 text-slate-950" />
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">Extreme Weather Leaderboard</h1>
                    <p class="text-sm font-medium text-slate-400">Live ranking of monitored cities across all metrics</p>
                </div>
            </div>
        </div>

        <!-- Leaderboard Content -->
        <main class="space-y-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
                {{-- Hottest Cities --}}
                <div class="glass-panel rounded-xl p-4">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500/20">
                            <x-heroicon-o-fire class="h-5 w-5 text-orange-300" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">5 Kota Terpanas</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-slate-900/50 text-xs uppercase">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Rank</th>
                                    <th scope="col" class="px-4 py-3">City</th>
                                    <th scope="col" class="px-4 py-3">Temp (°C)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hottestCities as $city)
                                    <tr class="border-b border-slate-700/30 hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 font-medium text-white">
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 flex items-center gap-2">
                                            <img src="https://openweathermap.org/img/wn/{{ $city->weather_icon }}@2x.png" alt="{{ $city->weather_description }}" class="h-6 w-6">
                                            {{ $city->city }}
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($city->temperature, 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Coldest Cities --}}
                <div class="glass-panel rounded-xl p-4">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-500/20">
                            <x-heroicon-o-arrow-trending-down class="h-5 w-5 text-blue-300" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">5 Kota Terdingin</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-slate-900/50 text-xs uppercase">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Rank</th>
                                    <th scope="col" class="px-4 py-3">City</th>
                                    <th scope="col" class="px-4 py-3">Temp (°C)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coldestCities as $city)
                                    <tr class="border-b border-slate-700/30 hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 font-medium text-white">
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 flex items-center gap-2">
                                            <img src="https://openweathermap.org/img/wn/{{ $city->weather_icon }}@2x.png" alt="{{ $city->weather_description }}" class="h-6 w-6">
                                            {{ $city->city }}
                                        </td>
                                        <td class="px-4 py-3">{{ number_format($city->temperature, 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Most Humid Cities --}}
                <div class="glass-panel rounded-xl p-4">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-sky-500/20">
                            <x-heroicon-o-beaker class="h-5 w-5 text-sky-300" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">5 Kota Terlembab</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-slate-900/50 text-xs uppercase">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Rank</th>
                                    <th scope="col" class="px-4 py-3">City</th>
                                    <th scope="col" class="px-4 py-3">Humidity (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mostHumidCities as $city)
                                    <tr class="border-b border-slate-700/30 hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 font-medium text-white">
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $city->city }}</td>
                                        <td class="px-4 py-3">{{ $city->humidity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Windiest Cities --}}
                <div class="glass-panel rounded-xl p-4">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-teal-500/20">
                            <x-heroicon-o-bolt class="h-5 w-5 text-teal-300" />
                        </div>
                        <h2 class="text-lg font-semibold text-white">5 Kota Angin Terkencang</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-400">
                            <thead class="bg-slate-900/50 text-xs uppercase">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Rank</th>
                                    <th scope="col" class="px-4 py-3">City</th>
                                    <th scope="col" class="px-4 py-3">Wind Speed (m/s)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($windiestCities as $city)
                                    <tr class="border-b border-slate-700/30 hover:bg-slate-700/20 transition-colors">
                                        <td class="px-4 py-3 font-medium text-white">
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-cyan-500 text-white text-xs font-bold">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $city->city }}</td>
                                        <td class="px-4 py-3">{{ number_format($city->wind_speed, 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-8 text-center text-sm text-slate-500">
            &copy; {{ now()->year }} WeatherInsight. All rights reserved.
        </footer>
    </div>
</body>
</html>
