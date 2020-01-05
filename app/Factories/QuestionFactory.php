<?php

namespace App\Factories;

use App\Constants\Keywords;
use Illuminate\Support\Str;

class QuestionFactory
{
    protected $dictionary;

    const MAX_WORDS = 999;

    const MAX_ANSWER = 6;

    const MAX_MISSING_WORD = 5;

    const MIN_MISSING_CHARACTER = 3;

    const MIN_MISSING_WORD = 2;

    const MAX_PERMUTATION = 50000;

    /**
     * construct
     */
    private function __construct()
    {
        $this->loadDictionary();
    }

    /**
     * Words
     */
    private function loadDictionary()
    {
        $this->dictionary = include_once(base_path("dictionary.php"));
    }

    /**
     * Spit Random Word
     * @return string
     */
    protected function spitWord(int $index)
    {
        return $this->dictionary[$index];
    }

    /**
     * Spit Index
     * @return int
     */
    protected function spitIndex()
    {
        return mt_rand(0, self::MAX_WORDS);
    }

    /**
     * Spit Random Words
     * @return array
     */
    protected function spitWords(int $numberOfWordsToSpit)
    {
        $wordRange = range(1, $numberOfWordsToSpit);
        foreach ($wordRange as $index) {
            $words[] = $this->spitWord($this->spitIndex());
        }
        return count($words) > 1 ? $words : $words[0];
    }

    protected function isValidWord($word)
    {
        return in_array($word, $this->dictionary);
    }

    /**
     * Make factory
     */
    public static function make()
    {
        $self = new static();
        return $self;
    }

    /**
     * Get permutations
     */
    protected function permutations($word)
    {
        $string = implode('', array_unique(str_split($word)));

        $i=0; while ($i++ < self::MAX_PERMUTATION) {

            $index = substr(str_shuffle($string),0,mt_rand(1,strlen($string)));
            $coll[$index] = true;
        }

        ksort($coll);

        return array_filter(array_keys($coll), function($wor) {
            return strlen($wor) >= self::MIN_MISSING_CHARACTER && $this->isValidWord($wor);
        });
    }

    /**
     * Generate
     */
    public function generate()
    {
        do {
            $answerable = $this->spitWords(1);
        } while ($this->invalidAnswer($answerable));

        $missings =  $this->permutations($answerable);
        $missingsCount = count($missings);

        if ($this->shouldRegenerate($missingsCount, $missings)) {
            return $this->generate();
        }

        $missings = array_values($missings);

        if ($this->shouldReduceMissings($missingsCount)) {
            $missings = $this->reduceMissings($missingsCount, $missings);
        }
        
        $missings = array_values(
            array_unique(array_merge([$answerable], $missings))
        );

        list($puzzle, $crossword, $grid) = PuzzleFactory::make($missings);

        return [
            "shuffled" => str_shuffle($answerable),
            "missings" => $missings,
            "puzzle" => $puzzle,
            "crossword" => $crossword,
            "grid" => $grid
        ];
    }

    /**
     * Checks if a question should be regenerated
     * @return bool
     */
    protected function shouldRegenerate($missingsCount, $missings)
    {
        return $missingsCount < self::MIN_MISSING_WORD || $missings == null;
    }

    /**
     * Checks if to reduce the generated questions
     * @return bool
     */
    protected function shouldReduceMissings($missingsCount)
    {
        return $missingsCount > self::MAX_MISSING_WORD;
    }

    /**
     * Reduce generated missings
     */
    protected function reduceMissings($missingsCount, $missings)
    {
        $startIndex = $missingsCount - 1;
        $stopIndex = $missingsCount - self::MAX_MISSING_WORD - 1;

        for ($i = $startIndex; $i > $stopIndex; $i--) {
            $reducedMissings[$i] = $missings[$i];
        }

        return $reducedMissings;
    }

    /**
     * Checks if answer fails any condition and regenerate new answer
     * @param string $answer
     * @return bool
     */
    protected function invalidAnswer($answerable)
    {
        return strlen($answerable) != self::MAX_ANSWER ?:
                 $this->isKeyWord($answerable);
    }

    /**
     * Is Keyword
     */
    protected function isKeyWord($answerable)
    {
        return in_array($answerable, $this->keywords());
    }

    /**
     * Get Key Words
     * @return array
     */
    protected function keywords()
    {
        $keywordClass = new \ReflectionClass(Keywords::class);
        return array_values($keywordClass->getConstants());
    }

}