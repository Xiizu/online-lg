<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['nom','token', 'role_id', 'game_id', 'is_alive', 'comment', 'camp_id', 'aura', 'apparence'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function camp()
    {
        return $this->belongsTo(Camp::class, 'camp_id');
    }

    public function etats()
    {
        return $this->belongsToMany(Etat::class, 'affecters', 'player_id', 'etat_id');
    }
}
