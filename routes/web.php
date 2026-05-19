<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClimateController;

Route::get('/', [ClimateController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [ClimateController::class, 'index'])->name('dashboard.page');
Route::get('/api/weather', [ClimateController::class, 'getWeatherData'])->name('weather.data');
