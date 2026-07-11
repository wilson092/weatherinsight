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
        Schema::create('weather_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rule_type')->comment('e.g., temperature, humidity, wind_speed, pressure');
            $table->string('operator')->comment('e.g., >, <, =, between');
            $table->decimal('threshold_value', 8, 2)->nullable()->comment('For single value comparisons');
            $table->decimal('min_value', 8, 2)->nullable()->comment('For range comparisons (min)');
            $table->decimal('max_value', 8, 2)->nullable()->comment('For range comparisons (max)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_rules');
    }
};
