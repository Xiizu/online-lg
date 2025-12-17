<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ asset('css/immersive.css') }}" rel="stylesheet">
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
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}"><strong>LG-Online</strong></a>
            @elseif ($player || $not_loged)
                <a class="navbar-brand" href="{{ route('player.dashboard') }}"><strong>LG-Online</strong></a>
            @endif
            <div class="dropdown">
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
    </nav>
    @yield('body')
</body>

</html>
