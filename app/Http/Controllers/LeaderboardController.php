<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboard = User::orderBy('score','desc')
            ->orderBy('songs_correct','asc')
            ->get();

        return view('leaderboard',[
            'leaderboard' => $leaderboard,
        ]);
    }
}
