<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Auth\GenericUser;
use App\Models\Player;
use Illuminate\Support\Facades\Log;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web']]);

        Broadcast::resolveAuthenticatedUserUsing(function () {

            if (session('authenticated')) {
                return new GenericUser([
                    'id' => 0,
                    'name' => 'MJ',
                    'role' => 'mj',
                ]);
            }

            if (session()->has('player_token')) {
                $player = Player::where('token', session('player_token'))->first();

                if ($player) {
                    return new GenericUser([
                        'id' => $player->id,
                        'name' => $player->nom,
                        'role' => 'player',
                    ]);
                }
            }

            Log::warning('Broadcast auth failed: no authenticated user found in session', [
                'session_data' => session()->all(),
            ]);
            return null;
        });
    }
}
