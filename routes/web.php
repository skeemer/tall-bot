<?php

use App\Http\Controllers\TwitchAuthCallbackController;
use App\Http\Controllers\TwitchSecretsController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/twitch/callback', TwitchAuthCallbackController::class)->name('twitch.auth.callback');
Route::get('/twitch-secrets', TwitchSecretsController::class)->name('twitch.secrets');
