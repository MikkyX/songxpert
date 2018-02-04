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
                $tokenExpiryMinutes = floor(($this->spotifyClient->getTokenExpiration() - time()) / 60);

                Cache::put(
                    'accessToken',
                    $this->spotifyClient->getAccessToken(),
                    $tokenExpiryMinutes
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

    /**
     * Show the form with the playing song and the three guess inputs
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
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
            'last_score' => session('last_score') ?: '',
            'track' => $correct_track->track,
            'update' => session('update') ?: '',
        ]);
    }

    /**
     * Handle a guess
     *
     * @param $request
     * @return redirect
     */
    public function guess(Request $request)
    {
        // Load the old session
        $correct = session('correct') ?: 0;
        $score = session('score') ?: 0;

        // Check to see if they got this one right
        if ($request->answer == session('answer')) {
            // If they did, they score up to 5 points, depending on answer speed
            $correct++;
            $last_score = ceil((30 - $request->time) / 6);
            $score += $last_score;
            $update = 'Right';
        } else {
            $last_score = 0;
            $update = 'Wrong';
        }

        // Store the updated stats in a session
        session([
            'correct' => $correct,
            'heard' => session('heard') ? session('heard') + 1 : 1,
            'score' => $score,
        ]);

        return redirect('/')->with([
            'last_score' => $last_score,
            'update' => $update,
        ]);
    }
}
