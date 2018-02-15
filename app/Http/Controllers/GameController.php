<?php

namespace App\Http\Controllers;

use Auth;
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
            // Grab a random 100 tracks from the "every UK number one ever" playlist
            $offset = rand(0,900);

            Cache::put(
                'playlist',
                $this->spotifyApi->getUserPlaylistTracks('officialcharts','5GEf0fJs9xBPr5R4jEQjtw',[
                    'offset' => $offset,
                ]),
                2
            );
        }

        $this->spotifyChart = Cache::get('playlist');
    }

    /**
     * Show the form with the playing song and the three guess inputs
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get our session of recently used tracks
        $recents = (session('recents') ?: collect());

        // Run some filters over the track list
        $tracks = collect($this->spotifyChart->items)->reject(function($track) use ($recents) {
            // Reject the track if it doesn't have a preview URL
            // or it appears in the recents list
            return $this->_trackHasNoPreview($track->track)
                || $this->_trackIsRecent($track->track->id);
        })->shuffle()->take(3);

        // The first track is the correct answer
        $correct_track = $tracks->first();
        $correct_answer = $correct_track->track->id;

        // Add this to the "recently used" session
        $recents = $recents->push($correct_answer);

        // Make sure we only store the last 20
        if ($recents->count() >= 20) {
            $recents = $recents->slice(1,20)->values();
        }

        // Store it back in the session
        session([
            'recents' => $recents
        ]);

        // Shuffle the tracks for the form
        $answers = $tracks->shuffle();

        // The first answer is the correct one
        session(['answer' => $correct_answer]);

        // Show the form
        return view('form',[
            'answers' => $answers,
            'last_score' => session('last_score') ?: '',
            'track' => $correct_track->track,
            'update' => session('update') ?: '',
        ]);
    }

    /**
     * Handle a timeout
     */
    public function timeout()
    {
        // Update the songs heard counter
        if (Auth::check()) {
            Auth::user()->increment('songs_seen');
        }

        // Redirect back
        return redirect()->route('game')->with([
            'update' => 'Timeout',
        ]);
    }

    /**
     * Test to see if a track has a valid preview URL
     *
     * @param $track
     * @return bool
     */
    private function _trackHasNoPreview($track)
    {
        return empty($track->preview_url);
    }

    /**
     * Test to see if a track has been recently used as a correct answer
     *
     * @param $track_id
     * @return bool
     */
    private function _trackIsRecent($track_id)
    {
        $recents = (session('recents') ?: collect());
        return $recents->contains($track_id);
    }
}
