<?php

namespace App\Http\Controllers;

use App\Actions\SetTwitchConnectionChannel;
use App\Models\TwitchConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TwitchAuthCallbackController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // TODO Handle canceled auth request

        $results = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
            'client_id' => config('services.twitch.client_id'),
            'client_secret' => config('services.twitch.client_secret'),
            'code' => $request->query('code'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => route('twitch.auth.callback'),
        ]);

        $data = $results->json();
        $tc = new TwitchConnection;
        $tc->fill($data);
        $tc->expires_at = now()->addSeconds($data['expires_in']);
        $tc->save();

        SetTwitchConnectionChannel::run($tc);

        return response()->redirectToRoute('dashboard')->with('success', 'Successfully connected to Twitch!');
    }
}
