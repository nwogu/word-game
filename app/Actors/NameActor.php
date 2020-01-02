<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Constants\Keywords;
use App\Actors\NameKeywordActor;
use App\Actors\PlayKeywordActor;
use App\Constants\Conversations;

class NameActor extends Actor
{

    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return $gamer->conversation && 
                $gamer->conversation->actor == NameKeywordActor::class;
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        $this->gamer->update(["name" => $this->message]);

        $convo =  "Nice name you've got, $this->message. Easy to remember. 
            You can change it at anytime using the keyword: *" . Keywords::NAME . "*";

        if ($this->gamer->conversation->refering_actor == PlayKeywordActor::class) {
            $convo =  $convo . "\n\n" . $this->call(PlayKeyWordActor::class);
        }

        $this->gamer->conversation()->delete();
        return $convo;
    }
}