@extends('layouts.app')
@section('title', 'Tableau de bord Joueur')

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
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="card shadow-lg border-0 overflow-hidden mb-4">
                    <div class="card-header bg-dark text-white py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <h1 class="h4 mb-2 fw-bold text-uppercase ls-1">
                                <i class="bi bi-person-circle me-2"></i> {{ $player->nom }}
                                <small class="fw-normal text-gray-600 ls-0">Village : ({{ $player->game->name }})</small>
                            </h1>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($player->etats as $etat)
                                    <span class="badge rounded-pill text-dark border border-light shadow-sm"
                                        style="background-color: {{ $etat->color ?? '#f8f9fa' }}; cursor: pointer;"
                                        data-bs-toggle="modal" data-bs-target="#etatModal{{ $etat->id }}"
                                        title="Voir la description">
                                        {{ $etat->label }}
                                    </span>
                                    <div class="modal fade text-dark" id="etatModal{{ $etat->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold"
                                                        style="color: {{ $etat->color ?? '#000' }}">
                                                        {{ $etat->label }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pt-2 text-secondary">
                                                    {{ $etat->description }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="ms-3">
                            @if ($player->is_alive)
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="bi bi-heart-pulse-fill me-1"></i> Vivant
                                </span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-2">
                                    <i class="bi bi-skull-fill me-1"></i> Mort
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-5 text-center">
                            <div class="position-relative d-inline-block w-100 mb-3" style="max-width: 300px;">
                                @if ($player->role->image_path)
                                    <img src="{{ asset('storage/' . $player->role->image_path) }}"
                                        alt="{{ $player->role->nom }}"
                                        class="img-fluid rounded-3 shadow border border-4 border-light"
                                        style="width: 100%; height: auto; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-3 shadow d-flex align-items-center justify-content-center text-white"
                                        style="width: 100%; aspect-ratio: 1/1;">
                                        <i class="bi bi-image display-1 opacity-50"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <div class="card border-0 bg-light shadow-sm">
                                    <div class="card-body py-2 px-3">
                                        <small class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Votre
                                            Camp</small>
                                        <span class="badge w-100 text-white"
                                            style="background-color: {{ $player->camp->color }};">
                                            {{ $player->camp->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex flex-column h-100 gap-3">
                                <div class="text-center text-md-start">
                                    <small class="text-uppercase text-muted fw-bold">Rôle actuel</small>
                                    <h2 class="fw-bold text-primary mb-0">{{ $player->role->nom }}</h2>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="p-2 border rounded bg-light text-center">
                                            <small class="d-block text-muted text-uppercase fw-bold"
                                                style="font-size: 0.7rem;">Aura</small>
                                            <span class="badge text-dark w-100"
                                                style="background-color: {{ getColor($player->role->aura) }}">{{ $player->role->aura }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 border rounded bg-light text-center">
                                            <small class="d-block text-muted text-uppercase fw-bold"
                                                style="font-size: 0.7rem;">Apparence</small>
                                            <span class="badge text-dark w-100"
                                                style="background-color: {{ getColor($player->role->apparence) }}">{{ $player->role->apparence }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-info-subtle p-3 rounded border">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-exclamation-circle fs-5 me-2"></i>
                                        <h6 class="alert-heading fw-bold mb-0">Informations</h6>
                                    </div>
                                    <p class="mb-0 small text-muted" style="max-height: 100px; overflow-y: auto;">
                                        {{ $player->comment ?? 'il n\'y à rien pour l\'instant.' }}
                                    </p>
                                </div>

                                <div class="d-flex flex-column gap-2">

                                    <!-- Bloc Pouvoir -->
                                    <div class="alert alert-warning shadow-sm mb-0 p-3 rounded border" role="alert">
                                        <!-- En-tête cliquable -->
                                        <div class="d-flex align-items-center justify-content-between"
                                            style="cursor: pointer;" data-bs-toggle="collapse"
                                            data-bs-target="#powerContent{{ $player->id }}" aria-expanded="false">
                                            <h6 class="fw-bold text-muted text-uppercase small mb-0">Pouvoir</h6>
                                            <i class="bi bi-chevron-down small text-muted"></i>
                                        </div>
                                        <!-- Contenu dépliable -->
                                        <div class="collapse mt-2" id="powerContent{{ $player->id }}">
                                            <p class="mb-0 small" style="line-height: 1.4;">
                                                {{ $player->role->pouvoir ?? 'Aucun pouvoir particulier.' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Bloc Description -->
                                    <div class="bg-light p-3 rounded border">
                                        <!-- En-tête cliquable -->
                                        <div class="d-flex align-items-center justify-content-between"
                                            style="cursor: pointer;" data-bs-toggle="collapse"
                                            data-bs-target="#descContent{{ $player->id }}" aria-expanded="false">
                                            <h6 class="fw-bold text-muted text-uppercase small mb-0">Description</h6>
                                            <i class="bi bi-chevron-down small text-muted"></i>
                                        </div>
                                        <!-- Contenu dépliable -->
                                        <div class="collapse mt-2" id="descContent{{ $player->id }}">
                                            <p class="mb-0 small text-secondary fst-italic">
                                                {{ $player->role->description }}
                                            </p>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    @include('layouts.map', [
        'gameId' => $player->game->id,
        'currentPlayerId' => $player->id,
    ])

@endsection
