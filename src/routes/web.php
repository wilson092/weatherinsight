<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'loginView'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'registerView']);

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/', WeatherDashboardController::class);

});

/*
|--------------------------------------------------------------------------
| Livewire Assets
|--------------------------------------------------------------------------
| NOTE: Do not remove
*/

Livewire::setUpdateRoute(function ($handle) {

    return Route::post(
        config('app.asset_prefix') . '/livewire/update',
        $handle
    );

});

Livewire::setScriptRoute(function ($handle) {

    return Route::get(
        config('app.asset_prefix') . '/livewire/livewire.js',
        $handle
    );

});

/*
|--------------------------------------------------------------------------
| END
|--------------------------------------------------------------------------
*/