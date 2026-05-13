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

    $table->json('conditions');

    $table->text('recommendation');

    $table->enum('risk_level', [
        'low',
        'medium',
        'high'
    ]);

    $table->text('insight');

    $table->boolean('is_active')
        ->default(true);

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
