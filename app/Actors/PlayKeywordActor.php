<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Traits\ActorTrait;
use App\Constants\Keywords;
use App\Factories\QuestionFactory;

class PlayKeywordActor extends Actor
{
    use ActorTrait;
    
    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return $gamer->game == null && Keywords::START == trim(strtolower($message));
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        if ($this->gamer->name == null) {
            return $this->processName();
        }

        return $this->processConvo(QuestionFactory::make()->generate());
    }

    /**
     * Process Conversation
     */
    private function processConvo($question)
    {
        $puzzle = join("\n", $question["puzzle"]);

        $this->gamer->game()->updateOrCreate(['gamer_id' => $this->gamer->id],[
            "question" => $question]);

        return $this->printPuzzle($puzzle, $question["shuffled"]);
    }

    /**
     * Process Name
     */
    private function processName()
    {
        return $this->call(NameKeywordActor::class, static::class);
    }
}