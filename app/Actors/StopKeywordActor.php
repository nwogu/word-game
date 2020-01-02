<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Traits\ActorTrait;
use App\Constants\Keywords;
use App\Constants\Conversations;

class StopKeywordActor extends Actor
{
    use ActorTrait;
    
    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return (bool)$gamer->game && Keywords::STOP == trim(strtolower($message));
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        list($gamePoints, $gameAttempts) = $this->saveGame();
        return $this->buildConvo($gamePoints, $gameAttempts);
    }

    /**
     * Return conversation after ending game
     */
    protected function buildConvo($gamePoints, $gameAttempts)
    {
        $convo = "Game Over!";
        if ($gamePoints > 150 && $gamePoints < 300) {
            $convo .= "Well done {$this->gamer->name}! You played well. You got a total of $gamePoints points, It's not too late for English lessons!";
        } elseif ($gamePoints > 300) {
            $convo .= "Brilliant Performance {$this->gamer->name}! You played well. You got a total of $gamePoints points, with $gameAttempts attempts. You're a legend!";
        } else {
            $convo .= "Poor Performance {$this->gamer->name}! Your English teacher would be disappointed. You got a total of $gamePoints points, with $gameAttempts correct attempts. English Olodo!";
        }
        $start = Keywords::START;
        return $convo . "\n Say *{$start}*, to start again";
    }


}