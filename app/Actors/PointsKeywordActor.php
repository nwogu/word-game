<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Constants\Conversations;
use App\Constants\Keywords;

class PointsKeywordActor extends Actor
{

    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return Keywords::POINTS == trim(strtolower($message));
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        $conversation = Conversations::POINTS;

        foreach ($this->buildConvo() as $key => $value) {
            $conversation = str_replace($key, $value, $conversation);
        }
        return $conversation;
    }

    /**
     * Build Convo
     */
    protected function buildConvo()
    {
        $points_key = $this->gamer->game == null 
                ? $this->gamer->points 
                : $this->gamer->game->points + $this->gamer->points;

        return compact('points_key');
    }
}