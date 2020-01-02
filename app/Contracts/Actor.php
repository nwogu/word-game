<?php

namespace App\Contracts;

interface Actor
{
    /**
     * Converse
     * 
     * @return string
     */
    public function talk();

    /**
     * Should Talk
     * @param App\Models\Gamer $gamer
     * @param string $message
     * 
     * @return bool
     */
    public static function shouldTalk($gamer, $message);
}