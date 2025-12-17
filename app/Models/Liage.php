<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Liage extends Model
{
    protected $fillable = [
        'name',
        'first_game_id',
        'second_game_id',
    ];

    public function firstGame()
    {
        return $this->belongsTo(Game::class, 'first_game_id');
    }

    public function secondGame()
    {
        return $this->belongsTo(Game::class, 'second_game_id');
    }
}
