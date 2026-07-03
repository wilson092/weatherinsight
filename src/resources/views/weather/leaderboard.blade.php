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
                    <a href="/comparison" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>Comparison</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    <a href="/leaderboard" class="group relative px-4 py-2 text-sm font-semibold text-white transition-colors hover:text-cyan-300">
                        <span>Leaderboard</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-100 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                    <a href="/history" class="group relative px-4 py-2 text-sm font-semibold text-slate-300 transition-colors hover:text-cyan-300">
                        <span>History</span>
                        <span class="absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-cyan-400 transition-transform group-hover:scale-x-100"></span>
                    </a>
                </div>

                <!-- Right Section: Search & User -->
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

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
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
            <x-weather.leaderboard :leaderboards="$leaderboards" />
        </main>

        <footer class="py-10 text-center text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">
            Weather Intelligence Monitoring System
        </footer>
    </div>
</body>
</html>