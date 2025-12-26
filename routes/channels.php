<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('game-announcements', function ($user, $gameId) {
    return true;
});

Broadcast::channel('mj-chat.{playerId}', function ($user, $playerId) {
    return $user && ($user->role === 'mj' || (int) $user->id === (int) $playerId);
});


