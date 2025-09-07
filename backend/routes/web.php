<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\DiscordController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
	return redirect()->route('dashboard');
});

Route::get('/auth/discord/redirect', [DiscordController::class, 'redirect'])->name('auth.discord');
Route::get('/auth/discord/callback', [DiscordController::class, 'callback']);
Route::post('/logout', [DiscordController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	Route::post('/rallies', [DashboardController::class, 'createRally'])->name('rallies.create');
	Route::post('/rallies/{rally}', [DashboardController::class, 'updateRally'])->name('rallies.update');
	Route::post('/reinforcements', [DashboardController::class, 'createReinforcement'])->name('reinforcements.create');
});