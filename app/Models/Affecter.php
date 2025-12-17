<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affecter extends Model
{
    protected $fillable = [
        'player_id',
        'etat_id',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function etat()
    {
        return $this->belongsTo(Etat::class);
    }
}
