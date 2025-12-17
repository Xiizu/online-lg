@extends('layouts.app')
@section('title', 'Paramètres - Rôles')
@section('body')
    @php
        use App\Models\Role;
        use App\Models\Camp;
        use App\Models\Etat;

        // Récupération des données pour les filtres
        $allRoles = Role::all();
        $allCamps = Camp::all();
        $allEtats = Etat::all();
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
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Gestion des Rôles</h1>
                <p class="text-muted">Cette section permet de gérer les rôles disponibles dans le jeu.</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Bouton Liste des Effets --}}
                <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEffects"
                    aria-controls="offcanvasEffects">
                    Afficher la liste des effets
                </button>
                @if ($admin)
                    {{-- Bouton Création --}}
                    <button class="btn btn-success" onclick="openCreateModal()">
                        <i class="bi bi-plus-lg"></i> Créer un Nouveau Rôle
                    </button>
                @endif
            </div>
        </div>

        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasEffects" aria-labelledby="offcanvasEffectsLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasEffectsLabel">Liste des Effets</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        @foreach ($allEtats as $etat)
            <span class="badge rounded-pill text-dark border border-light shadow-sm mb-1"
                style="background-color: {{ $etat->color ?? '#f8f9fa' }}; cursor: pointer;"
                data-bs-toggle="modal"
                data-bs-target="#etatModal{{ $etat->id }}"
                title="Voir la description">
                {{ $etat->label }}
            </span>
        @endforeach
    </div>
</div>

