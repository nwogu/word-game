<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gamer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone_number',
        'points', 'level'
    ];

    /**
     * Gamer's Game
     */
    public function game()
    {
        return $this->hasOne(Game::class);
    }

    /**
     * Gamer's Conversation
     */
    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }
}
