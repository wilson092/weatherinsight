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
    Schema::table('weather_histories', function (Blueprint $table) {
        $table->text('recommendation')->nullable();

        $table->text('insight')->nullable();

        $table->string('risk_level')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weather_histories', function (Blueprint $table) {
            //
        });
    }
};