<!-- 2. LES MODALES (Placées EN DEHORS de l'offcanvas, idéalement en bas de page) -->
@foreach ($allEtats as $etat)
    <div class="modal fade text-dark" id="etatModal{{ $etat->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: {{ $etat->color ?? '#000' }}">
                        {{ $etat->label }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2 text-secondary">
                    {{ $etat->description }}
                </div>
            </div>
        </div>
    </div>
@endforeach

        {{-- Toast Notification --}}
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="liveToast" class="toast align-items-center border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div id="toast-body" class="toast-body"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        {{-- Section Recherche et Filtres --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    {{-- Barre de recherche --}}
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                    class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" id="searchInput"
                                placeholder="Rechercher par nom uniquement..." onkeyup="filterTable()">
                            <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advancedFilters" aria-expanded="false" aria-controls="advancedFilters">
                                <i class="bi bi-funnel"></i> Filtres
                            </button>
                        </div>
                    </div>

                    {{-- Filtres Avancés (Collapsible) --}}
                    <div class="collapse col-12" id="advancedFilters">
                        <div class="card card-body bg-light border-0">
                            <div class="row g-4">
                                {{-- Filtre Camps --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2">Camps</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($allCamps as $camp)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-camp" type="checkbox"
                                                    value="{{ strtolower($camp->name) }}" id="camp_{{ $camp->id }}"
                                                    onchange="filterTable()">
                                                <label class="form-check-label" for="camp_{{ $camp->id }}">
                                                    {{ $camp->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtre Auras --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2">Auras</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($uniqueAuras as $aura)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-aura" type="checkbox"
                                                    value="{{ strtolower($aura) }}" id="aura_{{ $loop->index }}"
                                                    onchange="filterTable()">
                                                <label class="form-check-label" for="aura_{{ $loop->index }}">
                                                    {{ $aura }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Filtre Apparences --}}
                                <div class="col-md-4">
                                    <h6 class="fw-bold text-muted mb-2">Apparences</h6>
                                    <div class="d-flex flex-column gap-1" style="max-height: 150px; overflow-y: auto;">
                                        @foreach ($uniqueApparences as $apparence)
                                            <div class="form-check">
                                                <input class="form-check-input filter-checkbox filter-apparence"
                                                    type="checkbox" value="{{ strtolower($apparence) }}"
                                                    id="app_{{ $loop->index }}" onchange="filterTable()">
                                                <label class="form-check-label" for="app_{{ $loop->index }}">
                                                    {{ $apparence }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des Rôles --}}
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="rolesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Camps</th>
                                <th>Nom du Rôle</th>
                                <th>Aura</th>
                                <th>Apparence</th>
                                <th>Pouvoir</th>
                                <th>Description</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($allRoles as $role)
                                <tr style="cursor: pointer;" onclick="openEditModal({{ $role->id }})">
                                    <td>
                                        <div
                                            style="display: flex; flex-direction: column; align-items: flex-start; gap: 0.15rem;">
                                            @foreach ($role->camps as $camp)
                                                <span class="badge"
                                                    style="background-color: {{ $camp->color }}; text-align: center;">
                                                    {{ $camp->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="fw-bold">{{ $role->nom }}</td>
                                    <td><span class="badge" style="background-color: {{ getColor($role->aura) }};">{{ $role->aura }}</span></td>
                                    <td><span class="badge" style="background-color: {{ getColor($role->apparence) }};">{{ $role->apparence }}</span></td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;"
                                            title="{{ $role->pouvoir }}">
                                            {{ $role->pouvoir }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;"
                                            title="{{ $role->description }}">
                                            {{ $role->description }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($role->image_path)
                                            <img src="{{ asset('storage/' . $role->image_path) }}" alt="Img"
                                                style="height: 30px; width: auto;" class="rounded">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL UNIQUE (Sert pour Création ET Édition) --}}
    <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">
                        @if ($admin)
                            Nouveau Rôle
                        @else
                            Rôle
                        @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body centered align-items-center">
                    <div class="spinner-border text-primary d-none" id="modalLoading">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div id="modalContent">
                        <form id="roleForm" enctype="multipart/form-data">
                            <input type="hidden" id="role_id"> <!-- ID vide = Création, ID rempli = Édition -->

                            <div class="row g-3">
                                <div class="row g-3">
                                    <!-- COLONNE GAUCHE : IMAGE -->
                                    <div class="col-md-4 d-flex flex-column align-items-center">
                                        <label class="form-label fw-bold text-muted mb-2">Visuel</label>

                                        <!-- Image Preview (Toujours visible pour éviter le bug JS) -->
                                        <div class="mb-2 bg-light rounded d-flex align-items-center justify-content-center"
                                            style="min-height: 200px; width: 100%; border: 1px dashed #ccc;">
                                            <img src="" alt="Aucune image" id="roleImageDisplay"
                                                class="img-fluid rounded shadow-sm"
                                                style="max-height: 200px; max-width: 100%;">
                                        </div>

                                        @if ($admin)
                                            <div class="w-100">
                                                <label for="image_path" class="form-label small text-muted">Modifier
                                                    l'image</label>
                                                <input type="file" accept="image/*"
                                                    class="form-control form-control-sm" id="image_path">
                                            </div>
                                        @else
                                            <input type="hidden" id="image_path">
                                        @endif
                                    </div>

                                    <!-- COLONNE DROITE : INFOS -->
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label fw-bold text-uppercase ls-1">Nom du
                                                Rôle</label>
                                            <input type="text" class="form-control form-control-lg fw-bold"
                                                id="nom" required @if (!$admin) disabled @endif
                                                placeholder="Nom du rôle...">
                                        </div>

                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="aura" class="form-label text-muted">Aura</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-stars"></i></span>
                                                    <input type="text" class="form-control" id="aura"
                                                        @if (!$admin) disabled @endif>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="apparence" class="form-label text-muted">Apparence</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="bi bi-person-badge"></i></span>
                                                    <input type="text" class="form-control" id="apparence"
                                                        @if (!$admin) disabled @endif>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="pouvoir" class="form-label fw-bold text-warning"><i
                                                    class="bi bi-lightning-fill"></i> Pouvoir</label>
                                            <textarea class="form-control" id="pouvoir" rows="2" @if (!$admin) disabled @endif></textarea>
                                        </div>
                                    </div>

                                    <!-- BAS DE PAGE : DESCRIPTION -->
                                    <div class="col-12">
                                        <label for="description" class="form-label fw-bold text-muted"><i
                                                class="bi bi-card-text"></i> Description complète</label>
                                        <textarea class="form-control" id="description" rows="5" @if (!$admin) disabled @endif></textarea>
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
                @if ($admin)
                    <div class="modal-footer justify-content-between">
                        {{-- Bouton Supprimer (caché en mode création) --}}
                        <button type="button" class="btn btn-danger" id="btnDelete" style="display: none;"
                            onclick="deleteRole()">
                            Supprimer
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" onclick="saveRole()">Enregistrer</button>
                        </div>
                    </div>
                @else
                    <input type="hidden" id="btnDelete">
                @endif
            </div>
        </div>
    </div>

    <script>
        let modalInstance = null;

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('roleModal');
            if (el) {
                modalInstance = new bootstrap.Modal(el);
            }
        });

        // --- Ouverture Modal CRÉATION ---
        function openCreateModal() {
            document.getElementById('roleForm').reset();
            document.getElementById('role_id').value = '';
            document.getElementById('roleModalLabel').innerText =
                '@if ($admin) Créer un Nouveau Rôle @else Rôle @endif';
            document.getElementById('btnDelete').style.display = 'none';
            document.getElementById('modalLoading').classList.add('d-none');
            document.getElementById('modalContent').classList.remove('d-none');
            modalInstance.show();
        }

        // --- Ouverture Modal ÉDITION ---
        function openEditModal(id) {
            const modalLoading = document.getElementById('modalLoading');
            const modalContent = document.getElementById('modalContent');

            // Reset UI
            document.getElementById('roleForm').reset();
            document.getElementById('roleModalLabel').innerText =
                '@if ($admin) Éditer le Rôle @else Rôle @endif';
            document.getElementById('btnDelete').style.display = 'block';
            modalLoading.classList.remove('d-none');
            modalContent.classList.add('d-none');

            modalInstance.show();

            // Récupération des données via AJAX
            axios.post(`{{ route('admin.settings.roles.info', ['id' => '__replace_id__']) }}`.replace('__replace_id__',
                    id))
                .then(res => {
                    const r = res.data;
                    document.getElementById('role_id').value = r.id;
                    document.getElementById('nom').value = r.nom;
                    document.getElementById('aura').value = r.aura;
                    document.getElementById('apparence').value = r.apparence;
                    document.getElementById('pouvoir').value = r.pouvoir;
                    document.getElementById('description').value = r.description;
                    if (r.image_path) {
                        const imgDisplay = document.getElementById('roleImageDisplay');
                        imgDisplay.src = `{{ asset('storage/') }}/${r.image_path}`;
                        imgDisplay.style.display = 'block';
                    } else {
                        document.getElementById('roleImageDisplay').style.display = 'none';
                    }

                    modalLoading.classList.add('d-none');
                    modalContent.classList.remove('d-none');
                })
                .catch(err => {
                    console.error(err);
                    modalInstance.hide();
                    showToast('Erreur lors du chargement des données.', false);
                });
        }

        // --- Sauvegarde (Création ou Update) ---
        function saveRole() {
            const id = document.getElementById('role_id').value;
            const formData = new FormData();

            // Ajout des champs textes
            formData.append('nom', document.getElementById('nom').value);
            formData.append('aura', document.getElementById('aura').value);
            formData.append('apparence', document.getElementById('apparence').value);
            formData.append('pouvoir', document.getElementById('pouvoir').value);
            formData.append('description', document.getElementById('description').value);

            // Ajout du fichier image s'il y en a un
            const imageInput = document.getElementById('image_path');
            if (imageInput.files[0]) {
                formData.append('image', imageInput.files[0]);
            }

            let url;
            if (id) {
                // Mode Édition
                url = '{{ route('admin.settings.roles.update', ['id' => '__replace_id__']) }}'.replace('__replace_id__',
                    id);
            } else {
                // Mode Création
                url = '{{ route('admin.settings.roles.create') }}';
            }

            axios.post(url, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(() => {
                    modalInstance.hide();
                    showToast('Rôle enregistré avec succès !', true);
                    setTimeout(() => window.location.reload(), 500);
                })
                .catch(err => {
                    console.error(err);
                    const msg = err.response?.data?.message || "Erreur lors de l'enregistrement.";
                    showToast(msg, false);
                });
        }

        // --- Suppression ---
        function deleteRole() {
            const id = document.getElementById('role_id').value;
            if (!id || !confirm("Voulez-vous vraiment supprimer ce rôle ?")) return;

            axios.post('{{ route('admin.settings.roles.delete', ['id' => '__replace_id__']) }}'.replace('__replace_id__',
                    id))
                .then(() => {
                    modalInstance.hide();
                    showToast('Rôle supprimé.', true);
                    setTimeout(() => window.location.reload(), 500);
                })
                .catch(err => showToast("Erreur lors de la suppression.", false));
        }

        // --- Helper Toast ---
        function showToast(msg, success) {
            const toastEl = document.getElementById('liveToast');
            const body = document.getElementById('toast-body');

            toastEl.className = `toast align-items-center border-0 ${success ? 'text-bg-success' : 'text-bg-danger'}`;
            body.textContent = msg;

            new bootstrap.Toast(toastEl).show();
        }

        // --- Barre de Recherche Avancée ---
        function filterTable() {
            // 1. Récupérer la valeur de la barre de recherche
            const searchInput = document.getElementById('searchInput').value.toLowerCase();

            // 2. Récupérer les valeurs cochées dans les filtres
            // On transforme en Array et on map pour avoir les valeurs en minuscule
            const checkedCamps = Array.from(document.querySelectorAll('.filter-camp:checked')).map(cb => cb.value
                .toLowerCase());
            const checkedAuras = Array.from(document.querySelectorAll('.filter-aura:checked')).map(cb => cb.value
                .toLowerCase());
            const checkedApparences = Array.from(document.querySelectorAll('.filter-apparence:checked')).map(cb => cb.value
                .toLowerCase());

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cells = row.getElementsByTagName('td');
                // Col 0: Camps, Col 1: Nom, Col 2: Aura, Col 3: Apparence
                const rowCampText = cells[0] ? cells[0].textContent.toLowerCase() : '';
                const rowNameText = cells[1] ? cells[1].textContent.toLowerCase() : '';
                const rowAuraText = cells[2] ? cells[2].textContent.toLowerCase() : '';
                const rowApparenceText = cells[3] ? cells[3].textContent.toLowerCase() : '';

                // --- LOGIQUE DE FILTRE ---

                // A. Recherche textuelle (Nom UNIQUEMENT)
                const matchesSearch = searchInput === '' || rowNameText.includes(searchInput);

                // B. Filtres Checkbox (Logique "ET" entre catégories, "OU" au sein d'une catégorie)
                const matchesCampFilter = checkedCamps.length === 0 || checkedCamps.some(camp => rowCampText
                    .includes(camp));
                const matchesAuraFilter = checkedAuras.length === 0 || checkedAuras.includes(rowAuraText.trim());
                const matchesAppFilter = checkedApparences.length === 0 || checkedApparences.includes(
                    rowApparenceText.trim());

                // C. Affichage final
                if (matchesSearch && matchesCampFilter && matchesAuraFilter && matchesAppFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endsection
