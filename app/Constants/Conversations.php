<?php

namespace App\Constants;

class Conversations
{
    const SALUTE = "Hey name_key! extra_greet_key If you're familiar with the rules, just say *start_key* to begin, else say *rule_key*. Let's play game_key";

    const RULES = "There are some missing words, represented by *missing_filler_key*. I'll give you some scrambled letters. Form a new word with each letter to earn points_key points. \nKeywords: \n1. Say *start_key* to start the game\n2. Say *stop_key* to stop the game\n3. Say *name_key* to tell me your name\n4. Say *pnts_key* to see your total points\n5. Say *rule_key* to see this rules again";

    const POINTS = "You have points_key overall points! Answer more questions correctly to increase your points.";

    const NAME = "Ok, so tell me, what shall I call you?";

    const EXTRA_GEETING = "Welcome. So you think you're smart right? How good is your vocabulary?";
}