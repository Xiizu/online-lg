@extends('layouts.app')
@section('title', 'Gestion de la Partie')
@section('body')
    @php
        function getColor($field)
            {
                $field = strtolower($field);
                switch ($field) {
                    case 'lumineuse':
                        $color = '#eeea87';
                        break;
                    case 'obscure':
                        $color = '#c99d9d';
                        break;
                    case 'neutre':
                        $color = '#8a56b7';
                        break;
                    case 'brouillée':
                        $color = '#d0d0d0';
                        break;
                    case 'humaine':
                        $color = '#05ff00';
                        break;
                    case 'loup':
                        $color = '#ff0000';
                        break;
                    case 'divine':
                        $color = '#5ce1e6';
                        break;
                    default:
                        $color = '#8c8c8c';
                        break;
                }
                return $color;
            }
    @endphp
    <div class="container mt-4">
        <h1 class="mb-4">Gestion de la Partie: {{ $game->name }}</h1>
        <p>Date: {{ $game->date }}</p>
        <p>Status: {{ $game->status }}</p>
        @php
            use App\Models\Liage;
            $liage = Liage::where('first_game_id', $game->id)->orWhere('second_game_id', $game->id)->first();
        @endphp

        <h2>Joueurs</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Rôle</th>
                    <th>Camp</th>
                    <th>Aura</th>
                    <th>Apparence</th>
                    <th>Statut</th>
                    <th>Etats</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($players as $player)
                    <tr id="{{ $player->id }}" onclick="editModal({{ $player->id }})" style="cursor: pointer;">
                        <td>{{ $player->nom }}</td>
                        <td>{{ $player->role->nom }}</td>
                        <td>
                            @if ($player->camp)
                                <span class="badge"
                                    style="background-color: {{ $player->camp->color }}">{{ $player->camp->name }}</span>
                            @else
                                <span class="badge bg-secondary">Non assigné</span>
                            @endif
                        </td>

                        <td><span class="badge"
                                style="background-color:{{ getColor($player->role->aura) }}">{{ $player->role->aura }}</span>
                        </td>
                        <td><span class="badge"
                                style="background-color:{{ getColor($player->role->apparence) }}">{{ $player->role->apparence }}</span>
                        </td>
                        <td>
                            @if ($player->is_alive)
                                <span class="badge bg-success">Vivant</span>
                            @else
                                <span class="badge bg-danger">Mort</span>
                            @endif
                        </td>
                        <td>
                            @if ($player->etats->isEmpty())
                                <span class="badge bg-secondary">Aucun</span>
                            @endif
                            @foreach ($player->etats as $etat)
                                <span class="badge"
                                    style="background-color: {{ $etat->color }}">{{ $etat->label }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div style="overflow: auto; max-height: 5rem;">
                                {{ $player->comment }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit Player Modal -->
    <div class="modal fade" id="editPlayerModal" tabindex="-1" aria-labelledby="editPlayerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlayerModalLabel">Modifier le Joueur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="hideModal()"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="loadingSpinner" class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="modal-content-placeholder" style="display: none;">
                        <form class="text-start">
                            <div class="row g-3">
                                <input type="hidden" id="modal_player_id" value="">

                                {{-- Champs cachés pour la logique JS de liaison --}}
                                @if ($liage)
                                    <input type="hidden" id="liage_first_id" value="{{ $liage->first_game_id }}">
                                    <input type="hidden" id="liage_second_id" value="{{ $liage->second_game_id }}">
                                @endif

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="modal_nom" placeholder="Nom">
                                        <label for="modal_nom">Nom</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="modal_role" placeholder="Rôle">
                                            <option selected>role</option>
                                        </select>
                                        <label for="modal_role">Rôle</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <a href="" id="player_page_link" target="_blank"></a>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="modal_camp" placeholder="Camp">
                                            <option value="">Aucun camp</option>
                                        </select>
                                        <label for="modal_camp">Camp</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <label class="form-check-label" for="modal_isAlive">Mort</label>
                                        <div class="form-check form-switch ms-2">
                                            <input class="form-check-input" type="checkbox" id="modal_isAlive">
                                            <label class="form-check-label" for="modal_isAlive">En vie</label>
                                        </div>
                                    </div>
                                    @if (!$game->isSingleGame() && $liage)
                                        @php
                                            $currentGame =
                                                $game->id === $liage->firstGame->id
                                                    ? $liage->firstGame
                                                    : $liage->secondGame;
                                            $otherGame =
                                                $game->id === $liage->firstGame->id
                                                    ? $liage->secondGame
                                                    : $liage->firstGame;
                                        @endphp
                                        <input type="hidden" id="current_game_id" value="{{ $currentGame->id }}">
                                        <input type="hidden" id="other_game_id" value="{{ $otherGame->id }}">
                                        <div class="d-flex align-items-center mt-2">
                                            <label class="form-check-label"
                                                for="modal_village">{{ $currentGame->name }}</label>
                                            <div class="form-check form-switch ms-2">
                                                <input class="form-check-input" type="checkbox" id="modal_village">
                                                <label class="form-check-label"
                                                    for="modal_village">{{ $otherGame->name }}</label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12">
                                    <label for="modal_etats" class="form-label">États (Ctrl+Clic pour plusieurs)</label>
                                    <select class="form-select" id="modal_etats" name="etats[]" multiple size="6">
                                        <!-- Les options sont chargées au démarrage via JS -->
                                    </select>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="modal_comment" placeholder="Commentaire" style="height: 120px"></textarea>
                                        <label for="modal_comment">Commentaire</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                                    onclick="hideModal()">Annuler</button>
                                <button type="submit" class="btn btn-primary" id="edit_player">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Promesses pour charger les données une seule fois
        let rolesPromise;
        let campsPromise;
        let etatsPromise;
        // Cache des camps par ID pour un accès immédiat (ex: couleur)
        const campsById = {};

        document.addEventListener('DOMContentLoaded', () => {
            // Rôles
            rolesPromise = axios.post('{{ route('admin.roles.list') }}')
                .then(function(response) {
                    const roles = response.data;
                    const roleSelect = document.getElementById('modal_role');
                    roles.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.id;
                        option.text = role.nom;
                        roleSelect.appendChild(option);
                    });
                    return roles;
                })
                .catch(err => {
                    console.error('Error fetching roles:', err);
                    return [];
                });

            // Camps
            campsPromise = axios.post('{{ route('admin.camps.list') }}')
                .catch(() => {
                    // Fallback: charger les camps depuis les rôles
                    return axios.post('{{ route('admin.roles.list') }}')
                        .then(res => {
                            const camps = new Map();
                            res.data.forEach(role => {
                                if (role.camps) {
                                    role.camps.forEach(camp => {
                                        camps.set(camp.id, camp);
                                    });
                                }
                            });
                            return {
                                data: Array.from(camps.values())
                            };
                        });
                })
                .then(function(response) {
                    const camps = response.data;
                    const campSelect = document.getElementById('modal_camp');
                    camps.forEach(camp => {
                        const option = document.createElement('option');
                        option.value = camp.id;
                        option.text = camp.name;
                        // Stocker pour utilisation lors de la mise à jour du tableau
                        campsById[camp.id] = camp;
                        campSelect.appendChild(option);
                    });
                    return camps;
                })
                .catch(err => {
                    console.error('Error fetching camps:', err);
                    return [];
                });

            // États : On remplit la liste complète ici
            etatsPromise = axios.post('{{ route('admin.etats.list') }}')
                .then(function(response) {
                    const etats = response.data;
                    const etatsSelect = document.getElementById('modal_etats');
                    etatsSelect.innerHTML = ''; // Nettoyage initial
                    etats.forEach(etat => {
                        const option = document.createElement('option');
                        option.value = etat.id;
                        option.text = etat.label;
                        option.dataset.color = etat.color; // Stocker la couleur pour usage ultérieur
                        etatsSelect.appendChild(option);
                    });
                    return etats;
                })
                .catch(err => {
                    console.error('Error fetching etats:', err);
                    return [];
                });
        });

        const editPlayerBtn = document.getElementById('edit_player');

        editPlayerBtn.addEventListener('click', function(event) {
            event.preventDefault();

            // Récupération des champs
            var nomInput = document.getElementById('modal_nom');
            var roleSelect = document.getElementById('modal_role');
            var campInput = document.getElementById('modal_camp');
            var isAliveCheckbox = document.getElementById('modal_isAlive');
            var etatsSelect = document.getElementById('modal_etats');
            var commentTextarea = document.getElementById('modal_comment');
            var playerIdInput = document.getElementById('modal_player_id');

            // Logique Multi-Village
            var villageSwitch = document.getElementById('modal_village');
            var targetGameId = null;
            if (villageSwitch) {
                const currentIdInput = document.getElementById('current_game_id');
                const otherIdInput = document.getElementById('other_game_id');
                if (currentIdInput && otherIdInput) {
                    // Si coché => autre jeu, sinon => jeu courant
                    targetGameId = villageSwitch.checked ? otherIdInput.value : currentIdInput.value;
                } else {
                    // Rétrocompatibilité: fallback sur first/second ids si présents
                    const firstIdEl = document.getElementById('liage_first_id');
                    const secondIdEl = document.getElementById('liage_second_id');
                    if (firstIdEl && secondIdEl) {
                        targetGameId = villageSwitch.checked ? secondIdEl.value : firstIdEl.value;
                    }
                }
            }

            // Récupère les valeurs sélectionnées (multiple)
            const selectedEtats = Array.from(etatsSelect.selectedOptions).map(option => option.value);

            const payload = {
                nom: nomInput.value,
                role_id: roleSelect.value,
                camp_id: campInput.value || null,
                is_alive: isAliveCheckbox.checked,
                etats: selectedEtats,
                comment: commentTextarea.value
            };

            // Ajout game_id seulement si on est dans une partie liée
            if (targetGameId) {
                payload.game_id = targetGameId;
            }

            axios.post('{{ route('admin.players.update', ['id' => '__replace__']) }}'.replace('__replace__',
                    playerIdInput.value), payload)
                .then(function(response) {

                    // Si le joueur a changé de partie, on recharge la page pour mettre à jour la liste
                    if (targetGameId && targetGameId != {{ $game->id }}) {
                        window.location.reload();
                        return;
                    }

                    // Sinon, Mise à jour du tableau HTML
                    var playerRow = document.getElementById(playerIdInput.value);
                    playerRow.cells[0].innerText = nomInput.value;
                    playerRow.cells[1].innerText = roleSelect.options[roleSelect.selectedIndex].text;
                    playerRow.cells[2].innerHTML = campInput.value ?
                        '<span class="badge" style="background-color: ' + (campsById[campInput.value]?.color ||
                            '#6c757d') + '">' + campInput.options[campInput.selectedIndex].text + '</span>' :
                        '<span class="badge bg-secondary">Non assigné</span>';
                    playerRow.cells[5].innerHTML = isAliveCheckbox.checked ?
                        '<span class="badge bg-success">Vivant</span>' :
                        '<span class="badge bg-danger">Mort</span>';

                    var etatsCell = playerRow.cells[6];
                    etatsCell.innerHTML = '';
                    if (selectedEtats.length === 0) {
                        etatsCell.innerHTML = '<span class="badge bg-secondary">Aucun</span>';
                    } else {
                        selectedEtats.forEach(etatId => {
                            // On retrouve le texte via la liste complète chargée dans le select
                            var option = etatsSelect.querySelector('option[value="' + etatId + '"]');
                            if (option) {
                                etatsCell.innerHTML += '<span class="badge" style="background-color: ' +
                                    (option.dataset.color || '#6c757d') + '">' + option.text +
                                    '</span> ';
                            }
                        });
                    }
                    playerRow.cells[7].innerHTML = '<div style="overflow: auto; max-height: 5rem;">' +
                        commentTextarea.value + '</div>';

                    hideModal();
                })
                .catch(function(error) {
                    console.error('Error updating player:', error);
                });
        });

        function hideModal() {
            var modalElement = document.getElementById('editPlayerModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();

            // Reset UI
            document.getElementById('loadingSpinner').style.display = 'block';
            document.querySelector('.modal-content-placeholder').style.display = 'none';

            // Reset Fields
            document.getElementById('modal_nom').value = '';
            document.getElementById('modal_role').value = '';
            document.getElementById('modal_camp').value = '';
            document.getElementById('modal_isAlive').checked = false;
            document.getElementById('modal_comment').value = '';
            document.getElementById('modal_player_id').value = '';

            const vSwitch = document.getElementById('modal_village');
            if (vSwitch) vSwitch.checked = false;

            // Important : Ne pas vider le innerHTML, juste désélectionner les options
            var etatsSelect = document.getElementById('modal_etats');
            Array.from(etatsSelect.options).forEach(option => option.selected = false);
        }

        function editModal(playerId) {
            var modal = new bootstrap.Modal(document.getElementById('editPlayerModal'));
            modal.show();

            var playerIdInput = document.getElementById('modal_player_id');
            playerIdInput.value = playerId;

            Promise.all([rolesPromise, campsPromise, etatsPromise])
                .then(() => axios.post('{{ route('admin.players.info', ['id' => '__replace__']) }}'.replace('__replace__',
                    playerId)))
                .then(function(response) {
                    var player = response.data;

                    // Remplissage Champs simples (après avoir les options disponibles)
                    document.getElementById('modal_nom').value = player.nom;
                    document.getElementById('modal_role').value = player.role.id;
                    document.getElementById('modal_camp').value = player.camp_id || '';
                    document.getElementById('modal_isAlive').checked = player.is_alive;
                    document.getElementById('modal_comment').value = player.comment;
                    document.getElementById('player_page_link').href = '/players/' + player.token;
                    document.getElementById('player_page_link').innerText = '/players/' + player.token;

                    // Gestion Switch Village
                    const vSwitch = document.getElementById('modal_village');
                    if (vSwitch) {
                        const currentIdInput = document.getElementById('current_game_id');
                        const otherIdInput = document.getElementById('other_game_id');
                        if (currentIdInput && otherIdInput) {
                            // Coché si le joueur est dans l'autre jeu par rapport à la page courante
                            vSwitch.checked = (String(player.game_id) === String(otherIdInput.value));
                        } else {
                            // Fallback historique: comparer avec second_game_id si disponible
                            const secondIdInput = document.getElementById('liage_second_id');
                            if (secondIdInput) {
                                vSwitch.checked = (String(player.game_id) === String(secondIdInput.value));
                            }
                        }
                    }

                    // Gestion États : On sélectionne les bons items dans la liste existante
                    var etatsSelect = document.getElementById('modal_etats');

                    // 1. Tout désélectionner d'abord
                    Array.from(etatsSelect.options).forEach(option => option.selected = false);

                    // 2. Sélectionner ceux du joueur
                    if (player.etats && player.etats.length > 0) {
                        const playerEtatIds = player.etats.map(e => parseInt(e.id));
                        Array.from(etatsSelect.options).forEach(option => {
                            if (playerEtatIds.includes(parseInt(option.value))) {
                                option.selected = true;
                            }
                        });
                    }

                    // Affichage
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.querySelector('.modal-content-placeholder').style.display = 'block';
                })
                .catch(function(error) {
                    console.error('Error fetching player data:', error);
                });
        }

        @if (isset($openModal) && $openModal)
            window.onload = function() {
                // Attention: nécessite un ID valide si utilisé automatiquement
                // editModal(ID_JOUEUR);
            };
        @endif
    </script>

    @include('layouts.map', [
        'gameId' => $game->id,
    ])
@endsection
