<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherInsight</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f2942 100%);
            animation: fadeIn 0.6s ease-out;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 150, 136, 0.15);
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(30, 144, 255, 0.15) 0%, rgba(0, 206, 209, 0.1) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(34, 211, 238, 0.4);
        }

        .hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(173, 216, 230, 0.2) 100%);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(173, 216, 230, 0.4);
        }

        .table-row-hover:hover {
            background-color: rgba(0, 150, 136, 0.05);
        }

        .search-input::placeholder {
            color: rgba(0, 0, 0, 0.5) !important;
        }

        .icon-stat {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-title {
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .logo-glow {
            box-shadow: 0 0 30px rgba(0, 150, 136, 0.4), 0 0 60px rgba(34, 197, 94, 0.2);
        }

        .profile-card {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(20, 184, 166, 0.1) 100%);
            border: 2px solid rgba(34, 211, 238, 0.3);
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            border-color: rgba(34, 211, 238, 0.6);
            box-shadow: 0 0 20px rgba(34, 211, 238, 0.2);
        }

        .update-card {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
            border: 2px solid rgba(139, 92, 246, 0.3);
        }

        .logout-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(239, 68, 68, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .logout-btn:active {
            transform: translateY(-1px);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 16px;
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.05) 0%, rgba(20, 184, 166, 0.05) 100%);
            padding: 16px 24px;
            border-radius: 20px;
            border: 2px solid rgba(34, 211, 238, 0.2);
        }

        .forecast-card {
            background: linear-gradient(135deg, rgba(15, 118, 168, 0.2) 0%, rgba(0, 188, 212, 0.15) 100%);
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
        }

        .forecast-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 188, 212, 0.25);
            border-color: rgba(34, 211, 238, 0.6);
            background: linear-gradient(135deg, rgba(15, 118, 168, 0.3) 0%, rgba(0, 188, 212, 0.25) 100%);
        }
    </style>
</head>

<body class="text-white min-h-screen"wire:poll.60s>

