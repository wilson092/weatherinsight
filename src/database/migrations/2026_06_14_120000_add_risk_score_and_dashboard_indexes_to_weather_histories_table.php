<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_histories', function (Blueprint $table) {
            $table->unsignedTinyInteger('risk_score')->default(0)->after('risk_level');
            $table->index(['city', 'recorded_at'], 'weather_histories_city_recorded_at_index');
            $table->index('risk_score', 'weather_histories_risk_score_index');
        });

        $score = '(CASE WHEN temperature > 35 THEN 25 ELSE 0 END'
            . ' + CASE WHEN humidity > 85 THEN 25 ELSE 0 END'
            . ' + CASE WHEN wind_speed > 20 THEN 25 ELSE 0 END'
            . ' + CASE WHEN pressure < 1000 OR pressure > 1020 THEN 25 ELSE 0 END)';

        DB::table('weather_histories')->update([
            'risk_score' => DB::raw($score),
            'risk_level' => DB::raw("CASE WHEN {$score} <= 30 THEN 'LOW' WHEN {$score} <= 70 THEN 'MEDIUM' ELSE 'HIGH' END"),
        ]);
    }

    public function down(): void
    {
        Schema::table('weather_histories', function (Blueprint $table) {
            $table->dropIndex('weather_histories_city_recorded_at_index');
            $table->dropIndex('weather_histories_risk_score_index');
            $table->dropColumn('risk_score');
        });
    }
};
