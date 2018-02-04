<?php

namespace App\Http\Controllers;

use Cache;
use Illuminate\Http\Request;
use Session;
use SpotifyWebAPI;

class GameController extends Controller
{
    private $spotifyApi;
    private $spotifyClient;
    private $spotifyChart;

    public function __construct()
    {
        // Attempt to get access token
        if (!Cache::has('accessToken')) {
            // Create the Spotify Client
            $this->spotifyClient = new SpotifyWebAPI\Session(
                env('SPOTIFY_CLIENT_ID'),
                env('SPOTIFY_CLIENT_SECRET')
            );

            // Attempt to get client_credentials token
            if ($this->spotifyClient->requestCredentialsToken()) {
                Cache::put(
                    'accessToken',
                    $this->spotifyClient->getAccessToken(),
                    $this->spotifyClient->getTokenExpiration() / 60 // Expiration is returned in seconds?
                );
            }
        }

        // Use access token to connect to API
        $this->spotifyApi = new SpotifyWebAPI\SpotifyWebAPI();
        $this->spotifyApi->setAccessToken(Cache::get('accessToken'));

        // Get the current UK Top 50
        if (!Cache::has('playlist')) {
            Cache::put(
                'playlist',
                $this->spotifyApi->getUserPlaylist('spotifycharts','37i9dQZEVXbLnolsZ8PSNw'),
                60
            );
        }

        $this->spotifyChart = Cache::get('playlist');
    }

    public function index()
    {
        // First we need to filter out any track which doesn't have a preview...
        $tracks = collect($this->spotifyChart->tracks->items)->filter(function($track) {
            return (!empty($track->track->preview_url));
        })->random(3);

        // The first track is the correct answer
        $correct_track = $tracks->first();

        // Shuffle the tracks for the form
        $answers = $tracks->shuffle();

        // The first answer is the correct one
        session(['answer' => $correct_track->track->id]);

        // Show the form
        return view('form',[
            'answers' => $answers,
            'track' => $correct_track->track,
        ]);
    }
}
