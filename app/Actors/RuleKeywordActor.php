<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Constants\Keywords;
use App\Constants\Conversations;
use App\Factories\PuzzleFactory;

class RuleKeywordActor extends Actor
{

    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return Keywords::RULE == trim(strtolower($message));
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        $conversation = Conversations::RULES;

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
        $start_key = Keywords::START;
        $rule_key = Keywords::RULE;
        $stop_key = Keywords::STOP;
        $pnts_key = Keywords::POINTS;
        $name_key = Keywords::NAME;
        $missing_filler_key = PuzzleFactory::MISSING_FILLER;
        $points_key = config("wordgame.points");

        return compact(
            'start_key', 
            'rule_key', 
            'points_key',
            'stop_key',
            'pnts_key',
            'name_key',
            'missing_filler_key'
        );
    }
}