@extends('layouts.app')
@section('title', 'Notes & Scénario')

@section('body')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-journal-text me-2"></i>Notes de Partie</h1>
            <div id="saveStatus" class="text-muted small fst-italic opacity-0 transition-opacity">
                <i class="bi bi-check-circle me-1"></i>Modifications enregistrées
            </div>
        </div>

        <div class="row g-4">
            {{-- Colonne de gauche : Sélection --}}
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header fw-bold">
                        Sélectionner une partie
                    </div>
                    <div class="card-body">
                        <label for="gameSelect" class="form-label text-muted small text-uppercase">Partie en cours</label>
                        <select class="form-select mb-3" id="gameSelect" aria-label="Sélectionner une partie">
                            <option value="" selected disabled>-- Choisir une partie --</option>
                            @foreach ($games as $game)
                                <option value="{{ $game->id }}">
                                    {{ $game->name }} ({{ $game->date }})
                                </option>
                            @endforeach
                        </select>
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Sélectionnez une partie pour charger et éditer ses notes.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Colonne de droite : Éditeur --}}
            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header d-flex justify-content-between align-items-center py-3">
                        <span class="fw-bold text-primary" id="editorTitle">Aucune partie sélectionnée</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-secondary disabled" title="Aperçu (Bientôt disponible)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary disabled" title="Aide Markdown">
                                <i class="bi bi-markdown"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="position-relative h-100">
                            {{-- Overlay de chargement / attente --}}
                            <div id="editorOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center z-2" style="opacity: 0.8;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-arrow-left-circle display-4 mb-3 d-block"></i>
                                    Veuillez sélectionner une partie à gauche<br>pour commencer à écrire.
                                </div>
                            </div>

                            {{-- Zone de texte --}}
                            <textarea
                                class="form-control border-0 rounded-0 p-4 h-100 font-monospace"
                                id="notesEditor"
                                style="min-height: 500px; resize: none; font-size: 0.95rem; line-height: 1.6;"
                                placeholder="# Titre du document&#10;&#10;Commencez à écrire vos notes ici en Markdown..."
                                disabled
                            ></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-muted small d-flex justify-content-between">
                        <span>Format: <strong>Markdown</strong></span>
                        <span id="lastSavedTime">Dernière sauvegarde : Jamais</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gameSelect = document.getElementById('gameSelect');
            const editor = document.getElementById('notesEditor');
            const editorOverlay = document.getElementById('editorOverlay');
            const editorTitle = document.getElementById('editorTitle');
            const saveStatus = document.getElementById('saveStatus');
            const lastSavedTime = document.getElementById('lastSavedTime');

            let timeoutId = null;
            let currentGameId = null;

            // --- 1. Changement de partie ---
            gameSelect.addEventListener('change', function() {
                const gameId = this.value;
                const gameName = this.options[this.selectedIndex].text;

                if (!gameId) return;

                // UI Update
                editorOverlay.classList.remove('d-flex');
                editorOverlay.style.display = 'none';
                editor.disabled = false;
                editorTitle.textContent = "Notes : " + gameName;
                editor.value = "Chargement...";
                editor.disabled = true; // Désactiver pendant le chargement
                currentGameId = gameId;

                // Chargement des notes via AJAX
                // Route attendue: GET /admin/games/{id}/notes -> retourne JSON { notes: "..." }
                axios.post(`{{ route('admin.games.notes.get', ['id' => ':id']) }}`.replace(':id', gameId))
                    .then(response => {
                        editor.value = response.data.notes || ''; // Si null, chaîne vide
                        editor.disabled = false;
                        editor.focus();
                        updateLastSavedTime();
                    })
                    .catch(error => {
                        console.error("Erreur chargement notes", error);
                        editor.value = "Erreur lors du chargement des notes.";
                        showStatus("Erreur de chargement", "danger");
                    });
            });

            // --- 2. Auto-Save lors de la frappe ---
            editor.addEventListener('input', function() {
                if (!currentGameId) return;

                showStatus("Sauvegarde en cours...", "warning", true);

                // Debounce : on attend 1.5s après la dernière frappe avant d'envoyer
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    saveNotes(currentGameId, this.value);
                }, 1500);
            });

            function saveNotes(id, content) {
                // Route attendue: POST /admin/games/{id}/notes -> Payload { notes: "..." }
                axios.post(`{{ route('admin.games.notes.save', ['id' => ':id']) }}`.replace(':id', id), {
                    notes: content
                })
                .then(response => {
                    showStatus("Modifications enregistrées", "success");
                    updateLastSavedTime();
                })
                .catch(error => {
                    console.error("Erreur sauvegarde", error);
                    showStatus("Échec de la sauvegarde !", "danger");
                });
            }

            // --- Utilitaires ---
            function showStatus(message, type = "success", persistent = false) {
                saveStatus.innerHTML = `<i class="bi bi-${type === 'warning' ? 'hourglass-split' : (type === 'danger' ? 'exclamation-triangle' : 'check-circle')} me-1"></i>${message}`;
                saveStatus.className = `small fst-italic transition-opacity text-${type}`;
                saveStatus.style.opacity = '1';

                if (!persistent) {
                    setTimeout(() => {
                        saveStatus.style.opacity = '0';
                    }, 3000);
                }
            }

            function updateLastSavedTime() {
                const now = new Date();
                lastSavedTime.textContent = "Dernière sauvegarde : " + now.toLocaleTimeString();
            }
        });
    </script>

    <style>
        .transition-opacity {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
@endsection
