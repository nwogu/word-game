<?php

namespace App\Factories;

class PuzzleFactory
{
    /**
     * @var const
     */
    const GRID_LENGTH = 10;

    /**
     * @var const
     */
    const MISSING_FILLER = "@";

    /**
     * @var const
     */
    const GRID_FILLER = " ";

    /**
     * @param array $words
     */
    public function __construct(array $words)
    {
        $this->words = $words;
        
    }

    public static function make(array $missings)
    {
        $self = new static($missings);
        return $self->resolve();
    }

    /**
     * Resolve factory
     */
    protected function resolve()
    {
        $crossWordGenerator = new \Crossword\Crossword(
            self::GRID_LENGTH,
            self::GRID_LENGTH,
            $this->words
        );

        $crossWordGenerator->generate(
            \Crossword\Generate\Generate::TYPE_RANDOM, true
        );

        $crossword = $crossWordGenerator->toArray();
        $grid = $this->convertToGrid($crossword);

        $newgrid = $this->replaceGridWithFillers($grid, $filledGrid);
        return [$filledGrid, $crossword, $newgrid];
    }

    /**
     * Convert Crossword array to grid array
     * @param array
     * 
     * @return array
     */
    protected function convertToGrid(array $crossword)
    {
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                $grid[$i][$j] = $crossword[$i][$j] 
                    ?? self::GRID_FILLER;
            }
        }
        return $grid;
    }

    /**
     * Replace grid with missing fillers
     * @param array
     * 
     * @return array
     */
    protected function replaceGridWithFillers(array $grid, &$filledGrid)
    {
        foreach ($grid as &$block) {
            foreach ($block as &$replaceable) {
                $replaceable = $replaceable == self::GRID_FILLER 
                ? self::GRID_FILLER : self::MISSING_FILLER;
            }
            $filledGrid[] = implode("", $block);
        }
        return $grid;
    }
}