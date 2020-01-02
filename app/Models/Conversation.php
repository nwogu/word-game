<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gamer_id', 'actor', 'refering_actor'
    ];

    /**
     * Gamer
     */
    public function gamer()
    {
        return $this->belongsTo(Gamer::class);
    }
}
