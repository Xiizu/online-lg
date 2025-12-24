<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $senderName;
    public $conversationId; // C'est toujours l'ID du joueur concerné

    public function __construct($message, $senderName, $conversationId)
    {
        $this->message = $message;
        $this->senderName = $senderName;
        $this->conversationId = $conversationId;
    }

    public function broadcastOn(): array
    {
        // On diffuse sur le canal privé unique du joueur
        return [
            new Channel('mj-chat.' . $this->conversationId),
        ];
    }
}
