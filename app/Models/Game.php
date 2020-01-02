<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'gamer_id', 'question', 'answer',
        'points', 'attempts'
    ];

    /**
     * Cast Datatypes
     */
    protected $casts = [
        "answer" => "array",
        "question" => "array"
    ];

    /**
     * Gamer
     */
    public function gamer()
    {
        return $this->belongsTo(Gamer::class);
    }
}
