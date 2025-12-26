@php
    use App\Models\Game;
    use App\Models\Player;

    // Sécurités
    if (!isset($gameId)) { echo "<div class='alert alert-danger'>Erreur : gameId manquant.</div>"; return; }
    $game = Game::find($gameId);
    if (!$game) { echo "<div class='alert alert-danger'>Jeu introuvable.</div>"; return; }

    $players = $game->players()->get();
    $auth = null;
    $currentUserName = 'Moi';
    $currentUserId = '';
    if (session()->has('authenticated') && session('authenticated')) {
        $auth = "mj";
        $currentUserName = "MJ";
    } elseif (session()->has('player_token')) {
        $auth = "player";
        $p = Player::where('token', session('player_token'))->first();
        if($p) {
            $currentUserName = $p->nom;
            $currentUserId = $p->id;
        } else {
            return;
        }
    } else {
        return;
    }
    $playerIds = $players->pluck('id')->toArray();
@endphp

<!-- 1. BOUTON FLOTTANT (Déclencheur) -->
<button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
        style="position: fixed; bottom: 30px; left: 30px; width: 60px; height: 60px; z-index: 1040; transition: transform 0.2s;"
        data-bs-toggle="offcanvas"
        data-bs-target="#chatOffcanvas"
        aria-controls="chatOffcanvas"
        type="button"
        id="chat-trigger-btn"
        onmouseover="this.style.transform='scale(1.1)'"
        onmouseout="this.style.transform='scale(1)'"
        title="Messages">
    <i class="bi bi-chat-dots fs-3"></i>
    <span id="global-chat-badge" class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle d-none">
        <span class="visually-hidden">Nouveaux messages</span>
    </span>
</button>

