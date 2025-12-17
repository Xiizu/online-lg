<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use App\Models\Etat;

class EtatSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/etats.json');

        if (!File::exists($path)) {
            $this->command?->error("Erreur : Le fichier $path n'existe pas.");
            return;
        }

        $json = File::get($path);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command?->error('Erreur JSON : ' . json_last_error_msg());
            return;
        }

        foreach ($data as $item) {
            Etat::updateOrCreate(
                ['label' => $item['nom']],
                [
                    'label'       => $item['nom'],
                    'description' => $item['description'] ?? null,
                    'color'       => $item['color'] ?? '#999999',
                ]
            );
        }

        $this->command?->info(count($data) . ' états importés/vérifiés.');
    }
}
