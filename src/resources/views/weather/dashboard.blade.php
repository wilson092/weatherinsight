<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WeatherInsight</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#082B3A] text-white min-h-screen">

<div class="max-w-7xl mx-auto px-4 py-10">

   {{-- HEADER --}}
<div class="flex flex-col lg:flex-row lg:items-start lg:justify-between mb-10 gap-6">

    <div>

        <h1 class="text-5xl font-black text-gray-800">
            WeatherInsight
        </h1>

        <p class="text-gray-500 mt-3 text-lg">
            Real-time Weather Monitoring Dashboard
        </p>

    </div>

    <div class="flex items-center gap-4">

        {{-- USER PROFILE --}}
        <div class="bg-white rounded-2xl shadow-lg px-5 py-3 flex items-center gap-4">

            <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <div>

                <div class="text-sm text-gray-500">
                    Logged in as
                </div>

                <div class="font-bold text-gray-800">
                    {{ auth()->user()->name }}
                </div>

            </div>

        </div>

        {{-- LAST UPDATE --}}
        <div class="bg-white rounded-2xl shadow-lg px-5 py-3 text-right">

            <div class="text-sm text-gray-500">
                Last Update
            </div>

            <div class="font-bold text-gray-800">
                {{ $latest?->recorded_at?->format('d M Y H:i') }}
            </div>

        </div>

        {{-- LOGOUT --}}
        <form method="POST" action="/logout">

            @csrf

            <button
                type="submit"
                class="rounded-2xl bg-red-500 px-5 py-3 text-white font-bold shadow-lg hover:bg-red-600 transition"
            >
                Logout
            </button>

        </form>

    </div>

</div>
    <form method="GET" action="/" class="mb-6">
    <div class="flex gap-3">
        <input
    type="text"
    name="city"
    value="{{ request('city', 'Jakarta') }}"
    placeholder="Search city..."
    style="color: black !important;"
    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
/>

        <button
            type="submit"
            class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700 transition"
        >
            Search
        </button>
    </div>
</form>
    {{-- HERO CARD --}}
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl p-8 mb-8 border border-white/50">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between">

            <div>

                <div class="text-gray-500 text-lg mb-2">
                    {{ $latest?->city }}
                </div>

                <div class="text-7xl font-black text-gray-800">
                    {{ $latest?->temperature }}°
                </div>

                <div class="text-2xl text-gray-600 mt-2">
                    {{ $latest?->weather_main }}
                </div>

            </div>

            <div class="mt-8 md:mt-0 text-center">

                <img
                    class="w-40 mx-auto"
                    src="https://openweathermap.org/img/wn/{{ $latest?->weather_icon }}@4x.png"
                >

            </div>

        </div>

    </div>

    {{-- ALERT --}}
    @if($latest?->risk_level === 'HIGH')

        <div class="bg-red-500 text-white rounded-2xl p-6 mb-8 shadow-xl">

            <div class="text-2xl font-bold mb-2">
                ⚠ High Weather Risk
            </div>

            <div>
                {{ $latest?->insight }}
            </div>

        </div>

    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-2">
                Humidity
            </div>

            <div class="text-4xl font-black text-gray-800">
                {{ $latest?->humidity }}%
            </div>

        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-2">
                Pressure
            </div>

            <div class="text-4xl font-black text-gray-800">
                {{ $latest?->pressure }}
            </div>

            <div class="text-gray-500 mt-1">
                hPa
            </div>

        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-2">
                Wind Speed
            </div>

            <div class="text-4xl font-black text-gray-800">
                {{ $latest?->wind_speed }}
            </div>

            <div class="text-gray-500 mt-1">
                km/h
            </div>

        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-2">
                Risk Level
            </div>

            <div class="text-4xl font-black uppercase
                @if($latest?->risk_level === 'HIGH')
                    text-red-500
                @elseif($latest?->risk_level === 'MEDIUM')
                    text-yellow-500
                @else
                    text-green-500
                @endif
            ">
                {{ $latest?->risk_level }}
            </div>

        </div>

    </div>

    {{-- INSIGHT --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-3">
                Recommendation
            </div>

            <div class="text-2xl font-bold text-gray-800">
                {{ $latest?->recommendation }}
            </div>

        </div>

        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg p-6">

            <div class="text-gray-500 text-sm mb-3">
                Insight
            </div>

            <div class="text-2xl font-bold text-gray-800">
                {{ $latest?->insight }}
            </div>

        </div>

    </div>

    {{-- HISTORY --}}
    <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl p-8">

        <div class="flex items-center justify-between mb-8">

            <div>
                <h2 class="text-3xl font-black text-gray-800">
                    Weather History
                </h2>

                <p class="text-gray-500 mt-2">
                    Latest weather monitoring history
                </p>
            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b border-gray-200 text-gray-500">

                        <th class="text-left py-4">
                            Time
                        </th>

                        <th class="text-left py-4">
                            Temperature
                        </th>

                        <th class="text-left py-4">
                            Humidity
                        </th>

                        <th class="text-left py-4">
                            Pressure
                        </th>

                        <th class="text-left py-4">
                            Wind
                        </th>

                        <th class="text-left py-4">
                            Risk
                        </th>

                    </tr>

                </thead>

               <tbody>

    @forelse($history as $item)

        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">

            <td class="py-5 text-gray-700">
                {{ $item->recorded_at?->format('H:i') }}
            </td>

            <td class="py-5 font-semibold text-gray-800">
                {{ $item->temperature }} °C
            </td>

            <td class="py-5 text-gray-700">
                {{ $item->humidity }} %
            </td>

            <td class="py-5 text-gray-700">
                {{ $item->pressure }} hPa
            </td>

            <td class="py-5 text-gray-700">
                {{ $item->wind_speed }} km/h
            </td>

            <td class="py-5">

                <span class="
                    inline-flex items-center
                    px-4 py-2 rounded-full text-sm font-bold

                    @if($item->risk_level === 'HIGH')
                        bg-red-100 text-red-600
                    @elseif($item->risk_level === 'MEDIUM')
                        bg-yellow-100 text-yellow-700
                    @else
                        bg-green-100 text-green-600
                    @endif
                ">

                    {{ strtoupper($item->risk_level) }}

                </span>

            </td>

        </tr>

    @empty

        <tr>

            <td colspan="6" class="py-10 text-center text-gray-400">

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