<!-- 2. OFFCANVAS (Panneau latéral) -->
<!-- data-bs-scroll="true" : Permet de cliquer sur le jeu même avec le chat ouvert -->
<!-- data-bs-backdrop="false" : Enlève le fond gris sombre -->
<div class="offcanvas offcanvas-start shadow"
     data-bs-scroll="true"
     data-bs-backdrop="false"
     tabindex="-1"
     id="chatOffcanvas"
     aria-labelledby="chatOffcanvasLabel"
     style="max-width: 100%; width: 100%;">

    <div class="offcanvas-header bg-dark text-white">
        <h5 class="offcanvas-title" id="chatOffcanvasLabel"><i class="fas fa-comments me-2"></i>Messagerie</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column" style="overflow: hidden;">
        <div class="d-flex flex-grow-1 h-100">
            @if($auth === 'mj')
                <div class="player-list bg-secondary text-white p-0 d-flex flex-column" style="overflow-y: auto;">
                    <div class="p-2 border-bottom border-secondary bg-dark text-center small"
                         onclick="window.selectChannel('public');"
                         style="cursor: pointer;"
                         id="tab-public" title="Annonces">
                        <div class="fs-4">📢</div>
                    </div>
                    <div id="mj-players-list">
                        @foreach($players as $player)
                            <div class="player-item p-2 border-bottom border-dark text-center position-relative"
                                 onclick="window.selectChannel({{ $player->id }}, '{{ addslashes($player->nom) }}');"
                                 id="player-tab-{{ $player->id }}"
                                 style="cursor: pointer; font-size: 0.8rem;"
                                 title="{{ $player->nom }}">
                                <!-- Badge individuel -->
                                <span class="badge bg-danger position-absolute top-0 end-0 rounded-circle p-1 d-none" id="badge-{{ $player->id }}" style="transform: translate(-25%, 25%); width: 10px; height: 10px;"></span>
                                <div class="bg-light text-dark rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    {{ substr($player->nom, 0, 1) }}
                                </div>
                                <div class="text-truncate">{{ $player->nom }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- VUE JOUEUR (Sidebar gauche) --}}
            @if($auth === 'player')
                <div class="player-tabs bg-secondary text-white d-flex flex-column" style="">
                    <div class="p-3 text-center border-bottom border-dark bg-dark"
                         onclick="window.selectChannel('public');"
                         title="Annonces Publiques"
                         style="cursor:pointer;" id="tab-public">
                        <div class="fs-4">📢</div>
                    </div>
                    <div class="p-3 text-center border-bottom border-dark"
                         onclick="window.selectChannel('mj');"
                         title="Parler au MJ"
                         style="cursor:pointer;" id="tab-mj">
                        <div class="fs-4">🕵️</div>
                        <span class="badge bg-danger rounded-pill d-none" id="badge-mj" style="font-size: 0.6rem;">!</span>
                    </div>
                </div>
            @endif

            <!-- ZONE DE DISCUSSION (Reste de la largeur) -->
            <div class="chat-content flex-grow-1 d-flex flex-column bg-white text-dark" style="min-width: 0;">
                <div id="chat-title" class="p-2 border-bottom fw-bold bg-light text-truncate">
                    Sélectionnez un canal...
                </div>

                <div id="messages-box" class="flex-grow-1 p-3" style="overflow-y: auto;">
                    <div class="text-center mt-5">Bienvenue.<br>Cliquez sur une icône à gauche.</div>
                </div>

                <div class="p-2 border-top bg-light">
                    <form id="chat-form" onsubmit="window.sendMessage(event)">
                        <div class="input-group">
                            <input type="text" id="message-input" class="form-control" placeholder="Message..." autocomplete="off">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-send"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .player-item:hover, .player-tabs div:hover { background-color: #495057; }
    .active-channel { background-color: #0d6efd !important; color: white; }

    .message-bubble {
        padding: 8px 12px;
        border-radius: 15px;
        margin-bottom: 8px;
        max-width: 85%;
        font-size: 0.9em;
        word-wrap: break-word;
    }
    .message-mine { background-color: #0d6efd; color: white; align-self: flex-end; margin-left: auto; border-bottom-right-radius: 2px; border: 1px solid #000000; }
    .message-other { background-color: #e9ecef; color: black; align-self: flex-start; border-bottom-left-radius: 2px; border: 1px solid #000000; }
    .system-msg { background-color: #fff3cd; border: 1px solid #ffecb5; width: 90%; text-align: center; color: #856404; font-size: 0.8em; margin: 0 auto 8px auto; border: 1px solid #000000; }
</style>

<script>
    // Correction : On injecte l'ID résolu (int) et non le token brut
    const USER_ID = "{{ $currentUserId }}";
    const USER_ROLE = "{{ session('authenticated') ? 'mj' : 'player' }}";
    const MY_NAME = "{{ $currentUserName }}";
    const GAME_ID = "{{ $game->id ?? '' }}";
    const ALL_PLAYER_IDS = @json($playerIds);

    let currentChannel = 'public';
    let currentTargetName = 'Annonces';

    document.addEventListener('DOMContentLoaded', () => {
        // Vérifier si Echo (le système de diffusion en temps réel) est chargé
        let attempts = 0;
        const checkEcho = setInterval(() => {
            if (typeof window.Echo !== 'undefined') {
                clearInterval(checkEcho);
                initializeChatSystem();
            } else {
                attempts++;
                if (attempts > 50) {
                    clearInterval(checkEcho);
                    console.error("❌ Impossible de charger Laravel Echo. Vérifiez que 'npm run build' est bien lancé.");
                    document.getElementById('connection-status').innerText = "Erreur JS";
                    document.getElementById('connection-status').className = "badge bg-danger ms-2";
                }
            }
        }, 100);
    });
    function initializeChatSystem() {
        console.log("✅ Laravel Echo chargé avec succès.");

        Echo.connector.pusher.connection.bind('connected', () => {
            const statusEl = document.getElementById('connection-status');
            if(statusEl) {
                statusEl.innerText = "Connecté";
                statusEl.className = "badge bg-success ms-2";
                setTimeout(() => { statusEl.style.display = 'none'; }, 2000);
            }
            console.log("✅ Connecté à Reverb.");
        });

        // Si la connexion est perdue
        Echo.connector.pusher.connection.bind('unavailable', () => {
            const statusEl = document.getElementById('connection-status');
            if(statusEl) {
                statusEl.className = 'badge bg-danger ms-2';
                statusEl.innerText = 'Déconnecté';
                statusEl.style.display = 'inline-block';
            }
            console.error("❌ Déconnecté de Reverb.");
        });

        // Ecouter les annonces mj
        Echo.channel('game-announcements.' + GAME_ID)
            .listen('GameAnnouncement', (e) => {
                window.addMessage('public', 'MJ (Annonce)', e.message, true);
                if (currentChannel !== 'public') window.notifyNewMessage('public');
            });

        // S'assurer que l'utilisateur est authentifié coté laravel avant d'écouter les messages privés
        if ( !USER_ID && USER_ROLE !== 'mj' ) {
            console.error("❌ Utilisateur non authentifié pour les messages privés.");
            return;
        } else {
            console.log("✅ Utilisateur authentifié pour les messages privés.");
        }
        // Ecouter les messages privés en tant que MJ
        if (USER_ROLE === 'mj') {
            // Pour chaque joueur, écouter son canal privé
            ALL_PLAYER_IDS.forEach(playerId => {
                // Lancer l'écoute sur le canal privé du joueur
                Echo.channel('mj-chat.' + playerId)
                    .listen('PrivateMessageSent', (e) => {
                        if (currentChannel != playerId) {
                            window.notifyNewMessage(playerId);
                        } else {
                            window.addMessage(playerId, e.senderName, e.message);
                        }
                    });
            });
        // Ecouter les messages privés en tant que Joueur
        } else if (USER_ID) {
            // Lancer l'écoute sur le canal privé du joueur
            Echo.channel('mj-chat.' + USER_ID)
                .listen('PrivateMessageSent', (e) => {
                    window.addMessage('mj', e.senderName, e.message);
                    if (currentChannel !== 'mj') window.notifyNewMessage('mj');
                });
        }
        // Sélectionner le canal par défaut
        window.selectChannel('public');
        // Cacher le badge global à l'ouverture du panneau
        const offcanvasEl = document.getElementById('chatOffcanvas');
        if(offcanvasEl) {
            offcanvasEl.addEventListener('shown.bs.offcanvas', () => {
                document.getElementById('global-chat-badge')?.classList.add('d-none');
            });
        }
    }

    // Sélectionner un canal de discussion
    window.selectChannel = function(channelId, name = null) {
        // Met à jour le canal courant avec public/mj/id_joueur
        currentChannel = channelId;
        // Met à jour le nom affiché
        currentTargetName = name || (channelId === 'public' ? '📢 Annonces' : '🕵️ Privé MJ');
        // Met à jour le titre du chat
        const titleEl = document.getElementById('chat-title');
        // Met à jour le titre
        if(titleEl) titleEl.innerText = currentTargetName;
        // si on a un badge, le cacher
        if (channelId !== 'public') {
            const badgeId = (channelId === 'mj') ? 'badge-mj' : 'badge-' + channelId;
            const badge = document.getElementById(badgeId);
            if(badge) badge.classList.add('d-none');
        }
        // Charger l'historique des messages via AJAX
        const msgBox = document.getElementById('messages-box');
        // Vider la boîte de messages
        if(msgBox) {
            // Afficher un indicateur de chargement
            msgBox.innerHTML = '<div class="text-center mt-5"><div class="spinner-border spinner-border-sm"></div> Chargement...</div>';
            // Préparer les paramètres
            let realTargetId = null;
            // Déterminer le type de canal et l'ID cible
            if (channelId !== 'public') {
                if (USER_ROLE === 'mj') {
                    realTargetId = channelId;
                } else {
                    realTargetId = USER_ID;
                }
            }
            // Envoyer la requête AJAX pour récupérer l'historique
            let historyParams = {
                type: (channelId === 'public') ? 'public' : 'private',
                target_id: realTargetId,
                game_id: GAME_ID
            };
            // Requête AJAX avec Axios
            axios.post('{{ route("messages.history") }}', { params: historyParams })
                .then(response => {
                    // Vider la boîte de messages
                    msgBox.innerHTML = '';
                    const messages = response.data;
                    // Afficher les messages
                    if(messages.length > 0) {
                        // Si le canal est public
                        if(channelId === 'public') {
                            // Afficher chaque message sous forme d'annonces
                            messages.forEach(msg => {
                            window.addMessage(currentChannel, msg.sender_name, msg.message, true);
                            });
                        // sinon canal privé
                        } else {
                            // Afficher chaque message normalement
                            messages.forEach(msg => {
                                window.addMessage(currentChannel, msg.sender_name, msg.message, false);
                            });
                        }
                    }
                    // donner le focus à l'input
                    const input = document.getElementById('message-input');
                    if(input) input.focus();
                })
                .catch(err => {
                    // Gérer les erreurs
                    console.error("Erreur historique:", err);
                    msgBox.innerHTML = '<div class="text-center text-danger mt-5 small">Erreur chargement.</div>';
                });
        }
        // Gérer l'état du formulaire en fonction du rôle et du canal
        const input = document.getElementById('message-input');
        const btn = document.querySelector('#chat-form button');
        // Si l'utilisateur n'est pas MJ et le canal est public, désactiver l'envoi
        if (USER_ROLE !== 'mj' && channelId === 'public') {
            if(input) { input.disabled = true; input.placeholder = "Lecture seule..."; }
            if(btn) btn.disabled = true;
        } else {
            if(input) { input.disabled = false; input.placeholder = "Message..."; }
            if(btn) btn.disabled = false;
        }
        // Met à jour l'état actif dans la liste des canaux
        document.querySelectorAll('.active-channel').forEach(el => el.classList.remove('active-channel'));
        if (channelId === 'public') {
            document.getElementById('tab-public')?.classList.add('active-channel');
        } else if (channelId === 'mj') {
             document.getElementById('tab-mj')?.classList.add('active-channel');
        } else {
            document.getElementById('player-tab-' + channelId)?.classList.add('active-channel');
        }
    };

    // Envoyer un message
    window.sendMessage = function(e) {
        console.log("Envoi message sur canal:", currentChannel);
        // Empêcher le rechargement de la page
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value;
        input.value = 'Chargement ...';
        input.disabled = true;

        if (!message.trim()) return;

        let realTargetId = null;
        let msgType = (currentChannel === 'public') ? 'public' : 'private';
        if (msgType === 'private') {
            if (USER_ROLE === 'mj') {
                realTargetId = currentChannel;
            } else {
                realTargetId = USER_ID;
            }
        }
        let payload = {
            message: message,
            type: msgType,
            target_id: realTargetId,
            game_id: GAME_ID
        };

        //window.addMessage(currentChannel, 'Moi', message);
        //input.value = '';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        axios.post('{{ route("messages.send") }}', payload, { headers: { 'X-CSRF-TOKEN': csrfToken } })
            .catch(err => { alert("Erreur d'envoi"); })
            .finally(() => {
                input.disabled = false;
                input.value = '';
                input.focus();
            });
    };

    // Ajouter un message à la boîte de discussion
    window.addMessage = function(targetChannel, sender, text, isSystem = false) {
        // N'afficher le message que si on est sur le bon canal
        if (currentChannel != targetChannel && targetChannel !== 'mj') return;
        // Récupérer la boîte de messages
        const box = document.getElementById('messages-box');
        if(!box) return;
        // Créer un nouvel élément de message
        const div = document.createElement('div');
        // Appliquer les styles en fonction du type de message
        if (isSystem) {
            div.className = 'message-bubble system-msg mx-auto';
            div.innerHTML = `<strong>MJ</strong> :<br> ${text}`;
        } else {
            const isMe = sender === 'Moi' || sender === MY_NAME;
            div.className = `message-bubble ${isMe ? 'message-mine' : 'message-other'}`;
            div.innerHTML = `<div style="font-size:0.7em; opacity:0.7; margin-bottom:2px;">${sender}</div>${text}`;
        }
        // Ajouter le message à la boîte de discussion
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
    };

    window.notifyNewMessage = function(channelId) {
        if (channelId !== 'public') {
             const badgeId = (channelId === 'mj') ? 'badge-mj' : 'badge-' + channelId;
             const badge = document.getElementById(badgeId);
             if(badge) badge.classList.remove('d-none');
        }
        const offcanvasEl = document.getElementById('chatOffcanvas');
        if (!offcanvasEl.classList.contains('show')) {
            const globalBadge = document.getElementById('global-chat-badge');
            if(globalBadge) globalBadge.classList.remove('d-none');
        }
    };
</script>
