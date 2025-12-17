<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    public function playerAccess($token)
    {
        session(['player_token' => $token]);
        $player = Player::where('token', $token)->first();
        if (!$player) {
            return redirect()->route('home')->with('error', 'Invalid token. Please use your unique link to access the player dashboard.');
        }

        return view('player.dashboard', ['player' => $player]);
    }

    public function playerDashboard()
    {
        $token = session('player_token');
        if (!$token) {
            return redirect()->route('home')->with('error', 'Access denied. Please use your unique link to access the player dashboard.');
        }

        $player = Player::where('token', $token)->first();
        if (!$player) {
            return redirect()->route('home')->with('error', 'Invalid token. Please use your unique link to access the player dashboard.');
        }

        return view('player.dashboard', ['player' => $player]);
    }

    public function showRules(Request $request)
    {
        return view('player.rules');
    }
}
