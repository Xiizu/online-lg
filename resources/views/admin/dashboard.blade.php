@extends('layouts.app')
@section('title', 'Tableau de bord')
@php
    use App\Models\Role;
    use App\Models\Camp;
    use Illuminate\Support\Str;

    // Préparation des données pour les filtres (comme sur la page Rôles)
    $allRoles = Role::all();
    $allCamps = Camp::all();
    $uniqueAuras = $allRoles->pluck('aura')->unique()->filter()->values();
    $uniqueApparences = $allRoles->pluck('apparence')->unique()->filter()->values();

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

@section('body')
    <div class="container mt-5">
        <h1 class="mb-4">Tableau de bord administrateur</h1>
        <p>Bienvenue sur le tableau de bord administrateur. Ici, vous pouvez gérer les utilisateurs, les contenus et les
            paramètres du site.</p>

        <div class="row g-4 mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Créer une nouvelle Partie</h5>
                        <p class="card-text">Lancer une nouvelle partie de Loup-Garou</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#stepOneModal">
                            Lancer
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Liste des Parties</h5>
                        <p class="card-text">Afficher les parties existantes de Loup-Garou</p>
                        <a href="{{ route('admin.games.index') }}" class="btn btn-primary">Voir</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Paramètres</h5>
                        <p class="card-text">Afficher les paramètres du jeu</p>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-primary">Gérer</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Notes</h5>
                        <p class="card-text">Afficher les notes du jeu</p>
                        <a href="{{ route('admin.notes.index') }}" class="btn btn-primary">Gérer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Création Partie -->
    <div class="modal fade" tabindex="-1" id="stepOneModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Création de la partie - Sélection des rôles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Infos sur la partie --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="game-name" class="form-label">Nom de la partie</label>
                            <input type="text" class="form-control" id="game-name" placeholder="Ex: Soirée Loup-Garou #1"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="game-date" class="form-label">Date de la partie</label>
                            <input type="date" class="form-control" id="game-date" required>
                        </div>
                    </div>

                    <hr>

                    {{-- Interface de Filtres (Style Page Rôles) --}}
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="role-search"
                                placeholder="Rechercher un rôle par nom..." onkeyup="filterRoles()">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                                <i class="bi bi-funnel"></i> Filtres Avancés
                            </button>
                        </div>
                    </div>

                    {{-- Filtres Avancés (Collapsible) --}}
                    <div class="collapse mb-3" id="advancedFilters">
                        <div class="card card-body border-0">
                            <div class="row g-3">
                                {{-- Filtre Camps --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2 small">Camps</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($allCamps as $camp)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-camp" type="checkbox"
                                                    value="{{ strtolower($camp->name) }}" id="camp_{{ $camp->id }}"
                                                    onchange="filterRoles()">
                                                <label class="form-check-label small" for="camp_{{ $camp->id }}">
                                                    {{ $camp->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtre Auras --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2 small">Auras</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($uniqueAuras as $aura)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-aura" type="checkbox"
                                                    value="{{ strtolower($aura) }}" id="aura_{{ $loop->index }}"
                                                    onchange="filterRoles()">
                                                <label class="form-check-label small" for="aura_{{ $loop->index }}">
                                                    {{ $aura }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtre Apparences --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2 small">Apparences</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($uniqueApparences as $apparence)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-apparence"
                                                    type="checkbox" value="{{ strtolower($apparence) }}"
                                                    id="app_{{ $loop->index }}" onchange="filterRoles()">
                                                <label class="form-check-label small" for="app_{{ $loop->index }}">
                                                    {{ $apparence }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Liste des rôles --}}
                    <div class="overflow-auto border rounded p-2 " style="max-height: 50vh;">
                        <ul class="list-group list-group-flush" id="roles-list">
                            @foreach ($allRoles as $role)
                                @php
                                    $campNames = $role->camps->pluck('name')->join('/');
                                @endphp
                                <li class="list-group-item mb-1 rounded border-0 shadow-sm"
                                    data-camps="{{ strtolower($campNames) }}" data-aura="{{ strtolower($role->aura) }}"
                                    data-apparence="{{ strtolower($role->apparence) }}">

                                    <div class="d-flex align-items-center justify-content-between gap-2 py-1">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <strong class="role-name" style="font-size: 1.05rem;">
                                                    {{ $role->nom }}
                                                </strong>
                                                {{-- Badges indicatifs --}}
                                                <div>
                                                    @foreach ($role->camps as $camp)
                                                        <span class="badge"
                                                            style="background-color: {{ $camp->color }}; font-size: 0.7rem;">{{ $camp->name }}</span>
                                                        <span class="badge text-dark"
                                                            style="font-size: 0.7rem; background-color: {{ getColor($role->aura) }};">{{ $role->aura }}</span>
                                                        <span class="badge text-dark"
                                                            style="font-size: 0.7rem; background-color: {{ getColor($role->apparence) }};">{{ $role->apparence }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            {{-- Description et Pouvoirs supprimés ici --}}
                                        </div>

                                        {{-- Compteur --}}
                                        <div class="d-flex align-items-center gap-2 flex-shrink-0 rounded p-1">
                                            <button class="btn btn-sm btn-outline-danger border-0 fw-bold"
                                                style="width: 28px; height: 28px;"
                                                onclick="addOrRemoveOne({{ $role->id }}, 'decrement')">-</button>
                                            <span id="role-count-{{ $role->id }}" class="fw-bold text-center"
                                                style="width: 30px; font-size: 1.1rem;">0</span>
                                            <button class="btn btn-sm btn-outline-success border-0 fw-bold"
                                                style="width: 28px; height: 28px;"
                                                onclick="addOrRemoveOne({{ $role->id }}, 'increment')">+</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div name="alert-container" class="mt-3">
                        {{-- Les alertes seront insérées ici par JavaScript --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <span class="text-muted me-auto small">Nombre de rôles : <span id="role-count-total">0</span></span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary px-4" onclick="createGame()">
                        <i class="bi bi-play-fill"></i> Créer la partie
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($openModal ?? false)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var myModal = new bootstrap.Modal(document.getElementById('stepOneModal'));
                myModal.show();
            });
        </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initial filtrage
            filterRoles();
        });

        function filterRoles() {
            // 1. Récupérer la recherche (Nom)
            const searchInput = document.getElementById('role-search').value.toLowerCase();

            // 2. Récupérer les filtres cochés
            const checkedCamps = Array.from(document.querySelectorAll('.filter-camp:checked')).map(cb => cb.value
                .toLowerCase());
            const checkedAuras = Array.from(document.querySelectorAll('.filter-aura:checked')).map(cb => cb.value
                .toLowerCase());
            const checkedApparences = Array.from(document.querySelectorAll('.filter-apparence:checked')).map(cb => cb.value
                .toLowerCase());

            // 3. Appliquer le filtre sur chaque ligne
            document.querySelectorAll('#roles-list li.list-group-item').forEach(li => {
                // Récupération des données
                const roleName = li.querySelector('.role-name').textContent.toLowerCase();
                const roleCamps = li.getAttribute('data-camps') || '';
                const roleAura = li.getAttribute('data-aura') || '';
                const roleApparence = li.getAttribute('data-apparence') || '';

                // --- LOGIQUE DE FILTRAGE ---

                // A. Recherche textuelle
                const matchesSearch = searchInput === '' || roleName.includes(searchInput);

                // B. Filtres Checkbox (Si cochés, doit correspondre à au moins un)
                // Note: roleCamps contient "camp1/camp2", on cherche si l'un des checkedCamps est dedans
                const matchesCamp = checkedCamps.length === 0 || checkedCamps.some(c => roleCamps.includes(c));
                const matchesAura = checkedAuras.length === 0 || checkedAuras.includes(roleAura);
                const matchesApp = checkedApparences.length === 0 || checkedApparences.includes(roleApparence);

                if (matchesSearch && matchesCamp && matchesAura && matchesApp) {
                    li.style.display = '';
                } else {
                    li.style.display = 'none';
                }
            });
        }

        // Gestionnaire des compteurs
        function addOrRemoveOne(roleId, action) {
            const countSpan = document.getElementById(`role-count-${roleId}`);
            const totalCountSpan = document.getElementById('role-count-total');
            let totalCount = parseInt(totalCountSpan.textContent);
            let currentCount = parseInt(countSpan.textContent);

            if (action === 'increment') {
                currentCount++;
                totalCount++;
            } else if (action === 'decrement' && currentCount > 0) {
                currentCount--;
                totalCount--;
            }

            countSpan.textContent = currentCount;
            totalCountSpan.textContent = totalCount;

            // Feedback visuel optionnel sur la ligne quand sélectionné
            const li = countSpan.closest('li');
            if (currentCount > 0) {
                li.classList.add('border-primary', 'border');
                li.classList.remove('border-0');
            } else {
                li.classList.remove('border-primary', 'border');
                li.classList.add('border-0');
            }
        }

        // Création de la partie
        function createGame() {
            const modalEl = document.getElementById('stepOneModal');
            const close_button = modalEl.querySelector('.btn-secondary');
            const next_button = modalEl.querySelector('.btn-primary');

            // État de chargement
            close_button.disabled = true;
            next_button.disabled = true;
            const originalBtnText = next_button.innerHTML;
            next_button.innerHTML =
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Création...';

            const gameName = document.getElementById('game-name').value;
            const gameDate = document.getElementById('game-date').value;

            const showAlert = (message) => {
                const body = modalEl.querySelector('[name="alert-container"]');
                // Nettoyer anciennes alertes
                body.innerHTML = '';
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML =
                    `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
                body.prepend(alert);
            };

            if (!gameName || !gameDate) {
                showAlert("Veuillez remplir le nom et la date de la partie.");
                close_button.disabled = false;
                next_button.disabled = false;
                next_button.innerHTML = originalBtnText;
                return;
            }

            const rolesSelected = {};
            @foreach ($allRoles as $role)
                const count{{ $role->id }} = parseInt(document.getElementById('role-count-{{ $role->id }}')
                    .textContent);
                if (count{{ $role->id }} > 0) {
                    rolesSelected['{{ $role->id }}'] = count{{ $role->id }};
                }
            @endforeach

            const payload = {
                game_name: gameName,
                game_date: gameDate,
                roles: rolesSelected
            };

            axios.post("{{ route('admin.games.create') }}", payload)
                .then(response => {
                    if (response.status === 201) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        modal.hide();
                        sessionStorage.setItem('success', 'Partie créée avec succès.');
                        window.location.href = "{{ route('admin.games.index') }}";
                    } else {
                        throw new Error(response.data.error || 'Erreur inconnue');
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.message || error.message || 'Une erreur est survenue.';
                    showAlert(message);
                    close_button.disabled = false;
                    next_button.disabled = false;
                    next_button.innerHTML = originalBtnText;
                });
        }
    </script>
@endsection
