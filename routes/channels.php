<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('game-announcements', function ($user) {
    Log::info("Authorizing user for channel game-announcements", ['user' => $user]);
    return true; // Tous les utilisateurs connectés peuvent écouter
});

Broadcast::channel('mj-chat.{playerId}', function ($user, $playerId) {
    Log::info("Attempting to authorize user for channel mj-chat.$playerId", ['user' => $user]);
    return $user && ($user->role === 'mj' || (int) $user->id === (int) $playerId);
});


