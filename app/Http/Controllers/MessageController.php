<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\GameAnnouncement;
use App\Events\PrivateMessageSent;
use App\Models\Player;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class MessageController extends Controller
{
    // Envoi d'un message
    public function sendMessage(Request $request)
    {
        // Support robuste : on cherche à la racine OU dans 'params'
        $messageText = $request->input('message') ?? $request->input('params.message');
        $typeInput = $request->input('type') ?? $request->input('params.type');
        $reqTargetId = $request->input('target_id') ?? $request->input('params.target_id');
        $reqGameId = $request->input('game_id') ?? $request->input('params.game_id');

        $senderName = 'Système';
        $finalType = 'private';
        $targetId = null;

        // 1. Logique MJ
        if (session('authenticated')) {
            $senderName = 'MJ';
            $finalType = $typeInput;
            if ($finalType === 'private') {
                $targetId = $reqTargetId;
            } else {
                $targetId = $reqGameId;
            }

        // 2. Logique Joueur
        } elseif (session()->has('player_token')) {
            $player = Player::where('token', session('player_token'))->first();
            if (!$player) {
                return response()->json(['error' => 'Joueur introuvable'], 404);
            }
            $senderName = $player->nom;
            $finalType  = 'private';
            $targetId   = $player->id;
            $gameId     = $player->game_id;
        } else {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        if ($targetId !== null && !is_numeric($targetId)) {
            Log::error('Target ID invalide', ['target_id' => $targetId]);
            abort(500, 'Target ID invalide');
        }

        // 3. Sauvegarde
        $newMessage = Message::create([
            'sender_name' => $senderName,
            'message' => $messageText,
            'type' => $finalType,
            'target_id' => $targetId
        ]);

        // 4. Diffusion
        if ($finalType === 'public') {
            GameAnnouncement::dispatch($messageText, $reqGameId);
        } else {
            PrivateMessageSent::dispatch($messageText, $senderName, $targetId);
        }

        return response()->json(['status' => 'Message envoyé', 'data' => $newMessage]);
    }

    // Récupération de l'historique
    public function getMessageHistory(Request $request)
    {
        $type = $request->input('type') ?? $request->input('params.type');
        $targetId = $request->input('target_id') ?? $request->input('params.target_id');
        $reqGameId = $request->input('game_id') ?? $request->input('params.game_id');

        $query = Message::query();

        if ($type === 'public') {
            $query->where('type', 'public');
            if ($reqGameId) {
                $query->where('target_id', $reqGameId);
            }
        } else {
            $conversationId = null;

            if (session('authenticated')) {
                $conversationId = $targetId;
            } elseif (session()->has('player_token')) {
                $conversationId = $targetId;
            }
            if (!$conversationId) {
                return response()->json([]);
            }
            $query->where('type', 'private')
                  ->where('target_id', $conversationId);
        }
        $messages = $query->orderBy('created_at', 'desc')
                          ->take(100)
                          ->get()
                          ->reverse()
                          ->values();

        return response()->json($messages);
    }
}
