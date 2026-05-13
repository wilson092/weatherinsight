<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('weather_histories', function (Blueprint $table) {
            $table->id();

            $table->string('city');

            $table->decimal('temperature', 5, 2);
            $table->decimal('humidity', 5, 2);
            $table->decimal('pressure', 8, 2);
            $table->decimal('wind_speed', 8, 2);

            $table->string('weather_main')->nullable();
            $table->string('weather_description')->nullable();
            $table->string('weather_icon')->nullable();

            $table->timestamp('recorded_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_histories');
    }
};
