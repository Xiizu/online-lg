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

        $senderName = 'Système';
        $finalType = 'private';
        $targetId = null;

        // 1. Logique MJ
        if (session('authenticated')) {
            $senderName = 'MJ';
            $finalType = $typeInput;

            if ($finalType === 'private') {
                $targetId = $reqTargetId;
            }

        // 2. Logique Joueur
        } elseif (session()->has('player_token')) {
            $player = Player::where('token', session('player_token'))->first();
            if (!$player) {
                return response()->json(['error' => 'Joueur introuvable'], 404);
            }
            $senderName = $player->nom;
            $finalType  = 'private';
            $targetId   = $player->id; // 🔒 ID NUMÉRIQUE UNIQUEMENT
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
            GameAnnouncement::dispatch($messageText);
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

        // DEBUG
        error_log("--------------------------------------------------");
        error_log("GetMessageHistory START | Type: " . ($type ?? 'VIDE') . " | TargetId: " . ($targetId ?? 'VIDE'));

        $query = Message::query();

        if ($type === 'public') {
            $query->where('type', 'public');
        } else {
            $conversationId = null;

            if (session('authenticated')) {
                // MJ : regarde la conv demandée
                $conversationId = $targetId;
                error_log("Auth: MJ détecté");
            } elseif (session()->has('player_token')) {
                // Joueur : regarde SA conv (basée sur l'ID envoyé par le front, validé par le token en session)
                $conversationId = $targetId;
                error_log("Auth: Joueur détecté (TargetID: $conversationId)");
            } else {
                error_log("Auth: AUCUNE SESSION TROUVÉE");
            }

            error_log("ConversationID résolu: " . ($conversationId ?? 'NULL'));

            if (!$conversationId) {
                error_log("ERREUR: Aucun ID de conversation, retour vide.");
                return response()->json([]);
            }

            $query->where('type', 'private')
                  ->where('target_id', $conversationId);
        }

        // On prend les 100 derniers messages (trié par date décroissante puis inversé)
        $messages = $query->orderBy('created_at', 'desc')
                          ->take(100)
                          ->get()
                          ->reverse()
                          ->values();

        return response()->json($messages);
    }

    /* public function broadcastAuth(Request $request)
    {
        $user = null;
        if (session('authenticated')) {
            $user = new GenericUser(['id' => 0, 'name' => 'MJ', 'role' => 'mj']);
        }
        elseif (session()->has('player_token')) {
            $token = session('player_token');
            $player = Player::where('token', $token)->first();
            if ($player) {
                $user = new GenericUser(['id' => $player->id, 'name' => $player->nom, 'role' => 'player']);
            } else {
                abort(403, 'Joueur introuvable -> ' . json_encode($player));
            }
        }
        else {
            abort(403, 'Accès refusé');
        }
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        return Broadcast::auth($request);
    } */
}
