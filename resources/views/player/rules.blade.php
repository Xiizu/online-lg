@extends('layouts.app')
@section('title', 'Règles du Jeu')

@section('body')
    <div class="container mt-4">

        <div class="mb-4">
            <h1>Règles du Jeu</h1>
            <p class="text-muted">Bienvenue dans le jeu en ligne ! Cliquez ci-dessous pour lire les règles.</p>
        </div>

        <div class="accordion" id="rulesAccordion">
            {{-- Partie 1 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Partie 1 : Les Bases
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#rulesAccordion">
                    <div class="accordion-body">
                        Bienvenue dans cette première section. Ici, nous abordons les fondamentaux du jeu.
                        Le but est de comprendre l'environnement et les interactions de base entre les joueurs.
                        Tout commence par la distribution des rôles et la prise de connaissance de votre objectif secret.
                    </div>
                </div>
            </div>

            {{-- Partie 2 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Partie 2 : Le Déroulement
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#rulesAccordion">
                    <div class="accordion-body">
                        Cette seconde partie détaille les phases de jeu (Jour et Nuit).
                        Durant la nuit, certains rôles s'éveillent pour utiliser leurs pouvoirs.
                        Durant le jour, tout le village débat pour éliminer un suspect.
                        La communication est la clé, mais attention aux mensonges !
                    </div>
                </div>
            </div>

            {{-- Partie 3 --}}
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Partie 3 : Fin de Partie & Fair-play
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#rulesAccordion">
                    <div class="accordion-body">
                        Enfin, cette dernière partie explique les conditions de victoire pour chaque camp.
                        Le jeu se termine lorsqu'un camp a éliminé tous ses adversaires ou rempli une condition spéciale.
                        N'oubliez pas : le respect et le fair-play sont obligatoires pour que l'expérience reste amusante pour tous.
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
