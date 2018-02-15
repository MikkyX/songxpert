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
        // Work out the number of points to add or subtract on this round
        $last_score = ceil((30 - $request->time) / 3);

        // Check to see if they got this one right
        if ($request->answer == session('answer')) {
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
            if (Auth::check()) {
                Auth::user()->update([
                    'songs_seen' => \DB::raw('songs_seen + 1'),
                    'score' => \DB::raw('score - '.$last_score),
                ]);
            }

            $update = 'Wrong';
        }

        return redirect()->route('game')->with([
            'last_score' => $last_score,
            'update' => $update,
        ]);
    }
}
