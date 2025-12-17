<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Liage;

class Game extends Model
{
    protected $fillable = ['status', 'name', 'date', 'notes'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function isSingleGame()
    {
        return !Liage::where('first_game_id', $this->id)
                     ->orWhere('second_game_id', $this->id)
                     ->exists();
    }
}
