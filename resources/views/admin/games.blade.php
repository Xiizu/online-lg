@extends('layouts.app')
@section('title', 'Parties')

@section('body')
    <div class="container mt-4">
        <h1 class="mb-4">Gestion des Parties</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createLiageModal">
            <i class="bi bi-link-45deg"></i> Créer une liaison
        </button>

        <div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true"></div>

        <div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @php
                function status_color($status){
                    return match($status) {
                        'setup' => 'info',
                        'started' => 'success',
                        'ended' => 'danger',
                        default => 'warning',
                    };
                }
            @endphp
        </div>

        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="">
                    <tr>
                        <th>Nom</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $singleGames = $games->filter(fn($game) => $game->isSingleGame());
                        $linkedGroups = $games->filter(fn($game) => !$game->isSingleGame())->groupBy('liage_id');
                    @endphp
                    @foreach($singleGames as $game)
                        <tr>
                            <td>{{ $game->name }}</td>
                            <td>{{ $game->date }}</td>
                            <td>
                                <span class="badge bg-{{ status_color($game->status) }}">{{ $game->status }}</span>
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm text-white" href="{{ route('admin.games.view', ['id' => $game->id]) }}">
                                    <i class="bi bi-eye"></i> Voir
                                </a>
                                @if ($game->status == 'setup')
                                    <button class="btn btn-success btn-sm" id="{{ $game->id }}">Lancer</button>
                                @elseif ($game->status == 'started')
                                    <button class="btn btn-warning btn-sm" id="{{ $game->id }}">Terminer</button>
                                @elseif ($game->status == 'ended')
                                    <button class="btn btn-danger btn-sm" id="{{ $game->id }}">Supprimer</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    {{-- B. Affichage des groupes de parties liées --}}
                    @foreach($linkedGroups as $liageId => $group)
                        @foreach($group as $game)
                            {{-- Logique visuelle : Bordure haut sur le 1er, bas sur le dernier, fond coloré --}}
                            <tr class="table-info {{ $loop->index % 2 == 0 ? 'border-top border-3 border-info border-bottom-0' : '' }} {{ $loop->index % 2 == 1 ? 'border-bottom border-3 border-info border-top-0' : 'border-bottom-0' }}">
                                <td>
                                    @if($loop->index % 2 == 0)
                                        <i class="bi bi-link-45deg text-info me-1" title="Partie Liée"></i>
                                    @else
                                        <span class="ms-3 text-muted">↳</span>
                                    @endif
                                    {{ $game->name }}
                                </td>
                                <td>{{ $game->date }}</td>
                                <td>
                                    <span class="badge bg-{{ status_color($game->status) }}">{{ $game->status }}</span>
                                </td>
                                <td>
                                    <a class="btn btn-info btn-sm text-white" href="{{ route('admin.games.view', ['id' => $game->id]) }}">
                                        <i class="bi bi-eye"></i> Voir
                                    </a>
                                    @if ($game->status == 'setup')
                                        <button class="btn btn-success btn-sm" id="{{ $game->id }}">Lancer</button>
                                    @elseif ($game->status == 'started')
                                        <button class="btn btn-warning btn-sm" id="{{ $game->id }}">Terminer</button>
                                    @elseif ($game->status == 'ended')
                                        <button class="btn btn-danger btn-sm" id="{{ $game->id }}">Supprimer</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CRÉATION LIAISON --}}
    <div class="modal fade" id="createLiageModal" tabindex="-1" aria-labelledby="createLiageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLiageModalLabel">Créer une Liaison de Parties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('admin.liages.create') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info small">
                            Lier deux parties permet de gérer des interactions entre deux villages distincts.<br><strong>CETTE ACTION EST IRREVERSIBLE</strong>
                        </div>
                        <div class="mb-3">
                            <label for="liageName" class="form-label">Nom de la Liaison / Univers</label>
                            <input type="text" class="form-control" id="liageName" name="name" placeholder="Ex: Guerre des Deux Tours" required>
                        </div>
                        <div class="mb-3">
                            <label for="firstGame" class="form-label">Village Principal (Partie 1)</label>
                            <select class="form-select" id="firstGame" name="first_game_id" required>
                                @foreach($games as $game)
                                    <option value="{{ $game->id }}">{{ $game->name }} ({{ $game->date }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="secondGame" class="form-label">Village Voisin (Partie 2)</label>
                            <select class="form-select" id="secondGame" name="second_game_id" required>
                                @foreach($games as $game)
                                    <option value="{{ $game->id }}">{{ $game->name }} ({{ $game->date }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Créer la Liaison</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL CONFIRMATION --}}
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmer l'action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationModalBody">
                    <p name="confirmationMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmActionButton">Confirmer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modalEl = document.getElementById('confirmationModal');
            const modalBody = document.getElementById('confirmationModalBody');
            const confirmBtn = document.getElementById('confirmActionButton');
            const bsModal = new bootstrap.Modal(modalEl);

            let pendingAction = null; // { type: 'start' | 'end', gameId: number }

            document.querySelectorAll('button.btn-success.btn-sm[id]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault(); // Empêche comportement par défaut si submit
                    const gameId = parseInt(btn.id, 10);
                    pendingAction = { type: 'start', gameId };
                    modalBody.querySelector('p[name="confirmationMessage"]').textContent = 'Voulez-vous lancer cette partie ?';
                    bsModal.show();
                });
            });

            document.querySelectorAll('button.btn-warning.btn-sm[id]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const gameId = parseInt(btn.id, 10);
                    pendingAction = { type: 'end', gameId };
                    modalBody.querySelector('p[name="confirmationMessage"]').textContent = 'Voulez-vous terminer cette partie ?';
                    bsModal.show();
                });
            });

            document.querySelectorAll('button.btn-danger.btn-sm[id]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const gameId = parseInt(btn.id, 10);
                    pendingAction = { type: 'delete', gameId };
                    modalBody.querySelector('p[name="confirmationMessage"]').textContent = 'Voulez-vous supprimer cette partie ? Cette action est irréversible.';
                    bsModal.show();
                });
            });

            confirmBtn.addEventListener('click', () => {
                if (!pendingAction) return;
                const { type, gameId } = pendingAction;
                bsModal.hide();
                pendingAction = null;

                if (type === 'start') {
                    startgame(gameId);
                } else if (type === 'end') {
                    endgame(gameId);
                } else if (type === 'delete') {
                    deletegame(gameId);
                }
            });

            // Clear pending action when modal is dismissed
            modalEl.addEventListener('hidden.bs.modal', () => {
                pendingAction = null;
            });
        });

        function startgame(gameId) {
            const gameBtn = document.getElementById(gameId);
            // Feedback visuel immédiat
            if(gameBtn) {
                gameBtn.disabled = true;
                gameBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            // Redirection (Note: Le remplacement d'URL JS est plus propre que l'injection Blade directe dans JS)
            const url = "{{ route('admin.games.start', ['id' => ':id']) }}".replace(':id', gameId);
            window.location.href = url;
        }

        function endgame(gameId) {
            const url = "{{ route('admin.games.end', ['id' => ':id']) }}".replace(':id', gameId);

            axios.post(url)
                .then(function (response) {
                    showToast(response.data.success || 'Partie terminée avec succès', 'success');

                    const gameBtn = document.getElementById(gameId);
                    if (gameBtn) {
                        gameBtn.classList.remove('btn-danger');
                        gameBtn.classList.add('btn-warning');
                        gameBtn.innerText = 'Terminée';
                    }
                })
                .catch(function (error) {
                    const msg = error.response?.data?.error || 'Une erreur est survenue';
                    showToast(msg, 'danger');
                });
        }

        function deletegame(gameId) {
            const url = "{{ route('admin.games.delete', ['id' => ':id']) }}".replace(':id', gameId);

            axios.post(url)
                .then(function (response) {
                    showToast(response.data.success || 'Partie supprimée avec succès', 'success');

                    // Retirer la ligne du tableau
                    const gameBtn = document.getElementById(gameId);
                    if (gameBtn) {
                        const row = gameBtn.closest('tr');
                        if (row) row.remove();
                    }
                })
                .catch(function (error) {
                    const msg = error.response?.data?.error || 'Une erreur est survenue';
                    showToast(msg, 'danger');
                });
        }

        function showToast(message, type = 'success') {
            const toastEl = document.querySelector('.toast');
            if (!toastEl) return;

            // Construction du Toast
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>`;

            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>
@endsection
