<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ClimateController;

Route::get('/', [ClimateController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [ClimateController::class, 'index'])->name('dashboard.page');
Route::get('/api/weather', [ClimateController::class, 'getSolarData'])->name('weather.data');
Route::get('/api/solar-data', [ClimateController::class, 'getSolarData'])->name('solar.data');
