<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Etat;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\Camp;
use App\Models\Liage;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $password = $request->input('password');

        // Replace 'your_admin_password' with the actual admin password
        if ($password === 'Ffl0*_3v3nt_1g_p4ssw0rd') {
            $request->session()->put('authenticated', true);
            // Authentication successful
            return redirect()->route('admin.dashboard'); // Adjust the route as needed
        } else {
            // Authentication failed
            return redirect()->route('home')->withErrors(['password' => 'Invalid password.']);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('authenticated');
        $request->session()->forget('player_token');
        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    public function dashboard(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }
        $openModal = False;
        if ($request->has('openModal')) {
            $openModal = True;
        }
        return view('admin.dashboard', ['openModal' => $openModal]);
    }

    public function createGame(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }
        $validated = $request->validate([
            'game_name' => 'required|string|max:191',
            'game_date' => 'required|string',
            'roles'     => 'required|array',
        ]);

        try {
            $game = new Game();
            $game->status = 'setup';
            $game->name = $validated['game_name'];
            $game->date = $validated['game_date'];
            $game->save();

            foreach ($validated['roles'] as $roleId => $count) {
                $count = (int)$count;
                $role = Role::with('camps')->find($roleId);
                $defaultCampId = $role?->camps->sortBy('id')->first()?->id;

                for ($i = 0; $i < $count; $i++) {
                    $player = new Player();
                    $player->role_id = $roleId;
                    $player->game_id = $game->id;
                    $player->camp_id = $defaultCampId; // camp par défaut : plus petit id
                    $player->is_alive = true;
                    $player->nom = 'Unknown';
                    $player->token = (string) Str::uuid();
                    $player->save();
                }
            }
            return response()->json(['success' => 'Game created successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the game. Please try again.\n' . $e->getMessage()], 500);
        }
    }

    public function indexGame()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        $games = Game::all();
        return view('admin.games', ['games' => $games]);
    }

    public function startGame($id, Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        if ($request->has('player') && $request->has('playerName')) {
            $player_id = $request->input('player');
            $playerName = $request->input('playerName');
            $player = Player::find($player_id);
            if ($player) {
                $player->nom = $playerName;
                $firstCamp = $player->role->camps->first();
                if ($firstCamp) {
                    $player->camp_id = $firstCamp->id;
                }
                $player->save();
            }
        }

        $game = Game::find($id);
        if ($game && $game->status === 'setup') {
            $players = Player::where('game_id', $game->id)->where('nom', 'Unknown')->get();
            if ($players->isEmpty()) {
                $game->status = 'started';
                $game->save();
                return redirect()->route('admin.games.view', ['id' => $game->id])->with('success', 'Game started successfully.');
            }
            $randomPlayer = $players->random();
            session()->put('game', $game->id);
            session()->put('player', $randomPlayer->id);
            return view('admin.assign');
        } else {
            return redirect()->back()->withErrors(['game' => 'Game not found or cannot be started.']);
        }
    }

    public function endGame($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        $game = Game::find($id);
        if ($game && $game->status === 'started') {
            $game->status = 'ended';
            $game->save();
            return response()->json(['success' => 'Game ended successfully.'], 200);
        } else {
            return response()->json(['error' => 'Game not found or cannot be ended.'], 404);
        }
    }

    public function viewGame($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        $game = Game::find($id);
        if ($game) {
            $players = Player::where('game_id', $game->id)->get();
            return view('admin.manage', ['game' => $game, 'players' => $players]);
        } else {
            return redirect()->back()->withErrors(['game' => 'Game not found.']);
        }
    }

    public function deleteGame($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $game = Game::find((int)$id);
        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        // Suppression des joueurs associés
        Player::where('game_id', $game->id)->delete();

        // Suppression des liages associés
        Liage::where('first_game_id', $game->id)
              ->orWhere('second_game_id', $game->id)
              ->delete();

        // Suppression de la partie
        $game->delete();

        return response()->json(['success' => 'Game deleted successfully.']);
    }

    public function getPlayerInfo($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $player = Player::find((int)$id);
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }
        $role = $player->role;
        $etats = $player->etats;

        return response()->json([
            'id'       => $player->id,
            'nom'      => $player->nom,
            'role'     => $role,
            'camp'     => $player->camp?->name,
            'camp_id'  => $player->camp_id,
            'game_id'  => $player->game_id,
            'is_alive' => $player->is_alive,
            'comment'  => $player->comment,
            'etats'    => $etats,
            'token'    => $player->token,
        ], 200);
    }

    public function updatePlayer(Request $request, $id)
    {
        // 1. Vérification Auth
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Récupération du joueur
        $player = Player::find((int)$id);
        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // 3. Validation
        $validated = $request->validate([
            'nom'      => 'required|string|max:191',
            'role_id'  => 'required|integer|exists:roles,id',
            'camp_id'  => 'nullable|integer|exists:camps,id',
            'is_alive' => 'boolean', // axios envoie true/false
            'comment'  => 'nullable|string',
            'etats'    => 'array', // Peut être vide []
            'etats.*'  => 'integer|exists:etats,id', // On valide que chaque item est un ID d'état existant
            'game_id'  => 'nullable|integer|exists:games,id',
        ]);

        $player->nom = $validated['nom'];
        $player->role_id = $validated['role_id'];
        $player->camp_id = $validated['camp_id'] ?? null;
        $player->is_alive = $validated['is_alive'] ?? false;
        $player->comment = $validated['comment'] ?? '';

        // Déplacement éventuel du joueur vers l'autre partie (mode liage)
        if (array_key_exists('game_id', $validated) && !empty($validated['game_id'])) {
            $targetGameId = (int)$validated['game_id'];
            if ($targetGameId !== (int)$player->game_id) {
                $canMove = Liage::where(function($q) use ($player, $targetGameId) {
                                $q->where('first_game_id', $player->game_id)
                                  ->where('second_game_id', $targetGameId);
                            })
                            ->orWhere(function($q) use ($player, $targetGameId) {
                                $q->where('second_game_id', $player->game_id)
                                  ->where('first_game_id', $targetGameId);
                            })
                            ->exists();

                if (!$canMove) {
                    return response()->json(['error' => 'Invalid target game for player move.'], 422);
                }
                $player->game_id = $targetGameId;
            }
        }

        $player->save();

        if (array_key_exists('etats', $validated)) {
             $player->etats()->sync($validated['etats']);
        }

        return response()->json(['success' => 'Player updated successfully.']);
    }

    public function getRoles()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $roles = Role::with('camps')->get();
        return response()->json($roles, 200);
    }

    public function getEtats()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $etats = Etat::all();
        return response()->json($etats, 200);
    }

    public function getCamps()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $camps = Camp::all();
        return response()->json($camps, 200);
    }

    public function settings()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        return view('admin.settings');
    }

    public function createEtat(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:191',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $etat = new Etat();
        $etat->label = $validated['label'];
        $etat->description = $validated['description'] ?? '';
        // Si la colonne color existe dans votre table etats :
        $etat->color = $validated['color'] ?? '#563d7c';
        $etat->save();

        // On retourne l'objet créé pour que le JS puisse l'afficher directement
        return response()->json($etat);
    }

    public function updateEtat(Request $request, $id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $etat = Etat::find((int)$id);
        if (!$etat) {
            return response()->json(['error' => 'Etat not found'], 404);
        }

        $validated = $request->validate([
            'label' => 'required|string|max:191',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
        ]);

        $etat->label = $validated['label'];
        $etat->description = $validated['description'] ?? '';
        $etat->color = $validated['color'] ?? '#563d7c';
        $etat->save();

        return response()->json(['success' => 'Etat updated successfully.', 'etat' => $etat]);
    }

    public function deleteEtat($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $etat = Etat::find((int)$id);
        if (!$etat) {
            return response()->json(['error' => 'Etat not found'], 404);
        }

        $etat->delete();

        return response()->json(['success' => 'Etat deleted successfully.']);
    }

    public function createCamp(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'color' => 'nullable|string|max:7',
        ]);

        $camp = new Camp();
        $camp->name = $validated['name'];
        $camp->color = $validated['color'] ?? '#563d7c';
        $camp->save();

        // On retourne l'objet créé pour le JS
        return response()->json($camp);
    }

    public function updateCamp(Request $request, $id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $camp = Camp::find((int)$id);
        if (!$camp) {
            return response()->json(['error' => 'Camp not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'color' => 'nullable|string|max:7',
        ]);

        $camp->name = $validated['name'];
        $camp->color = $validated['color'] ?? '#563d7c';
        $camp->save();

        return response()->json(['success' => 'Camp updated successfully.', 'camp' => $camp]);
    }

    public function deleteCamp($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $camp = Camp::find((int)$id);
        if (!$camp) {
            return response()->json(['error' => 'Camp not found'], 404);
        }

        $camp->delete();

        return response()->json(['success' => 'Camp deleted successfully.']);
    }

    public function rolesIndex(Request $request)
    {
        $admin = false;
        $player = false;
        if (session()->has('authenticated') && session('authenticated')) {
            $admin = true;
        }
        else if (session()->has('player_token') && session('player_token')) {
            $player = true;
        }
        if (!$admin && !$player) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        return view('admin.roles', ['admin' => $admin, 'player' => $player]);
    }

    public function getRoleInfo($id)
    {
        $admin = false;
        $player = false;
        if (session()->has('authenticated') && session('authenticated')) {
            $admin = true;
        }
        else if (session()->has('player_token') && session('player_token')) {
            $player = true;
        }
        if (!$admin && !$player) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $role = Role::with('camps')->find((int)$id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        return response()->json($role, 200);
    }

    public function deleteRole($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $role = Role::find((int)$id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $role->delete();

        return response()->json(['success' => 'Role deleted successfully.']);
    }

    public function updateRole(Request $request, $id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $role = Role::find((int)$id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        $validated = $request->validate([
            'nom'         => 'required|string|max:191',
            'aura'        => 'required|string|max:191',
            'apparence'   => 'required|string|max:191',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:51200',
            'pouvoir'     => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $role->nom = $validated['nom'];
        $role->aura = $validated['aura'];
        $role->apparence = $validated['apparence'];
        $role->pouvoir = $validated['pouvoir'] ?? '';
        $role->description = $validated['description'] ?? '';

        // Gestion Image : Mise à jour directe
        if ($request->hasFile('image')) {
            // Suppression de l'ancienne image via le chemin physique complet
            if ($role->image_path && file_exists(public_path('storage/' . $role->image_path))) {
                unlink(public_path('storage/' . $role->image_path));
            }

            // Sauvegarde de la nouvelle
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/roles'), $filename);

            $role->image_path = 'roles/' . $filename;
        }

        $role->save();

        if ($request->has('camps')) {
            $campsInput = $request->input('camps');
            if (is_string($campsInput)) {
                $camps = explode(',', $campsInput);
            } else {
                $camps = $campsInput;
            }
            $camps = array_filter($camps, function($value) { return !empty($value); });

            $role->camps()->sync($camps);
        }

        return response()->json(['success' => 'Role updated successfully.', 'role' => $role]);
    }

    public function createRole(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'nom'         => 'required|string|max:191',
            'aura'        => 'required|string|max:191',
            'apparence'   => 'required|string|max:191',
            'pouvoir'     => 'nullable|string',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:51200',
        ]);

        $role = new Role();
        $role->nom = $validated['nom'];
        $role->aura = $validated['aura'];
        $role->apparence = $validated['apparence'];
        $role->pouvoir = $validated['pouvoir'] ?? '';
        $role->description = $validated['description'] ?? '';

        // Gestion Image : Enregistrement direct dans public/storage/roles
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Déplacement physique vers le dossier public
            $file->move(public_path('storage/roles'), $filename);

            // Le chemin reste relatif pour que asset() fonctionne
            $role->image_path = 'roles/' . $filename;
        }

        $role->save();

        if ($request->has('camps')) {
            $campsInput = $request->input('camps');
            if (is_string($campsInput)) {
                $camps = explode(',', $campsInput);
            } else {
                $camps = $campsInput;
            }
            $camps = array_filter($camps, function($value) { return !empty($value); });
            $role->camps()->sync($camps);
        }

        return response()->json(['success' => 'Role created successfully', 'role' => $role]);
    }

    public function createLiage(Request $request)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'first_game_id' => 'required|integer|exists:games,id',
            'second_game_id' => 'required|integer|exists:games,id',
        ]);

        if ($validated['first_game_id'] === $validated['second_game_id']) {
            return redirect()->back()->withErrors(['liage' => 'Cannot create a liage between the same game.']);
        }
        if (Liage::where('first_game_id', $validated['first_game_id'])->exists() ||
            Liage::where('second_game_id', $validated['second_game_id'])->exists() ||
            Liage::where('first_game_id', $validated['second_game_id'])->exists() ||
            Liage::where('second_game_id', $validated['first_game_id'])->exists()) {
            return redirect()->back()->withErrors(['liage' => 'One of the selected games is already part of another liage.']);
        }
        try {
            $liage = new Liage();
            $liage->name = $validated['name'];
            $liage->first_game_id = $validated['first_game_id'];
            $liage->second_game_id = $validated['second_game_id'];
            $liage->save();

            return redirect()->back()->with('success', 'Liage created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['liage' => 'An error occurred while creating the liage. Please try again.\n' . $e->getMessage()]);
        }
    }

    public function notesIndex()
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return redirect()->route('home')->withErrors(['login' => 'Please log in to access the admin dashboard.']);
        }

        $games = Game::all();
        return view('admin.notes', ['games' => $games]);
    }

    public function getNotes($id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $game = Game::find((int)$id);
        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        return response()->json(['notes' => $game->notes], 200);
    }

    public function updateNotes(Request $request, $id)
    {
        if (!session()->has('authenticated') || !session('authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $game = Game::find((int)$id);
        if (!$game) {
            return response()->json(['error' => 'Game not found'], 404);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $game->notes = $validated['notes'] ?? '';
        $game->save();

        return response()->json(['success' => 'Notes updated successfully.']);
    }
}
