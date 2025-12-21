@extends('layouts.app')
@section('title', 'Paramètres - Général')
@php
    use App\Models\Etat;
    use App\Models\Camp;
@endphp
@section('body')
    <div class="container mt-4">
        <h1 class="mb-4">Paramètres du Jeu</h1>
        <p>Gérez les paramètres globaux du jeu ici.</p>

        {{-- Conteneur Toast (Positionné en bas à droite) --}}
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div id="toast-body" class="toast-body">
                        <!-- Le message sera injecté ici -->
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- Section ETATS --}}
        <div class="card mb-4">
            {{-- En-tête cliquable pour le collapse --}}
            <div class="card-header bg-primary d-flex justify-content-between align-items-center"
                 data-bs-toggle="collapse"
                 data-bs-target="#collapseEtats"
                 aria-expanded="true"
                 aria-controls="collapseEtats"
                 style="cursor: pointer;">
                <span><i class="bi bi-tag-fill me-2"></i> Gestion des États</span>
                <i class="bi bi-chevron-down"></i>
            </div>

            {{-- Zone repliable --}}
            <div id="collapseEtats" class="collapse show">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="">
                                <tr>
                                    <th style="width: 30%">Label</th>
                                    <th style="width: 50%">Description</th>
                                    <th style="width: 20%">Couleur</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-etats">
                                {{-- Ligne de Création --}}
                                <tr class="">
                                    <td>
                                        <input type="text" id="new_label" class="form-control" placeholder="Nouveau label (ex: Paralysé)">
                                    </td>
                                    <td>
                                        <input type="text" id="new_description" class="form-control" placeholder="Description de l'effet">
                                    </td>
                                    <td>
                                        <input type="color" id="new_color" class="form-control form-control-color" value="#563d7c" title="Choisissez une couleur">
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-sm w-100" onclick="createEtat()">
                                            <i class="bi bi-plus-circle"></i> Ajouter
                                        </button>
                                    </td>
                                </tr>

                                {{-- Liste des États existants --}}
                                @foreach (Etat::all() as $etat)
                                    <tr id="row-etat-{{ $etat->id }}">
                                        <td>
                                            <input type="text" class="form-control" id="label-{{ $etat->id }}" value="{{ $etat->label }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" id="description-{{ $etat->id }}" value="{{ $etat->description }}">
                                        </td>
                                        <td>
                                            <input type="color" class="form-control form-control-color" id="color-{{ $etat->id }}" value="{{ $etat->color ?? '#000000' }}" title="Couleur de l'état">
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm flex-grow-1" onclick="updateEtat({{ $etat->id }})">
                                                    Modifier
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteEtat({{ $etat->id }})">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section CAMPS --}}
        <div class="card mb-4">
            {{-- En-tête cliquable pour le collapse --}}
            <div class="card-header bg-primary d-flex justify-content-between align-items-center"
                 data-bs-toggle="collapse"
                 data-bs-target="#collapseCamps"
                 aria-expanded="false"
                 aria-controls="collapseCamps"
                 style="cursor: pointer;">
                <span><i class="bi bi-shield-fill-exclamation me-2"></i> Gestion des Camps</span>
                <i class="bi bi-chevron-down"></i>
            </div>

            {{-- Zone repliable --}}
            <div id="collapseCamps" class="collapse">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="">
                                <tr>
                                    <th style="width: 40%">Nom</th>
                                    <th style="width: 40%">Couleur</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-camps">
                                {{-- Ligne de Création Camp --}}
                                <tr class="">
                                    <td>
                                        <input type="text" id="new_camp_name" class="form-control" placeholder="Nouveau camp (ex: Village)">
                                    </td>
                                    <td>
                                        <input type="color" id="new_camp_color" class="form-control form-control-color" value="#0d6efd" title="Choisissez une couleur">
                                    </td>
                                    <td>
                                        <button class="btn btn-success btn-sm w-100" onclick="createCamp()">
                                            <i class="bi bi-plus-circle"></i> Ajouter
                                        </button>
                                    </td>
                                </tr>

                                {{-- Liste des Camps existants --}}
                                @foreach (Camp::all() as $camp)
                                    <tr id="row-camp-{{ $camp->id }}">
                                        <td>
                                            <input type="text" class="form-control" id="camp-name-{{ $camp->id }}" value="{{ $camp->name }}">
                                        </td>
                                        <td>
                                            <input type="color" class="form-control form-control-color" id="camp-color-{{ $camp->id }}" value="{{ $camp->color ?? '#000000' }}" title="Couleur du camp">
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm flex-grow-1" onclick="updateCamp({{ $camp->id }})">
                                                    Modifier
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteCamp({{ $camp->id }})">
                                                    Supprimer
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section ROLES --}}
        <div class="card mb-4">
            {{-- En-tête cliquable pour le collapse --}}
            <div class="card-header bg-primary d-flex justify-content-between align-items-center"
                 data-bs-toggle="collapse"
                 data-bs-target="#collapseRoles"
                 aria-expanded="false"
                 aria-controls="collapseRoles"
                 style="cursor: pointer;">
                <span><i class="bi bi-person-badge-fill me-2"></i> Gestion des Rôles</span>
                <i class="bi bi-chevron-down"></i>
            </div>

            {{-- Zone repliable --}}
            <div id="collapseRoles" class="collapse">
                <div class="card-body">
                    <p>La gestion des rôles est disponible dans une section dédiée. <a href="{{ route('admin.settings.roles.index') }}">Accéder à la gestion des rôles</a>.</p>
                </div>
            </div>

    </div>

    <script>
        // --- Gestion des Toasts ---
        function showToast(message, isSuccess = true) {
            const toastEl = document.getElementById('liveToast');
            const toastBody = document.getElementById('toast-body');

            // Réinitialiser les classes de couleur
            toastEl.classList.remove('text-bg-success', 'text-bg-danger');

            // Appliquer la nouvelle couleur
            if (isSuccess) {
                toastEl.classList.add('text-bg-success');
            } else {
                toastEl.classList.add('text-bg-danger');
            }

            // Mettre à jour le texte
            toastBody.textContent = message;

            // Afficher le toast via Bootstrap
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }

        // --- GESTION ETATS ---

        function createEtat() {
            const label = document.getElementById('new_label').value;
            const description = document.getElementById('new_description').value;
            const color = document.getElementById('new_color').value;

            if (!label) {
                showToast('Le label est obligatoire.', false);
                return;
            }

            axios.post('{{ route("admin.settings.etat.create") }}', {
                label: label,
                description: description,
                color: color
            })
            .then(response => {
                // Si le serveur retourne l'objet créé, on l'ajoute dynamiquement
                const newEtat = response.data;

                // Vérification si la réponse contient bien l'objet (et pas juste un message)
                if (newEtat && newEtat.id) {
                    const tbody = document.getElementById('tbody-etats');
                    const tr = document.createElement('tr');
                    tr.id = `row-etat-${newEtat.id}`;
                    tr.innerHTML = `
                        <td>
                            <input type="text" class="form-control" id="label-${newEtat.id}" value="${newEtat.label}">
                        </td>
                        <td>
                            <input type="text" class="form-control" id="description-${newEtat.id}" value="${newEtat.description || ''}">
                        </td>
                        <td>
                            <input type="color" class="form-control form-control-color" id="color-${newEtat.id}" value="${newEtat.color || '#000000'}" title="Couleur de l'état">
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-grow-1" onclick="updateEtat(${newEtat.id})">
                                    Modifier
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteEtat(${newEtat.id})">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    // Reset des champs
                    document.getElementById('new_label').value = '';
                    document.getElementById('new_description').value = '';
                    showToast('État ajouté avec succès !', true);
                } else {
                    // Fallback si le contrôleur ne renvoie pas l'objet
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur création état : " + (error.response?.data?.message || "Erreur serveur"), false);
            });
        }

        function updateEtat(id) {
            const label = document.getElementById(`label-${id}`).value;
            const description = document.getElementById(`description-${id}`).value;
            const color = document.getElementById(`color-${id}`).value;
            const url = '{{ route("admin.settings.etat.update", ["id" => "__replace__"]) }}'.replace('__replace__', id);

            axios.post(url, {
                label: label,
                description: description,
                color: color
            })
            .then(response => {
                showToast('État modifié avec succès !', true);
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur modification état.", false);
            });
        }

        function deleteEtat(id) {
            if (!confirm("Voulez-vous vraiment supprimer cet état ? Cela le retirera de tous les joueurs.")) {
                return;
            }

            const url = '{{ route("admin.settings.etat.delete", ["id" => "__replace__"]) }}'.replace('__replace__', id);

            axios.post(url)
            .then(response => {
                const row = document.getElementById(`row-etat-${id}`);
                if(row) row.remove();
                showToast('État supprimé avec succès.', true);
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur suppression état.", false);
            });
        }

        // --- GESTION CAMPS ---

        function createCamp() {
            const name = document.getElementById('new_camp_name').value;
            const color = document.getElementById('new_camp_color').value;

            if (!name) {
                showToast('Le nom du camp est obligatoire.', false);
                return;
            }

            axios.post('{{ route("admin.settings.camp.create") }}', {
                name: name,
                color: color
            })
            .then(response => {
                const newCamp = response.data;

                if (newCamp && newCamp.id) {
                    const tbody = document.getElementById('tbody-camps');
                    const tr = document.createElement('tr');
                    tr.id = `row-camp-${newCamp.id}`;
                    tr.innerHTML = `
                        <td>
                            <input type="text" class="form-control" id="camp-name-${newCamp.id}" value="${newCamp.name}">
                        </td>
                        <td>
                            <input type="color" class="form-control form-control-color" id="camp-color-${newCamp.id}" value="${newCamp.color}" title="Couleur du camp">
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-grow-1" onclick="updateCamp(${newCamp.id})">
                                    Modifier
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCamp(${newCamp.id})">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(tr);

                    // Reset des champs
                    document.getElementById('new_camp_name').value = '';
                    document.getElementById('new_camp_color').value = '#0d6efd';
                    showToast('Camp ajouté avec succès !', true);
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur création camp : " + (error.response?.data?.message || "Erreur serveur"), false);
            });
        }

        function updateCamp(id) {
            const name = document.getElementById(`camp-name-${id}`).value;
            const color = document.getElementById(`camp-color-${id}`).value;
            const url = '{{ route("admin.settings.camp.update", ["id" => "__replace__"]) }}'.replace('__replace__', id);

            axios.post(url, {
                name: name,
                color: color
            })
            .then(response => {
                showToast('Camp modifié avec succès !', true);
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur modification camp.", false);
            });
        }

        function deleteCamp(id) {
            if (!confirm("Voulez-vous vraiment supprimer ce camp ?")) {
                return;
            }

            const url = '{{ route("admin.settings.camp.delete", ["id" => "__replace__"]) }}'.replace('__replace__', id);

            axios.post(url)
            .then(response => {
                const row = document.getElementById(`row-camp-${id}`);
                if(row) row.remove();
                showToast('Camp supprimé avec succès.', true);
            })
            .catch(error => {
                console.error(error);
                showToast("Erreur suppression camp.", false);
            });
        }
    </script>
@endsection
