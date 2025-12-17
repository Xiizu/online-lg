<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camp extends Model
{
    protected $fillable = ['name', 'color'];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
