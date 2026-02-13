<?php

use App\Http\Controllers\TwitchAuthCallbackController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::dashboard')->name('dashboard');

Route::get('/auth/twitch/callback', TwitchAuthCallbackController::class)->name('twitch.auth.callback');

require __DIR__.'/settings.php';
