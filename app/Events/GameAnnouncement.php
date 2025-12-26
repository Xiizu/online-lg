<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameAnnouncement implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $gameId;

    public function __construct($message, $gameId)
    {
        $this->message = $message;
        $this->gameId = $gameId;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('game-announcements.' . $this->gameId),
        ];
    }
}
