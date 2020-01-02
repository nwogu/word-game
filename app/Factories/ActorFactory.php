<?php

namespace App\Factories;

use App\Models\Gamer;
use Illuminate\Support\Facades\Request;

class ActorFactory
{

    /**
     * Actor
     * @var App\Contracts\Actor;
     */
    protected $actor;

    /**
     * Gamer
     * @var App\Models\Gamer
     */
    protected $gamer;

    /**
     * Construct
     */
    public function __construct($phoneNumber = null, $message = null)
    {
        $this->resolveGamer($phoneNumber);
        $this->actor = $this->resolveActor($message);
    }

    /**
     * Make Actor
     */
    public static function make()
    {
        $self = new static(
            Request::get("From"), 
            Request::get("Body"));

        return $self->actor;
    }

    /**
     * Resolve Gamer
     * @param $gamer
     * @return void
     */
    protected function resolveGamer($phoneNumber)
    {
        $this->gamer = Gamer::firstOrCreate([
            'phone_number' => $phoneNumber
        ]);
    }

    /**
     * Resolve Actor
     * @param $message
     * 
     * @return App\Contracts\Actor
     */
    protected function resolveActor($message)
    {
        $message = $this->normalizeMessage($message);

        $actors = $this->getActors();

        foreach ($actors as $actor) {

            if ($actor::shouldTalk($this->gamer, $message)) 
                return new $actor($this->gamer, $message);
        }
        return new \App\Actors\SaluteActor($this->gamer, $message);
    }

    /**
     * Trim and lower case the message
     */
    protected function normalizeMessage($message)
    {
        return trim(strtolower($message));
    }

    /**
     * Get available actors
     * @return array
     */
    protected function getActors()
    {
        return config("wordgame.actors");
    }
}