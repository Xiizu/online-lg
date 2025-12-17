<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etat extends Model
{
    protected $fillable = ['label', 'description', 'color'];

    public function players()
    {
        return $this->belongsToMany(Player::class);
    }

}
