<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071426">
    <title>Weather History - WeatherInsight</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
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
<body class="weather-dashboard min-h-screen text-slate-100 antialiased" x-data="{ mobileMenuOpen: false, selectedRecord: null }">
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
                    <a href="/comparison" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>Comparison</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    @auth
                        <a href="/leaderboard" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                            <span>Leaderboard</span>
                            <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                        </a>
                        <a href="/history" class="group relative px-4 py-2 text-sm font-semibold text-white transition-colors hover:text-cyan-300">
                            <span>History</span>
                            <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-100 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                        </a>
                    @else
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.')" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                            <span>Leaderboard</span>
                            <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                        </button>
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.')" class="group relative px-4 py-2 text-sm font-semibold text-white transition-colors hover:text-cyan-300">
                            <span>History</span>
                            <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-100 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                        </button>
                    @endauth
                </div>

                <!-- Right Section: Search & User -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- User Info (Hidden on mobile) -->
                    @auth
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
                    <a href="/" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Dashboard
                    </a>
                    <a href="/comparison" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                        Comparison
                    </a>
                    @auth
                        <a href="/leaderboard" @click="mobileMenuOpen = false" class="block rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            Leaderboard
                        </a>
                        <a href="/history" class="block rounded-xl bg-cyan-400/10 px-4 py-3 text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
                            History
                        </a>
                    @else
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.'); mobileMenuOpen = false" class="block w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-slate-300 transition-colors hover:bg-white/5 hover:text-white">
                            Leaderboard
                        </button>
                        <button type="button" @click="alert('Silakan login terlebih dahulu untuk mengakses halaman ini.'); mobileMenuOpen = false" class="block w-full rounded-xl bg-cyan-400/10 px-4 py-3 text-left text-sm font-semibold text-cyan-300 transition-colors hover:bg-cyan-400/20">
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

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-400 to-purple-500 shadow-lg shadow-indigo-950/40">
                    <x-heroicon-o-clock class="h-7 w-7 text-slate-950" />
                </div>
                <div>
                    <h1 class="text-3xl font-black tracking-tight text-white sm:text-4xl">Weather History</h1>
                    <p class="text-sm font-medium text-slate-400">Historical weather observations and records</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <form method="GET" action="/history" class="glass-panel mb-6 rounded-3xl p-4 sm:p-6">
            <div class="grid gap-4 sm:grid-cols-[1fr_auto]">
                <!-- Date Filter -->
                <label class="relative">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-400">Date</span>
                    <x-heroicon-o-calendar class="pointer-events-none absolute left-3 top-10 h-5 w-5 text-slate-400" />
                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full rounded-2xl border border-white/10 bg-slate-950/40 py-3 pl-10 pr-4 text-white focus:border-cyan-400 focus:ring-cyan-400"
                    >
                </label>

                <!-- Actions -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-cyan-400 to-teal-400 px-6 py-3 font-bold text-slate-950 shadow-lg shadow-cyan-950/30 transition hover:brightness-110">
                        <span class="flex items-center justify-center gap-2">
                            <x-heroicon-o-funnel class="h-5 w-5" />
                            Date
                        </span>
                    </button>
                    <a href="/history" class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 bg-slate-950/40 text-slate-400 transition hover:border-cyan-400 hover:text-cyan-400" title="Refresh history">
                        <x-heroicon-o-arrow-path class="h-5 w-5" />
                    </a>
                </div>
            </div>
        </form>

        <!-- History Table -->
        <div class="glass-panel rounded-xl p-4 mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-400">
                    <thead class="bg-slate-900/50 text-xs uppercase">
                        <tr>
                            <th scope="col" class="px-4 py-3">Date & Time</th>
                            <th scope="col" class="px-4 py-3">City</th>
                            <th scope="col" class="px-4 py-3 text-right">Temp</th>
                            <th scope="col" class="hidden px-4 py-3 text-right sm:table-cell">Humidity</th>
                            <th scope="col" class="hidden px-4 py-3 text-right md:table-cell">Pressure</th>
                            <th scope="col" class="hidden px-4 py-3 text-right lg:table-cell">Wind</th>
                            <th scope="col" class="px-4 py-3 text-center">Risk</th>
                            <th scope="col" class="px-4 py-3">Weather</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($history as $record)
                            <tr 
                                @click="selectedRecord = {{ json_encode($record->getModalData()) }}"
                                class="border-b border-slate-700/30 cursor-pointer transition-colors hover:bg-slate-700/20"
                            >
                                <td class="px-4 py-3 font-medium text-slate-300">
                                    {{ $record->recorded_at->format('d M Y') }}<br>
                                    <span class="text-xs text-slate-500">{{ $record->recorded_at->format('H:i') }}</span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-white">
                                    {{ Str::title($record->city) }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-white">
                                    {{ number_format($record->temperature, 1) }}°C
                                </td>
                                <td class="hidden px-4 py-3 text-right font-medium text-slate-300 sm:table-cell">
                                    {{ $record->humidity }}%
                                </td>
                                <td class="hidden px-4 py-3 text-right font-medium text-slate-300 md:table-cell">
                                    {{ number_format($record->pressure, 0) }} hPa
                                </td>
                                <td class="hidden px-4 py-3 text-right font-medium text-slate-300 lg:table-cell">
                                    {{ number_format($record->wind_speed, 1) }} m/s
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $riskColors = [
                                            'low' => 'bg-emerald-400/10 text-emerald-300',
                                            'medium' => 'bg-amber-400/10 text-amber-300',
                                            'high' => 'bg-rose-400/10 text-rose-300',
                                        ];
                                        $riskLevel = $record->risk_level ?? 'low';
                                        $riskColor = $riskColors[$riskLevel] ?? 'bg-slate-400/10 text-slate-300';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-bold uppercase {{ $riskColor }}">
                                        {{ strtoupper($riskLevel) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <img 
                                            src="https://openweathermap.org/img/wn/{{ $record->weather_icon }}@2x.png" 
                                            alt="{{ $record->weather_description }}"
                                            class="h-8 w-8"
                                        >
                                        <span class="hidden text-sm font-medium capitalize text-slate-300 lg:inline">
                                            {{ $record->weather_description }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <x-heroicon-o-inbox class="mx-auto mb-3 h-12 w-12 text-slate-600" />
                                    <p class="text-sm font-semibold text-slate-500">No weather history found</p>
                                    <p class="mt-1 text-xs text-slate-600">Try adjusting the date filter or refresh the table</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($history->hasPages())
                <div class="border-t border-white/10 bg-slate-950/40 px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-slate-400">
                            Showing <span class="font-semibold text-white">{{ $history->firstItem() }}</span> 
                            to <span class="font-semibold text-white">{{ $history->lastItem() }}</span> 
                            of <span class="font-semibold text-white">{{ $history->total() }}</span> records
                        </div>
                        <div class="flex gap-2">
                            @if ($history->onFirstPage())
                                <span class="cursor-not-allowed rounded-xl border border-white/10 bg-slate-950/40 px-4 py-2 text-sm font-semibold text-slate-600">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $history->previousPageUrl() }}" class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-2 text-sm font-semibold text-cyan-300 transition hover:bg-slate-950/60">
                                    Previous
                                </a>
                            @endif

                            @if ($history->hasMorePages())
                                <a href="{{ $history->nextPageUrl() }}" class="rounded-xl border border-white/10 bg-slate-950/40 px-4 py-2 text-sm font-semibold text-cyan-300 transition hover:bg-slate-950/60">
                                    Next
                                </a>
                            @else
                                <span class="cursor-not-allowed rounded-xl border border-white/10 bg-slate-950/40 px-4 py-2 text-sm font-semibold text-slate-600">
                                    Next
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </main>

        <footer class="py-10 text-center text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
            Weather Intelligence Monitoring System
        </footer>
    </div>

    <!-- Detail Modal -->
    <div 
        x-show="selectedRecord" 
        x-cloak
        @click="selectedRecord = null"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 p-4 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.stop
            class="glass-panel w-full max-w-2xl rounded-3xl p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <template x-if="selectedRecord">
                <div>
                    <div class="mb-6 flex items-center justify-between">
                        <h2 class="text-2xl font-black text-white">Weather Details</h2>
                        <button @click="selectedRecord = null" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-slate-950/40 text-slate-400 transition hover:border-rose-400 hover:text-rose-400">
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">City</p>
                            <p class="text-lg font-bold text-white" x-text="selectedRecord.city"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Recorded At</p>
                            <p class="text-lg font-bold text-white" x-text="new Date(selectedRecord.recorded_at).toLocaleString()"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Temperature</p>
                            <p class="text-lg font-bold text-white" x-text="selectedRecord.temperature + '°C'"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Humidity</p>
                            <p class="text-lg font-bold text-white" x-text="selectedRecord.humidity + '%'"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Pressure</p>
                            <p class="text-lg font-bold text-white" x-text="selectedRecord.pressure + ' hPa'"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Wind Speed</p>
                            <p class="text-lg font-bold text-white" x-text="selectedRecord.wind_speed + ' m/s'"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Risk Level</p>
                            <p class="text-lg font-bold uppercase text-white" x-text="selectedRecord.risk_level"></p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <p class="mb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Weather</p>
                            <p class="text-lg font-bold capitalize text-white" x-text="selectedRecord.weather_description"></p>
                        </div>
                    </div>

                    <template x-if="selectedRecord.recommendation">
                        <div class="mt-4 rounded-2xl border border-cyan-400/20 bg-cyan-400/5 p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-cyan-400">Recommendation</p>
                            <p class="text-sm text-slate-300" x-text="selectedRecord.recommendation"></p>
                        </div>
                    </template>

                    <template x-if="selectedRecord.insight">
                        <div class="mt-4 rounded-2xl border border-purple-400/20 bg-purple-400/5 p-4">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-purple-400">Insight</p>
                            <p class="text-sm text-slate-300" x-text="selectedRecord.insight"></p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</body>
</html>
