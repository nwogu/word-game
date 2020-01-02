<?php

namespace App\Helpers;

use App\Factories\PuzzleFactory;

class PuzzleResolver {

    protected $finalgrid;
    
    private function get($r, $c, $grid, $h) {
        if ($h) {
            return $grid[$r][$c];
        }
        return $grid[$c][$r];
    }
    
    private function set($v, $r, $c, &$grid, $h) {
        if ($h) {
            $grid[$r][$c] = $v;
        } else {
            $grid[$c][$r] = $v;
        }
    }
    
    private function placeWord($word, $grid, $h) {
        $results = [];
        $l = strlen($word);
        
        for ($r = 0; $r < 10; $r++) {
            $c = 0;
            while ($c < 10) {
                $k = 0;
                $seenMinus = false;
                while (
                    $c < 10 &&
                    $k < $l && 
                    ($word[$k] == $this->get($r, $c, $grid, $h) || $this->get($r, $c, $grid, $h) == PuzzleFactory::MISSING_FILLER)) {
                    if ($this->get($r, $c, $grid, $h) == PuzzleFactory::MISSING_FILLER) {
                        $seenMinus = true;
                    }
                    $c++;
                    $k++;
                }
                
                if (
                    $k == $l 
                    && $seenMinus 
                    && ($c == 10 || $this->get($r, $c, $grid, $h) == PuzzleFactory::GRID_FILLER)
                    && ($c - $l == 0 || $this->get($r, $c - $l - 1, $grid, $h) == PuzzleFactory::GRID_FILLER)
                ) {
                    $newGrid = $grid;
                    while ($k > 0) {
                        $this->set($word[$l - $k], $r, $c - $k, $newGrid, $h);
                        $k--;
                    }
                    $results[] = $newGrid;
                }

                $c++;
            }
        }
        
        return $results;
    }
    
    public function solve($k, $n, $words, $grid) {

        if ($k == $n) {
            $i = 0;
            while ($i < 10) {
                $this->finalgrid[] =  $grid[$i];
                $i++;
            }
        }

        
        $candidates = $this->placeWord($words[$k] ?? "", $grid, true);

        foreach ($candidates as $c) {
            $this->solve($k + 1, $n, $words, $c);
        }
        $candidates = $this->placeWord($words[$k] ?? "", $grid, false);

        foreach ($candidates as $c) {
            $this->solve($k + 1, $n, $words, $c);
        }
        return $this->finalgrid;
    }
    
}