<?php

namespace App\Actors;

use App\Contracts\Actor as ActorContract;

abstract class Actor implements ActorContract
{
    /**
     * @var Gamer
     */
    protected $gamer;

    /**
     * @var string
     */
    protected $message;

    public function __construct($gamer, $message)
    {
        $this->gamer = $gamer;
        $this->message = $message;
    }

    /**
     * Call Actor from within an actor
     * @param string $actor
     * @param mixed $data
     * @return string $convo
     */
    protected function call($actor, $data = null)
    {
        $actor = new $actor($this->gamer, $this->message);
        return $actor->talk($data);
    }
}