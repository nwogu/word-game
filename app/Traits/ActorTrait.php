<?php

namespace App\Traits;

use App\Actors\PlayKeywordActor;
use App\Actors\StopKeywordActor;

trait ActorTrait
{

    /**
     * Save the game data for gamer
     */
    protected function saveGame()
    {
        $gamePoints = $this->gamer->game->points;
        $gameAttempts = $this->gamer->game->attempts;

        $this->gamer->points += $gamePoints;
        $this->gamer->level++;

        $this->gamer->save();
        $this->gamer->game()->delete();

        return [$gamePoints, $gameAttempts];
    }

    /**
     * Print Puzzle
     */
    protected function printPuzzle($puzzle, $shuffled)
    {
        return "\n{$puzzle}\n\n" . strtoupper($shuffled);
    }
}