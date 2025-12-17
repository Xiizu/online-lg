<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['nom', 'aura', "apparence", "pouvoir", "description", "image_path"];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function camps()
    {
        return $this->belongsToMany(Camp::class);
    }

}