<div class="max-w-7xl mx-auto px-4 py-12">

   {{-- HEADER --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-14 gap-8">

    {{-- BRAND SECTION --}}
    <div class="brand-section">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-cyan-300 via-teal-500 to-emerald-600 flex items-center justify-center logo-glow flex-shrink-0">
            <!-- Weather/Cloud Icon SVG -->
            <svg class="w-12 h-12 text-white" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                <!-- Sun -->
                <circle cx="12" cy="8" r="4" fill="white"/>
                <!-- Sun rays -->
                <line x1="12" y1="1" x2="12" y2="3" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="12" y1="21" x2="12" y2="23" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="21" y1="8" x2="23" y2="8" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="1" y1="8" x2="3" y2="8" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                <!-- Cloud -->
                <path d="M4 14c-1.1 0-2 .9-2 2 0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2 0-1.1-.9-2-2-2-0.4-1.2-1.5-2-2.8-2-1 0-1.9.5-2.4 1.2C10.3 14.2 9.2 14 8 14c-2.2 0-4 1.8-4 4" fill="white" opacity="0.95"/>
            </svg>
        </div>
        <div>
            <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-200 via-teal-200 to-emerald-200">
                WeatherInsight
            </h1>
            <p class="text-cyan-100 mt-1 text-sm md:text-base font-semibold">
                Real-time Weather Monitoring Dashboard
            </p>
        </div>
    </div>

    {{-- RIGHT SECTION (Profile, Update, Logout) --}}
    <div class="flex flex-col sm:flex-row items-stretch md:items-center gap-4 w-full lg:w-auto">

        {{-- USER PROFILE --}}
        <div class="profile-card rounded-2xl px-6 py-4 flex items-center gap-4 flex-1 sm:flex-none">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-cyan-400 to-teal-600 rounded-xl blur opacity-75"></div>
                <div class="relative w-14 h-14 rounded-xl bg-gradient-to-br from-cyan-300 to-teal-600 flex items-center justify-center text-white font-black text-xl shadow-lg">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>

            <div class="flex-1">
                <div class="text-xs text-cyan-300 uppercase tracking-widest font-bold">
                    👤 Logged In
                </div>
                <div class="font-bold text-white text-sm md:text-base leading-tight">
                    {{ auth()->user()->name }}
                </div>
            </div>
        </div>

        {{-- LAST UPDATE --}}
        <div class="update-card rounded-2xl px-6 py-4 flex items-center justify-center gap-3 flex-1 sm:flex-none">
            <div class="text-2xl">🕐</div>
            <div>
                <div class="text-xs text-purple-300 uppercase tracking-widest font-bold">
                    Last Updated
                </div>
                <div class="font-bold text-white text-sm md:text-base">
                    {{ $latest?->recorded_at?->format('d M Y') }}
                </div>
                <div class="text-xs text-purple-200">
                    {{ $latest?->recorded_at?->format('H:i') }}
                </div>
            </div>
        </div>

        {{-- LOGOUT BUTTON --}}
        <form method="POST" action="/logout" class="w-full sm:w-auto">
            @csrf
            <button
                type="submit"
                class="logout-btn w-full sm:w-auto rounded-2xl px-8 py-4 text-white font-bold text-center flex items-center justify-center gap-2 shadow-xl hover:shadow-2xl"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>

    </div>

</div>
    <form method="GET" action="/" class="mb-8">
    <div class="flex gap-3">
        <input
            type="text"
            name="city"
            value="{{ request('city', 'Jakarta') }}"
            placeholder="Search city..."
            class="search-input flex-1 rounded-xl border-2 border-cyan-300/50 bg-white px-5 py-3 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400 transition font-medium"
        />

        <button
            type="submit"
            class="rounded-xl bg-gradient-to-r from-cyan-500 to-teal-600 px-6 py-3 font-semibold text-white hover:from-cyan-600 hover:to-teal-700 transition transform hover:scale-105"
        >
            Search
        </button>
    </div>
</form>
    {{-- HERO CARD --}}
    <div class="hero-card rounded-3xl shadow-2xl p-10 mb-10 card-hover">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between">

            <div>

                <div class="text-cyan-200 text-lg font-semibold mb-3 uppercase tracking-wide">
                    {{ $latest?->city }}
                </div>

                <div class="text-7xl md:text-8xl font-black text-white mb-4">
                    {{ $latest?->temperature }}°
                </div>

                <div class="text-2xl text-cyan-100 font-semibold">
                    {{ $latest?->weather_main }}
                </div>

            </div>

            <div class="mt-10 md:mt-0">

                <img
                    class="w-48 mx-auto drop-shadow-lg"
                    src="https://openweathermap.org/img/wn/{{ $latest?->weather_icon }}@4x.png"
                    alt="Weather icon"
                >

            </div>

        </div>

    </div>

    {{-- ALERT --}}
    @if($latest?->risk_level === 'HIGH')

        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-2xl p-8 mb-10 shadow-2xl card-hover border border-red-400/30">

            <div class="flex items-start gap-4">
                <div class="text-4xl">⚠️</div>
                <div>
                    <div class="text-2xl font-bold mb-2">
                        High Weather Risk
                    </div>

                    <div class="text-red-50 text-lg">
                        {{ $latest?->insight }}
                    </div>
                </div>
            </div>

        </div>

    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

        {{-- HUMIDITY --}}
        <div class="stat-card rounded-2xl shadow-xl p-6 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="stat-title text-cyan-100 font-semibold uppercase">
                    Humidity
                </div>
                <div class="icon-stat bg-blue-100">
                    <svg class="w-8 h-8 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.69C6.66 7.24 4 11.17 4 14.49c0 2.95 2.18 5.51 5 5.51s5-2.56 5-5.51c0-3.32-2.66-7.25-2-12.3zm0 1.48c1.4 1.41 3.6 4.76 3.6 10.82 0 2.09-1.56 3.79-3.5 3.79s-3.5-1.71-3.5-3.79c0-6.06 2.2-9.41 3.4-10.82z"/>
                    </svg>
                </div>
            </div>

            <div class="text-4xl font-black text-white">
                {{ $latest?->humidity }}%
            </div>
        </div>

        {{-- PRESSURE --}}
        <div class="stat-card rounded-2xl shadow-xl p-6 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="stat-title text-cyan-100 font-semibold uppercase">
                    Pressure
                </div>
                <div class="icon-stat bg-amber-100">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="6"/>
                        <path d="M12 6v6"/>
                        <circle cx="12" cy="12" r="2" fill="currentColor"/>
                    </svg>
                </div>
            </div>

            <div class="text-4xl font-black text-white">
                {{ $latest?->pressure }}
            </div>

            <div class="text-cyan-200 mt-2 font-medium">
                hPa
            </div>
        </div>

        {{-- WIND SPEED --}}
        <div class="stat-card rounded-2xl shadow-xl p-6 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="stat-title text-cyan-100 font-semibold uppercase">
                    Wind Speed
                </div>
                <div class="icon-stat bg-cyan-100">
                    <svg class="w-8 h-8 text-cyan-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 8H5c-.55 0-1 .45-1 1s.45 1 1 1h14c.55 0 1-.45 1-1s-.45-1-1-1zm0 5H3c-.55 0-1 .45-1 1s.45 1 1 1h16c.55 0 1-.45 1-1s-.45-1-1-1zm-2 5H5c-.55 0-1 .45-1 1s.45 1 1 1h12c.55 0 1-.45 1-1s-.45-1-1-1z"/>
                    </svg>
                </div>
            </div>

            <div class="text-4xl font-black text-white">
                {{ $latest?->wind_speed }}
            </div>

            <div class="text-cyan-200 mt-2 font-medium">
                km/h
            </div>
        </div>

        {{-- RISK LEVEL --}}
        <div class="stat-card rounded-2xl shadow-xl p-6 card-hover">
            <div class="flex items-center justify-between mb-4">
                <div class="stat-title text-cyan-100 font-semibold uppercase">
                    Risk Level
                </div>
                <div class="icon-stat
                    @if($latest?->risk_level === 'HIGH')
                        bg-red-100
                    @elseif($latest?->risk_level === 'MEDIUM')
                        bg-yellow-100
                    @else
                        bg-green-100
                    @endif
                ">
                    <svg class="w-8 h-8
                        @if($latest?->risk_level === 'HIGH')
                            text-red-600
                        @elseif($latest?->risk_level === 'MEDIUM')
                            text-yellow-600
                        @else
                            text-green-600
                        @endif
                    " viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 11h-2v-2h2v2zm0-4h-2V7h2v1z"/>
                    </svg>
                </div>
            </div>

            <div class="text-4xl font-black uppercase
                @if($latest?->risk_level === 'HIGH')
                    text-red-400
                @elseif($latest?->risk_level === 'MEDIUM')
                    text-yellow-400
                @else
                    text-green-400
                @endif
            ">
                {{ $latest?->risk_level }}
            </div>
        </div>

    </div>

    {{-- INSIGHT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-xl p-8 card-hover border border-white/20">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="text-cyan-200 text-sm font-semibold uppercase tracking-wide">
                    Recommendation
                </div>
            </div>

            <div class="text-xl font-bold text-white">
                {{ $latest?->recommendation }}
            </div>

        </div>

        <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-xl p-8 card-hover border border-white/20">

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-teal-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11z"/>
                    </svg>
                </div>
                <div class="text-teal-200 text-sm font-semibold uppercase tracking-wide">
                    Insight
                </div>
            </div>

            <div class="text-xl font-bold text-white">
                {{ $latest?->insight }}
            </div>

        </div>

    </div>
{{-- FORECAST 5 HARI --}}
<div class="mt-12 mb-10">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-black text-white">
                5 Day Forecast
            </h2>

            <p class="text-cyan-200 text-base mt-2">
                Extended weather prediction
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">

        @php
            $forecastByDay = [];
            foreach($forecast['list'] ?? [] as $item) {
                $day = \Carbon\Carbon::parse($item['dt_txt'])->format('Y-m-d');
                if(!isset($forecastByDay[$day])) {
                    $forecastByDay[$day] = $item;
                }
            }
            $dailyForecasts = array_values(array_slice($forecastByDay, 0, 5));
        @endphp

        @foreach($dailyForecasts as $item)

            <div class="forecast-card rounded-2xl p-6 text-white shadow-xl card-hover border border-cyan-400/30">

                <div class="text-center mb-4">
                    <p class="text-sm font-bold text-cyan-200 uppercase tracking-wide mb-1">
                        {{ \Carbon\Carbon::parse($item['dt_txt'])->format('l') }}
                    </p>
                    <p class="text-xs text-cyan-300 font-medium">
                        {{ \Carbon\Carbon::parse($item['dt_txt'])->format('H:i') }}
                    </p>

                    <img
                        class="w-16 h-16 mx-auto mt-2"
                        src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}@2x.png"
                        alt="Weather"
                    >
                </div>

                <div class="text-center mb-4">
                    <h3 class="text-4xl font-black text-white">
                        {{ round($item['main']['temp']) }}°
                    </h3>

                    <p class="text-cyan-100 text-sm mt-2 capitalize font-medium">
                        {{ $item['weather'][0]['description'] }}
                    </p>
                </div>

                <div class="border-t border-cyan-400/20 pt-3 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-cyan-100">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.69C6.66 7.24 4 11.17 4 14.49c0 2.95 2.18 5.51 5 5.51s5-2.56 5-5.51c0-3.32-2.66-7.25-2-12.3zm0 1.48c1.4 1.41 3.6 4.76 3.6 10.82 0 2.09-1.56 3.79-3.5 3.79s-3.5-1.71-3.5-3.79c0-6.06 2.2-9.41 3.4-10.82z"/>
                            </svg>
                            Humidity
                        </span>
                        <span class="font-bold text-white">{{ $item['main']['humidity'] }}%</span>
                    </div>

                    <div class="flex items-center justify-between text-cyan-100">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-cyan-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 8H5c-.55 0-1 .45-1 1s.45 1 1 1h14c.55 0 1-.45 1-1s-.45-1-1-1zm0 5H3c-.55 0-1 .45-1 1s.45 1 1 1h16c.55 0 1-.45 1-1s-.45-1-1-1zm-2 5H5c-.55 0-1 .45-1 1s.45 1 1 1h12c.55 0 1-.45 1-1s-.45-1-1-1z"/>
                            </svg>
                            Wind
                        </span>
                        <span class="font-bold text-white">{{ round($item['wind']['speed'], 1) }} km/h</span>
                    </div>
                </div>

            </div>

        @endforeach

    </div>
</div>
    {{-- HISTORY --}}
    <div class="bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl p-10 border border-white/20 card-hover">

        <div class="flex items-center justify-between mb-10">

            <div>
                <h2 class="text-3xl font-black text-white">
                    Weather History
                </h2>

                <p class="text-cyan-200 mt-2 text-base">
                    Latest weather monitoring history
                </p>
            </div>

            

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b border-white/20">

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Time
                        </th>

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Temperature
                        </th>

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Humidity
                        </th>

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Pressure
                        </th>

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Wind
                        </th>

                        <th class="text-left py-4 px-6 text-cyan-200 font-semibold uppercase tracking-wider text-sm">
                            Risk
                        </th>

                    </tr>

                </thead>

               <tbody>

    @forelse($history as $item)

        <tr class="border-b border-white/10 table-row-hover transition">

            <td class="py-5 px-6 text-white font-medium">
                {{ $item->recorded_at?->format('H:i') }}
            </td>

            <td class="py-5 px-6 font-semibold text-white">
                {{ $item->temperature }} °C
            </td>

            <td class="py-5 px-6 text-gray-300">
                {{ $item->humidity }} %
            </td>

            <td class="py-5 px-6 text-gray-300">
                {{ $item->pressure }} hPa
            </td>

            <td class="py-5 px-6 text-gray-300">
                {{ $item->wind_speed }} km/h
            </td>

            <td class="py-5 px-6">

                <span class="
                    inline-flex items-center
                    px-4 py-2 rounded-full text-sm font-bold

                    @if($item->risk_level === 'HIGH')
                        bg-red-500/20 text-red-300 border border-red-500/30
                    @elseif($item->risk_level === 'MEDIUM')
                        bg-yellow-500/20 text-yellow-300 border border-yellow-500/30
                    @else
                        bg-green-500/20 text-green-300 border border-green-500/30
                    @endif
                ">

                    {{ strtoupper($item->risk_level) }}

                </span>

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="6" class="py-12 text-center text-gray-400 text-lg">

                No weather history available

            </td>

        </tr>

    @endforelse

</tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>