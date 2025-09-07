<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BotController;

Route::middleware('api_token')->group(function () {
	Route::get('/rallies/pending', [BotController::class, 'pendingRallies']);
	Route::post('/rallies/{id}/ack', [BotController::class, 'ack']);
	Route::get('/discord/guilds', [BotController::class, 'guilds']);
	Route::get('/discord/guilds/{id}/channels', [BotController::class, 'channels']);
});