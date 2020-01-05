<?php

namespace App\Actors;

use App\Actors\Actor;
use App\Traits\ActorTrait;
use App\Helpers\PuzzleResolver;

class GameActor extends Actor
{
    use ActorTrait;

    const ANSWERED_PREVIOUSLY = 100;
    const ANSWERED_CORRECTLY = 200;
    const ANSWERED_WRONGLY = 300;

    protected $successMessages = [
        "You’re the most brilliant person I know. Well done.",
        "Good one mate. Bravo",
        "You're becoming a legend.",
        "You did it again. Amazing!",
        "You're making me cry. OMG!!!",
        "I'm impressed. You did it",
        "You're the bomb. You're doing great",
        "Way to go. You're a genius"
    ];

    /**
     * should talk
     */
    public static function shouldTalk($gamer, $message)
    {
        return (bool)$gamer->game;
    }

     /**
     * Converse
     * @return string
     */
    public function talk()
    {
        $answerScenario = $this->checkAnswer();

        switch ($answerScenario) {
            case self::ANSWERED_PREVIOUSLY:
                return $this->answeredPreviouslyActivity();
            case self::ANSWERED_WRONGLY:
                return $this->wrongAnswerActivity();
            case self::ANSWERED_CORRECTLY:
                return $this->correctAnswerActivity();
            default:
                return "I don't know what to do";
        }
    }

    /**
     * Check Answer
     */
    protected function checkAnswer()
    {
        if (in_array($this->message, $this->gamer->game->answer ?? [])) {
            return self::ANSWERED_PREVIOUSLY;
        }
        if (in_array($this->message, $this->gamer->game->question["missings"])) {
            return self::ANSWERED_CORRECTLY;
        }
        return self::ANSWERED_WRONGLY;
    }

    /**
     * Perfom if gamer answers correctly
     */
    protected function correctAnswerActivity()
    {
        $this->givePoints();
        return $this->successMessages[rand(0, count($this->successMessages) - 1)]
                . " You're on {$this->gamer->game->points} points" . $this->dropQuestion();
    }

    /**
     * Perform if gamer has answers previously
     */
    protected function answeredPreviouslyActivity()
    {
        return $this->repeatQuestion();
    }

    /**
     * Generate next question
     */
    protected function dropQuestion()
    {
        if ($this->shouldMoveLevel()) {
            $this->saveGame();

            $level = $this->gamer->level + 1;
            return "*** Level {$level} ***" . "\n\n" .
            $this->call(PlayKeywordActor::class);
        }

        return $this->repeatQuestion();
    }

    /**
     * Checks if Questions Should move to another level
     * @return bool
     */
    protected function shouldMoveLevel()
    {
        return count($this->gamer->game->answer ?? []) 
        >= count($this->gamer->game->question["missings"]);
    }

    /**
     * Give points to gamer
     */
    protected function givePoints()
    {
        $game = $this->gamer->game;
        $answer = $game->answer ?? [];

        $game->points += config("wordgame.points");
        $answer[] = $this->message;
        $game->attempts = count($answer);
        $game->answer = $answer;
        $this->gamer->push();
    }

    /**
     * Perfom if gamer answers wrongly
     */
    protected function wrongAnswerActivity()
    {
        return $this->repeatQuestion();
    }

    /**
     * Repeat Question with already submitted answers
     */
    protected function repeatQuestion()
    {
        $question = $this->gamer->game->question;
        $words = $this->gamer->game->answer ?? [];
        $words = array_map("strtoupper", $words);

        $puzzle = $question["puzzle"];

        if (empty($words)) {
            $resolvedPuzzle = $puzzle;
        } else {
            $puzzleResolver = new PuzzleResolver(
                $words, 
                $question["crossword"], 
                $question["grid"]
            );
            $resolvedPuzzle = $puzzleResolver->solve();
        }
        
        $resolvedPuzzle = join("\n", $resolvedPuzzle);

        return "\n\n" . $this->printPuzzle(
            $resolvedPuzzle, $question["shuffled"]
        );
    }
}