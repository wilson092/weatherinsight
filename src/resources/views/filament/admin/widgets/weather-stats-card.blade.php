<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">
                    Recommendation
                </div>

                <div class="text-lg font-bold">
                    {{ $latest?->recommendation ?? '-' }}
                </div>
            </div>

            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">
                    Insight
                </div>

                <div class="text-lg font-bold">
                    {{ $latest?->insight ?? '-' }}
                </div>
            </div>

            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">
                    Risk Level
                </div>

                <div class="text-lg font-bold">
                    {{ strtoupper($latest?->risk_level ?? '-') }}
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>