<?php

use App\Http\Controllers\TwitchAuthCallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/twitch/callback', TwitchAuthCallbackController::class)->name('twitch.auth.callback');
