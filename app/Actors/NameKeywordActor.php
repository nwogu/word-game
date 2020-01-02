<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Constants\Keywords;
use App\Constants\Conversations;

class NameKeywordActor extends Actor
{

    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return Keywords::NAME == trim(strtolower($message));
    }

     /**
     * Converse
     * @return string
     */
    public function talk($data = null)
    {
        $this->gamer->conversation()->updateOrcreate(["gamer_id" => $this->gamer->id],
        ['actor' => static::class, 'refering_actor' => $data]);

        return Conversations::NAME;
    }
}