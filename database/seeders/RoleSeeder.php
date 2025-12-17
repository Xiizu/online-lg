<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Camp;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Définition du chemin du fichier JSON
        $path = database_path('data/roles.json');

        // 2. Vérification de l'existence du fichier
        if (!File::exists($path)) {
            $this->command->error("Erreur : Le fichier $path n'existe pas.");
            return;
        }

        // 3. Lecture et décodage du fichier
        $json = File::get($path);
        $data = json_decode($json, true);

        // 4. Vérification si le JSON est valide
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Erreur JSON : ' . json_last_error_msg());
            return;
        }

        // 5. Boucle pour l'insertion et la gestion des relations Many-to-Many
        foreach ($data as $item) {
            // Extraction de la chaîne des camps (ex: "Village" ou "Village/Loup-Garou")
            $campsString = $item['camp'] ?? null;

            // On retire la clé "camp" du tableau car elle ne correspond plus à une colonne simple de la table 'roles'
            unset($item['camp']);

            // Création ou récupération du Rôle s'il n'existe pas (basé sur le nom)
            // Eloquent gère automatiquement created_at et updated_at
            $role = Role::firstOrCreate(
                ['nom' => $item['nom']], // Recherche par nom
                $item // Données à insérer si le rôle n'existe pas
            );

            // Gestion des camps multiples (Many-to-Many)
            if ($campsString) {
                // Découpage de la chaîne par "/" pour avoir chaque camp séparé
                $campNames = explode('/', $campsString);

                foreach ($campNames as $campName) {
                    $campName = trim($campName);
                    if (empty($campName)) continue;

                    // Création ou récupération du Camp dans la table 'camps'
                    $camp = Camp::firstOrCreate(
                        ['name' => $campName],
                        ['color' => '#' . substr(md5($campName), 0, 6)] // Génère une couleur basée sur le nom
                    );

                    // Vérification si la liaison existe déjà pour éviter les doublons
                    $exists = DB::table('camp_role')
                        ->where('role_id', $role->id)
                        ->where('camp_id', $camp->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('camp_role')->insert([
                            'role_id' => $role->id,
                            'camp_id' => $camp->id,
                        ]);
                    }
                }
            }
        }

        $this->command->info(count($data) . ' rôles importés/vérifiés et liés à leurs camps avec succès !');
    }
}
