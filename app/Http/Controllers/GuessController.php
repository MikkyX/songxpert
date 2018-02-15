<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Session;

class GuessController extends Controller
{
    /**
     * Handle the user's guess
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Check to see if they got this one right
        if ($request->answer == session('answer')) {
            // If they did, they score up to 10 points, depending on answer speed
            $last_score = ceil((30 - $request->time) / 3);

            // Update the database
            if (Auth::check()) {
                Auth::user()->update([
                    'songs_seen' => \DB::raw('songs_seen + 1'),
                    'songs_correct' => \DB::raw('songs_correct + 1'),
                    'score' => \DB::raw('score + '.$last_score),
                ]);
            }

            $update = 'Right';
        } else {
            $last_score = 0;

            if (Auth::check()) {
                Auth::user()->increment('songs_seen');
            }

            $update = 'Wrong';
        }

        return redirect()->route('game')->with([
            'last_score' => $last_score,
            'update' => $update,
        ]);
    }
}
