<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/immersive.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
@php
    use App\Models\Player;
    use App\Models\Admin;

    $admin = false;
    $player = false;
    $no_loged_route = ['home', 'login', 'logout', 'rules'];
    $not_loged = false;
    if (session()->has('authenticated') && session('authenticated')) {
        $admin = true;
    } elseif (session()->has('player_token') && session('player_token')) {
        $player = true;
    } elseif (in_array(request()->route()->getName(), $no_loged_route)) {
        $not_loged = true;
    }
    if (!$admin && !$player && !$not_loged) {
        abort(400, 'Unauthorized access');
    }

@endphp

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-md">
            @if ($admin)
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('logo.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2 rounded-circle">
                    <strong>LG-Online</strong>
                </a>
            @elseif ($player || $not_loged)
                <a class="navbar-brand" href="{{ route('player.dashboard') }}">
                    <img src="{{ asset('logo.png') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2 rounded-circle">
                    <strong>LG-Online</strong>
                </a>
            @endif
            <div class="d-flex align-items-center gap-2">
                <button id="theme-toggle" class="btn btn-primary"><i class="bi bi-sun-fill"></i></button>
                <script>
                    const toggleButton = document.getElementById('theme-toggle');
                    const htmlElement = document.documentElement;

                    toggleButton.addEventListener('click', () => {
                        const currentTheme = htmlElement.getAttribute('data-bs-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        htmlElement.setAttribute('data-bs-theme', newTheme);
                        toggleButton.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-fill"></i>';
                    });
                </script>
                <div class="dropstart">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Menu
                    </a>
                    <ul class="dropdown-menu">
                        @if ($admin)
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard', ['openModal' => true]) }}">Créer
                                    une partie</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.games.index') }}">Gestion des Parties</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('admin.settings.index') }}">Paramètres</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.notes.index') }}">Éditeur de Notes</a></li>
                        @endif
                        @if ($player)
                            <li><a class="dropdown-item" href="{{ route('player.dashboard') }}">Tableau de bord</a></li>
                        @endif
                        <li><a class="dropdown-item" href="{{ route('admin.settings.roles.index') }}">Liste des Rôles</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('player.showRules') }}">Règles du Jeu</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    @yield('body')
</body>

</html>
