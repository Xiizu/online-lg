@extends('layouts.app')
@section('title', 'Règles du Jeu')

@section('body')
@php
    $rules = [
        // PARTIE 1
        [
            'title' => 'Introduction - L’esprit du jeu',
            'content' => "<p>Ce jeu est une évolution profonde du <strong>Loup-Garou</strong> classique.<br>
                Il conserve son cœur : <strong>la déduction sociale</strong>, le mensonge, la manipulation et l’observation des comportements, tout en y ajoutant une <strong>grande richesse stratégique</strong> grâce à de nombreux rôles, camps et mécaniques inédites.</p>
                <p>Chaque partie est <strong>unique.</strong></p>
                <p>La vérité est toujours <strong>incertaine.</strong><br>
                Même les rôles à information ne peuvent jamais être totalement sûrs de ce qu’ils découvrent.</p>",
        ],
        // PARTIE 2
        [
            'title' => 'Vue d’ensemble',
            'content' => "<ul>
                <li>Les joueurs incarnent des personnages secrets appartenant à différents <strong>camps</strong></li>
                <li>Le jeu se déroule en <strong>cycles Jour / Nuit</strong></li>
                <li>La journée est consacrée aux <strong>discussions et accusations</strong></li>
                <li>La nuit permet aux rôles d’agir et aux joueurs de discuter librement dans leur village</li>
                <li>Deux villages existent en parallèle :
                    <ul>
                        <li><strong>Rougeval</strong></li>
                        <li><strong>Cendrelune</strong></li>
                    </ul>
                </li>
                <li>Chaque village a son propre <strong>Maire</strong> et son propre déroulement de jeu</li>
                <li>À partir de <strong>15 joueurs ou moins au total</strong>, les deux villages fusionnent en un seul</li>
            </ul>
            <p>🎯 <strong>Objectif</strong> : Être le <strong>dernier camp</strong> en vie.</p>",
        ],
        // PARTIE 3
        [
            'title' => 'Mise en place de la partie',
            'subparts' => [
                ['subtitle' => 'Rôles et camps', 'text' => "<p>Chaque joueur reçoit <strong>un rôle secret</strong> appartenant à un camp.</p>
                <p>Les camps possibles sont :</p>
                <ul>
                    <li>Villageois</li>
                    <li>Loups-Garous</li>
                    <li>Roux-Garous</li>
                    <li>Soldats Anglais</li>
                    <li>Dieux</li>
                    <li>Vampires</li>
                    <li>La Secte</li>
                    <li>Rôles Solitaires</li>
                </ul>
                <p>👉 La composition exacte de la partie n’est pas dévoilée.</p>"],
                ['subtitle' => 'Répartition en villages', 'text' => "<p>Au début de la partie :</p>
                <ul>
                    <li>Les joueurs sont répartis entre <strong>Rougeval</strong> et <strong>Cendrelune</strong></li>
                    <li>Chaque village fonctionne de manière <strong>totalement indépendante :</strong></li>
                    <ul>
                        <li>son propre jour</li>
                        <li>sa propre nuit</li>
                        <li>ses propres morts</li>
                        <li>son propre Maire</li>
                        <li>son propre vote</li>
                    </ul>
                </ul>
                <p>Chaque village dispose de :</p>
                <ul>
                    <li><strong>Maître du Jeu dédié</strong></li>
                    <li><strong>une zone de jeu distincte</strong></li>
                </ul>
                <p>⚠️ <strong>Interdiction absolue :</strong></p>
                <ul>
                    <li>de parler aux joueurs de l’autre village</li>
                    <li>de transmettre des informations entre villages</li>
                </ul>

                "],
                ['subtitle'=>'Élection du Maire','text'=>"<p>Chaque village élit <strong>un Maire</strong> au début de la partie.</p>
                <p><strong>Procédure normale</strong></p>
                <ul>
                    <li>tous les joueurs du village votent</li>
                    <li>le joueur avec <strong>le plus de votes</strong> devient Maire</li>
                </ul>
                <p><strong>Exception :</strong></p>
                <p>Un rôle spécifique peut être <strong>Maire dès le début</strong>. Si ce rôle est présent, aucun vote n’a lieu.</p>"],
                ['subtitle' => 'Pouvoirs et statut du Maire', 'text' => "<p>Le Maire a un rôle <strong>politique central.</strong></p>
                <ul>
                    <li>Le Maire <strong>ne peut jamais être exilé</strong> de son village</li>
                    <li>Chaque nuit, il peut :</li>
                    <ul>
                        <li><strong>exiler un joueur</strong> vers l’autre village</li>
                        <li>choisir de <strong>ne pas exiler</strong></li>
                    </ul>
                </ul>
                <p>🧭 <strong>Exil :</strong></p>
                <ul>
                    <li>Un joueur exilé quitte son village et rejoint l’autre</li>
                    <li>L’exil est <strong>annoncé publiquement</strong></li>
                    <li>Il conserve son rôle et son camp</li>
                    <li>Un maximum de <strong>un joueur par nuit et par village</strong> peut être exilé</li>
                </ul>"],
                ['subtitle' => 'Succession du Maire', 'text' => "
                <ul>
                    <li>Dès son élection, le Maire désigne <strong>un successeur</strong> en secret</li>
                    <li>Le successeur n’a <strong>aucun pouvoir particulier</strong> tant qu’il n’est pas Maire</li>
                </ul>
                <h5>En cas de décès :</h5>
                <ul>
                    <li>Si le Maire meurt → le successeur devient Maire</li>
                    <li>Le nouveau Maire choisit immédiatement son propre successeur</li>
                </ul>
                <h5>Cas particuliers :</h5>
                <ul>
                    <li>Si le successeur meurt avant le Maire → le Maire en désigne un nouveau</li>
                    <li>Si le Maire <strong>et</strong> son successeur meurent en même temps →  un nouveau <strong>vote</strong> est organisé dans le village</li>
                </ul>"],
                ['subtitle' => 'Fusion des villages', 'text' => "<p>Lorsque le nombre total de joueurs vivants atteint <strong>15 ou moins</strong> :<p>
                <ul>
                    <li>Rougeval et Cendrelune <strong>fusionnent</strong></li>
                    <li>Il ne reste plus qu’un <strong>seul village</strong></li>
                    <li>Tous les Maires sont <strong>destitués</strong></li>
                    <li>Les anciens Maires redeviennent de simples habitants</li>
                    <li>Le jeu continue</li>
                </ul>"]
            ]
        ],
        // PARTIE 4
        [
            'title' => 'Déroulement d’un cycle de jeu',
            'content' => "<p>Un cycle de jeu est composé de <strong>deux phases</strong> :</p>
            <ol>
                <li><strong>Le Jour</strong></li>
                <li><strong>La Nuit</strong></li>
            </ol>
            <p>Chaque village joue son cycle <strong>en simultané</strong>, sous la supervision de son Maître du Jeu, tant que les villages sont séparés."
        ],
        // PARTIE 5
        [
            'title' => 'La phase de Jour – Discussions & accusations',
            'subparts' => [
                ['subtitle' => 'Discussion libre', 'text' => "<p>Pendant la journée :</p>
                <ul>
                    <li>Tous les joueurs <strong>vivants</strong> du village peuvent parler librement</li>
                    <li>Le mensonge est <strong>autorisé</strong>, y compris sur :</li>
                    <ul>
                        <li>son rôle</li>
                        <li>son camp</li>
                        <li>ses informations</li>
                    </ul>
                </ul>
                <p>🚫 <strong>Interdictions strictes</strong></p>
                <ul>
                    <li>Montrer une preuve formelle de son rôle</li>
                    <li>Prouver son rôle par un élément extérieur au jeu</li>
                    <li>Communiquer avec l’autre village</li>
                    <li>Faire de l’anti-jeu (refuser de jouer, saboter volontairement la partie)</li>
                </ul>
                <p>⚠️ Le Maître du Jeu peut sanctionner toute infraction.</p>"],
                ['subtitle' => 'Le vote par accusation', 'text' => "<p>Le jeu utilise un système appelé <strong>Vote par accusation</strong>, intégré directement au débat.</p>"],
                ['subtitle' => 'Lancer une accusation', 'text' => "<p>À tout moment durant la discussion, un joueur peut dire clairement :</p>
                <strong>&emsp;« J’accuse [Nom du joueur] »</strong>
                <ul>
                    <li>Un joueur peut accuser <strong>plusieurs personnes différentes</strong></li>
                    <li>Accuser <strong>n’interrompt pas</strong> la discussion</li>
                    <li>Les accusations restent actives tant qu’elles ne sont pas résolues</li>
                </ul>"],
                ['subtitle' => 'Déclenchement d’un jugement', 'text' => "<p>Un joueur est officiellement jugé lorsque :</p>
                <ul>
                    <li>À <strong>plus de 12 joueurs vivants</strong> dans le village :</li>
                    <ul>
                        <li>Il est accusé par <strong>3 joueurs différents</strong></li>
                    </ul>
                    <li>À <strong>12 joueurs vivants ou moins</strong> :</li>
                    <ul>
                        <li>Il est accusé par <strong>2 joueurs différents</strong></li>
                    </ul>
                </ul>
                <p>👉 À ce moment-là :</p>
                <p><strong>Toute discussion s’arrête immédiatement.</strong></p>"],
                ['subtitle' => 'Phase d’accusation', 'text' => "<ul><li>Seul le <strong>premier joueur ayant accusé</strong> peut parler</li>
                    <li>Il a deux choix :</li>
                    <ol>
                        <li>Expliquer lui-même pourquoi il accuse le joueur</li>
                        <li>Donner la parole à <strong>un autre accusateur</strong></li>
                    </ol>
                </ul>
                <p>Cette phase permet de poser clairement les charges.</p>"],
                ['subtitle' => 'Phase de défense', 'text' => "<p>Ensuite :</p>
                <ul>
                    <li>Le joueur accusé est <strong>le seul autorisé à parler</strong></li>
                    <li>Il peut dire <strong>tout ce qu’il souhaite</strong> pour se défendre :</li>
                    <ul>
                        <li>Mentir</li>
                        <li>Dire la vérité</li>
                        <li>Accuser en retour</li>
                        <li>Rester silencieux</li>
                    </ul>
                </ul>"],
                ['subtitle' => 'Vote final : Éliminer ou gracier', 'text' => "<p>Tous les joueurs du village encore en vie votent :</p>
                <ul>
                    <li>👍 <strong>Pouce vers le haut</strong> → Gracier le joueur</li>
                    <li>👎 <strong>Pouce vers le bas</strong> → Éliminer le joueur</li>
                </ul>
                <p><strong>Résolution :</strong></p>
                <ul>
                    <li>Plus de 👎 →</li>
                    <ul>
                        <li>Le joueur est <strong>éliminé</strong></li>
                        <li>Son <strong>rôle est révélé</strong></li>
                        <li>La journée prend fin</li>
                        <li>La nuit <strong>commence immédiatement</strong></li>
                    </ul>
                    <li>Plus de 👍 →</li>
                    <ul>
                        <li>Le joueur est <strong>sauvé</strong></li>
                        <li>Il <strong>ne peut plus être accusé</strong> ce jour-là</li>
                        <li>La discussion reprend</li>
                    </ul>
                </ul>"],
                ['subtitle' => 'Égalité de votes', 'text' => "<p>En cas d’égalité entre 👍 et 👎 :</p>
                <ul>
                    <li><strong>1er ou 2e jugement du jour</strong> → Le joueur est <strong>considéré comme sauvé</strong></li>
                    <li><strong>3e jugement du jour</strong> → L'égalité équivaut à la <strong>mort du joueur</strong>"],
                ['subtitle' => 'Limite quotidienne d’accusations', 'text' => "<ul><li>Un maximum de <strong>3 accusations abouties</strong> peut avoir lieu par jour</li>
                    <li>Après la <strong>3e accusation</strong>, la nuit tombe <strong>obligatoirement</strong>, même si personne ne meurt</li></ul>"]

            ]
        ],
        // PARTIE 6
        [
            'title' => 'La phase de Nuit',
            'subparts' => [
                ['subtitle' => 'Début de la nuit', 'text' => "<ul><li>Tous les joueurs quittent la table</li>
                    <li>Chaque village utilise <strong>sa zone de jeu</strong></li>
                    <li>Les joueurs peuvent se déplacer <strong>librement</strong> dans leur zone</li></ul>"],
                ['subtitle' => 'Discussions nocturnes', 'text' => "<ul><li>La nuit, <strong>tous les joueurs peuvent parler</strong></li>
                    <li>Les discussions doivent être faites <strong>physiquement</strong>, jamais par téléphone</li>
                    <li>Il n’y a <strong>aucune restriction de parole</strong>, sauf :</li>
                    <ul>
                        <li>interdiction de parler à l’autre village</li>
                        <li>interdiction de prouver son rôle</li>
                    </ul>
                    <p>🕵️ Les camps agissant en groupe (Loups-Garous, Roux-Garous, Vampires…) doivent se retrouver discrètement, sans éveiller les soupçons si il souhaite se mettre d'accord sur un vote.<p>"],
                ['subtitle' => 'Utilisation des pouvoirs', 'text' => "<ul><li>Les pouvoirs sont activés <strong>uniquement via le téléphone</strong> auprès du Maître du Jeu</li>
                    <li>Le joueur doit rester <strong>discret</strong> lorsqu’il utilise son pouvoir</li>
                    <li>Le MJ applique les effets sans jamais confirmer publiquement l’action</li>
                    </ul>"],
                ['subtitle' => 'Fin de la nuit', 'text' => "<p>À la fin de la nuit :</p>
                <ul>
                    <li>Tous les joueurs sont rappelés autour de la table</li>
                    <li>Le Maître du Jeu annonce :</li>
                    <ul>
                        <li>les morts</li>
                        <li>l’exil potentiel décidé par le Maire</li>
                    </ul>
                    <li>Le jour recommence.</li>
                </ul>"]
            ]
        ],
        // PARTIE 7
        [
            'title' => 'Mort & joueurs éliminés',
            'subparts' => [
                ['subtitle' => 'Effet de la mort', 'text' => "<p>Lorsqu’un joueur meurt :</p>
                <ul>
                    <li>Son rôle est <strong>révélé publiquement</strong></li>
                    <li>Il est retiré définitivement de la partie active</li>
                    <li>Il rejoint le groupe des joueurs morts</li>
                </ul>"],
                ['subtitle' => 'Statut des morts', 'text' => "<p>Les joueurs morts :</p>
                <ul>
                    <li>Peuvent se déplacer librement dans l’ensemble de la salle</li>
                    <li>Peuvent parler <strong>entre eux</strong></li>
                    <li>Ne peuvent <strong>plus</strong> :</li>
                    <ul>
                        <li>Participer aux débats de jour</li>
                        <li>Influencer les joueurs vivants</li>
                        <li>Parler de la partie en cours pendant la nuit</li>
                    </ul>
                </ul>
                <p>Toute infraction peut entraîner une <strong>sanction</strong> du MJ, y compris sur des joueurs morts."]
            ]
        ],
        // PARTIE 8
        [
            'title' => 'Aura & Apparence',
            'subparts' => [
                ['subtitle' => 'Introduction', 'text' => "<p>L’aura et l’apparence sont des mécaniques centrales du jeu.</p>
                <p>Elles fournissent des <strong>informations fiables mais incomplètes.</strong></p>"],
                ['subtitle' => 'L’Aura', 'text' => "<p>Chaque rôle possède une <strong>aura définie.</strong></p>
                <ul>
                    <li>Aura <strong>lumineuse</strong> : généralement associée aux rôles villageois</li>
                    <li>Aura <strong>obscure</strong> : généralement associée aux rôles hostiles</li>
                    <li>Aura <strong>neutre</strong> : généralement associée aux rôles solitaires</li>
                </ul>
                <p>⚠️ <strong>Attention</strong></p> :
                <ul>
                    <li>Il existe de <strong>nombreuses exceptions</strong></li>
                    <li>Certains rôles ont une aura trompeuse ou inhabituelle.</li>
                    <li>Une aura <strong>ne garantit jamais</strong> le camp réel du joueur.</li>
                </ul>
                <p>Un rôle à information recevra une réponse claire, par exemple :</p>
                <p>&emsp;« Le joueur espionné a une aura obscure »</p>"],
                ['subtitle' => 'L’Apparence', 'text' => "<p>L’apparence indique si un joueur semble :</p>
                <ul>
                    <li><strong>Humain</strong></li>
                    <li><strong>Bête</strong></li>
                    <li><strong>Divin</strong></li>
                </ul>
                <p>⚠️ Là encore, les exceptions sont nombreuses :</p>
                <ul>
                    <li>certains rôles du village ont une apparence de bête</li>
                    <li>certains loups ont une apparence humaine</li>
                    <li>les vampires ont une apparence humaine</li>
                </ul>
                <p>Exemple de réponse claire :</p>
                <p>&emsp;« Le joueur espionné a une apparence humaine »</p>"]
            ]
        ],
        // PARTIE 9
        [
            'title' => 'Camps & conditions de victoire',
            'subparts' => [
                ['subtitle' => 'Principe général', 'text' => "<ul><li><strong>Un seul camp gagne la partie</strong></li>
                    <li>Tous les autres camps perdent</li></ul>"],
                ['subtitle' => 'Victoire des camps', 'text' => "<p>Un camp gagne lorsque :</p>
                <ul>
                    <li>Tous les autres camps sont éliminés</li>
                    <li>Ou lorsqu’une condition spéciale de rôle est remplie (seule existante : fin anticipée à 2 ou 3 joueurs par des rôles solitaires)</li></ul>"],
                ['subtitle' => 'Rôles solitaires', 'text' => "<ul><li>Un rôle solitaire ne peut gagner que s’il est <strong>encore en vie</strong></li>
                    <li>S’il gagne :</li>
                    <ul>
                        <li>Il est <strong>le seul vainqueur</strong></li>
                        <li>Tous les autres camps perdent</li>
                    </ul>
                    <li>Personne ne sait :</li>
                    <ul>
                        <li>Combien de rôles solitaires existent</li>
                        <li>Ni s’ils sont présents dans la partie.</li>
                    </ul>
                </ul>"],
                ['subtitle' => 'Autres camps', 'text' => "<p>Chaque camps a <strong>une spécialité</strong> qui peut lui permettre de remporter la victoire :</p>
                <ul>
                    <li>Nombre de membre plus important</li>
                    <li>Connaissance des autres membres et possibilité de tuer la nuit</li>
                    <li>Agrandir leurs nombre de membres</li></ul>"]
            ]
        ],
        // PARTIE 10
        [
            'title' => 'Sanctions & autorité du Maître du Jeu',
            'subparts' => [
                ['subtitle' => 'Informations', 'text' => "<p>Le Maître du Jeu (MJ) est le <strong>garant du bon déroulement de la partie</strong>.</p>
                <p>Il a autorité pour intervenir à tout moment afin de préserver l’équité, l’immersion et l’esprit du jeu.</p>"],
                ['subtitle' => 'Infractions sanctionnables', 'text' => "<p>Un joueur s’expose à une sanction s’il :</p>
                <ul>
                    <li>Montre une <strong>preuve formelle</strong> de son rôle</li>
                    <li>Utilise un élément extérieur pour prouver son identité</li>
                    <li>Communique avec des membres de l’autre village avant la fusion</li>
                    <li>Parle de la partie en cours lorsqu’il est mort avec des joueurs vivants</li>
                    <li>Fait volontairement de l’<strong>anti-jeu</strong> (sabotage, refus de jouer, nuisance volontaire)</li>
                </ul>"],
                ['subtitle' => 'Types de sanctions', 'text' => "<p>Selon la gravité de l’infraction, le MJ peut décider :</p>
                <ul>
                    <li>d’un <strong>rappel à l’ordre</strong></li>
                    <li>d’un <strong>dévoilement partiel</strong> du rôle</li>
                    <li>d’un <strong>dévoilement total</strong> du rôle</li>
                    <li>ou d’une <strong>élimination immédiate</strong> du joueur</li>
                </ul>
                <p>⚠️ <strong>L’élimination par sanction est définitive</strong></p>
                <ul>
                    <li>Le joueur est considéré comme mort</li>
                    <li>Son rôle est révélé</li>
                    <li>Il rejoint immédiatement les autres morts</li>
                </ul>
                <p>Cette décision est <strong>sans appel</strong>.</p>"]
            ]
        ],
    ];
@endphp

{{-- AFFICHAGE : --}}
<style>
    /* Correction de l'affichage des puces des listes */
    .card-body ul { list-style-type: disc; padding-left: 1.25rem; margin-left: 0; }
    .card-body ul ul { list-style-type: circle; }
</style>
<div class="container mt-4">
    <div class="mb-5 text-center">
        <h1 class="display-4 fw-bold">Règles Officielles</h1>
    </div>

    <div class="row">
        {{-- Sommaire Latéral (Sticky) --}}
        <div class="col-md-3 d-none d-md-block">
            <nav id="rules-nav" class="nav nav-pills flex-column sticky-top" style="top: 20px; max-height: 90vh; overflow-y: auto;">
                @foreach($rules as $index => $part)
                    <a class="nav-link {{ $index === 0 ? 'active' : '' }} mb-2" href="#part-{{ $index }}">
                        {{ $part['title'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Contenu Principal --}}
        <div class="col-md-9">
            <div data-bs-spy="scroll" data-bs-target="#rules-nav" data-bs-offset="0" tabindex="0">
                @foreach($rules as $index => $part)
                    <div id="part-{{ $index }}" class="card mb-4 shadow-sm border-0">
                        <div class="card-header bg-primary text-white py-3">
                            <h3 class="h5 m-0 fw-bold">{{ $part['title'] }}</h3>
                        </div>
                        <div class="card-body">
                            @if(isset($part['subparts']))
                                @foreach($part['subparts'] as $sub)
                                    <div class="mb-4">
                                        <h5 class="text-primary fw-bold border-bottom pb-2">{{ $sub['subtitle'] }}</h5>
                                        <div class="">
                                            {!! $sub['text'] ?? '' !!}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                {!! $part['content'] ?? '' !!}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Script pour le ScrollSpy fluide --}}
<script>
    document.addEventListener("DOMContentLoaded", function(){
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#rules-nav'
        });
    });
</script>
@endsection
