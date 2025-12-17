<?php

use App\Models\Etat;

if (! function_exists('getColor')) {
    /**
     * Retourne la couleur (classe Tailwind ou hex) associée à un état.
     *
     * @param  int|string|Etat|null  $etat
     * @param  string  $default  Valeur par défaut si non trouvé
     * @return string
     */
    function getColor($etat, string $default = 'gray-500'): string
    {
        // Supporte différents types d'argument
        $etatModel = null;

        if ($etat instanceof Etat) {
            $etatModel = $etat;
        } elseif (is_numeric($etat)) {
            $etatModel = Etat::query()->find($etat);
        } elseif (is_string($etat) && $etat !== '') {
            $etatModel = Etat::query()->where('code', $etat)->orWhere('name', $etat)->first();
        }

        // Mappe une propriété vers une couleur; ajustez selon votre schéma
        if ($etatModel) {
            // Essayez d'utiliser une colonne dédiée si elle existe
            if (isset($etatModel->color) && is_string($etatModel->color) && $etatModel->color !== '') {
                return $etatModel->color;
            }

            // Exemple de mapping basique basé sur un code ou nom
            $key = strtolower($etatModel->code ?? $etatModel->name ?? '');
            return match ($key) {
                'actif', 'active' => 'green-600',
                'inactif', 'inactive' => 'gray-500',
                'danger', 'alerte' => 'red-600',
                'info' => 'blue-600',
                'warning', 'avertissement' => 'yellow-600',
                default => $default,
            };
        }

        return $default;
    }
}
