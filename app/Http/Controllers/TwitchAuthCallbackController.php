<?php

namespace App\Http\Controllers;

use App\Actions\FillTwitchConnectionInformation;
use App\Models\TwitchConnection;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Native\Desktop\Facades\Window;

class TwitchAuthCallbackController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|ResponseFactory|Response
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

        FillTwitchConnectionInformation::run($tc);

        if (Window::get('oauth')) {
            Window::close('oauth');

            return response('');
        }

        return response()->redirectToRoute('dashboard')->with('success', 'Successfully connected to Twitch!');
    }
}
