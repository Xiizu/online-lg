@extends('layouts.app')
@section('title', 'Attribution des Rôles')

@section('body')
    @php
        use App\Models\Player;
        use App\Models\Game;

        $player_id = session('player');
        $player = Player::find($player_id);

        $game_id = session('game');
        $game = Game::find($game_id);

        $token = $player->token ?? '';
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode(url("/player/$token")) . "&size=200x200";
    @endphp

    <div class="container mt-4 align-items-center d-flex flex-column">
        <div class="card align-items-center">
            <div class="card-body text-center align-items-center d-flex flex-column">
                <h3 class="card-title mb-4">Attribution du Rôle pour {{ $player->name }}</h3>

                <label for="playerName">Entrez le prénom du joueur :</label>
                <input type="text" id="playerName" class="form-control mb-3" placeholder="Prenom du joueur">

                <p><strong>Lien attribué :</strong> <a href="{{ url('/player/' . $player->token) }}">{{ url('/player/' . $player->token) }}</a></p>
                <div class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true"></div>
                <button class="btn btn-secondary"
                        onclick="copyToClipboard('{{ url('/player/' . $player->token) }}', 'Lien copié !')">
                    Copier le lien
                </button>

                <div class="mt-4 d-flex flex-column align-items-center">
                    <h5>Code QR pour le Token :</h5>
                    <img src="{{ $qrCodeUrl }}" alt="QR Code pour le Token de {{ $player->name }}">
                </div>

                <button class="btn btn-primary mt-4" onclick="nextPlayer()">Joueur suivant</button>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(url, message) {
            navigator.clipboard.writeText(url).then(() => {
                const toast = document.querySelector('.toast');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>`;
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }).catch(() => alert('Impossible de copier le lien.'));
        }

        function nextPlayer() {
            const playerName = document.getElementById('playerName').value.trim();
            if (!playerName) {
                alert('Veuillez entrer le prénom du joueur.');
                return;
            }

            const baseUrl = "{{ route('admin.games.start', ['id' => $game->id, 'player' => $player->id]) }}";
            const url = baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'playerName=' + encodeURIComponent(playerName);
            window.location.href = url;
        }
    </script>
@endsection
