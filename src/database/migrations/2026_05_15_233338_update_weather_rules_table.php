<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weather_rules', function (Blueprint $table) {

            $table->integer('min_temp')->nullable();

            $table->integer('max_temp')->nullable();

            $table->dropColumn('conditions');
        });
    }

    public function down(): void
    {
        Schema::table('weather_rules', function (Blueprint $table) {

            $table->text('conditions')->nullable();

            $table->dropColumn([
                'min_temp',
                'max_temp',
            ]);
        });
    }
};