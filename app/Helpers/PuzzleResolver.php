<?php

namespace App\Helpers;

use App\Factories\PuzzleFactory;

class PuzzleResolver
{
    protected $grid = [];
    protected $questionGrid = [];
    protected $mappings = [];
    protected $axisMappings = [];
    protected $words = [];

    public function __construct($words, $crossword, $questionGrid)
    {
        $this->words = $words;
        $this->mappings = $this->getLetterMappings($crossword);
        $this->axisMappings = $this->getAxisMappings();
        $this->questionGrid = $questionGrid;
    }
    public function getLetterMappings($crossword)
    {
        foreach ($crossword as $row => $columns) {
            foreach ($columns as $column => $letter) {
                $mappings[$letter][] = "{$row}|{$column}";
            }
        }
        return $mappings;
    }

    public function getAxisMappings() {
        foreach ($this->mappings as $letter => $axis) {
            foreach ($axis as $xy) {
                $axisMapping[$xy] = $letter;
            }
        }
        return $axisMapping;
    }

    public function searchHorizontalAxisForWord($word) {
        $letter = str_split($word)[0];
        $mappingsForWord = $this->mappings[$letter];
        foreach ($mappingsForWord as $axis) {
            $axisMatched = [];
            if ($getHorizontalRange = $this->getHorizontalRange($axis, $word)) {
                foreach ($getHorizontalRange as $horizontalRange => $rangeLetter) {
                    $axisMatched[] = ($rangeLetter == ($this->axisMappings[$horizontalRange] ?? ""));
                }
                if ((count(array_filter($axisMatched)) == count($getHorizontalRange)) 
                    && (!$this->hasPreHorizontalRange($getHorizontalRange)) 
                    && (!$this->hasPostHorizontalRange($getHorizontalRange))) {
                    return $getHorizontalRange;
                }
            }
        }
        return [];
    }

    public function searchVerticalAxisForWord($word) {
        $letter = str_split($word)[0];
        $mappingsForWord = $this->mappings[$letter];
        foreach ($mappingsForWord as $axis) {
            $axisMatched = [];
            if ($getVerticalRange = $this->getVerticalRange($axis, $word)) {
                foreach ($getVerticalRange as $verticalRange => $rangeLetter) {
                    $axisMatched[] = ($rangeLetter == ($this->axisMappings[$verticalRange] ?? ""));
                }
                if ((count(array_filter($axisMatched)) == count($getVerticalRange)) 
                    && (!$this->hasPreVerticalRange($getVerticalRange)) 
                    && (!$this->hasPostVerticalRange($getVerticalRange))) {
                    return $getVerticalRange;
                }
            }
        }
        return [];
    }

    public function hasPreVerticalRange($verticalRange)
    {
        $verticalRange = array_keys($verticalRange);
        $endAxis = reset($verticalRange);
        list($row, $column) = explode("|", $endAxis);
        if ($row == "1") return false;

        $axisMappings = array_keys($this->axisMappings);
        $previousRowIndex = $row - 1;

        return in_array("$previousRowIndex|$column", $axisMappings);
    }

    public function hasPreHorizontalRange($horizontalRange)
    {
        $horizontalRange = array_keys($horizontalRange);
        $endAxis = reset($horizontalRange);
        list($row, $column) = explode("|", $endAxis);
        if ($column == "1") return false;

        $axisMappings = array_keys($this->axisMappings);
        $previousColumnIndex = $column - 1;

        return in_array("$row|$previousColumnIndex", $axisMappings);
    }

    public function hasPostVerticalRange($verticalRange)
    {
        $verticalRange = array_keys($verticalRange);
        $startAxis = end($verticalRange);
        list($row, $column) = explode("|", $startAxis);
        if ($row == "10") return false;

        $axisMappings = array_keys($this->axisMappings);
        $nextRowIndex = $row + 1;

        return in_array("$nextRowIndex|$column", $axisMappings);
    }

    public function hasPostHorizontalRange($horizontalRange)
    {
        $horizontalRange = array_keys($horizontalRange);
        $startAxis = end($horizontalRange);
        list($row, $column) = explode("|", $startAxis);
        if ($column == "10") return false;

        $axisMappings = array_keys($this->axisMappings);
        $nextColumnIndex = $column + 1;

        return in_array("$row|$nextColumnIndex", $axisMappings);
    }

    public function getHorizontalRange($axis, $word) {
        $letters = str_split($word);
        $wordLength = count($letters);
        list($row, $column) = explode("|", $axis);
        if ($column == "10") return [];
        $lastColumn = $column + ($wordLength - 1);
        if ($lastColumn > 10) return [];
        $range = range($column, $lastColumn);

        $index = 0;
        foreach ($range as $_column) {
            $axises["{$row}|{$_column}"] = $letters[$index];
            $index++;
        }

        return $axises;

    }

    public function getVerticalRange($axis, $word) {
        $letters = str_split($word);
        $wordLength = count($letters);
        list($row, $column) = explode("|", $axis);
        if ($row == "10") return [];
        $lastRow = $row + ($wordLength - 1);
        if ($lastRow > 10) return [];
        $range = range($row, $lastRow);

        $index = 0;
        foreach ($range as $_row) {
            $axises["{$_row}|{$column}"] = $letters[$index];
            $index++;
        }

        return $axises;

    }

    public function getGrid($axises)
    {
        foreach($axises as $axis => $letter) {
            list($row, $column) = explode("|", $axis);
            $this->grid[$row][$column] = $letter;
        }
    }

    public function solve()
    {
        foreach ($this->words as $word) {
            if (!$foundAxis = $this->searchHorizontalAxisForWord($word)) {
                $foundAxis = $this->searchVerticalAxisForWord($word);
            }
            $this->getGrid($foundAxis);
        }

        return $this->flatGrid($this->fillGrid());
    }

    protected function fillGrid()
    {
        for ($i = 1; $i <= 10; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                $this->questionGrid[$i][$j] = $this->grid[$i][$j] 
                    ?? $this->questionGrid[$i][$j];
            }
        }
        return $this->questionGrid;
    }

    protected function flatGrid($grids)
    {
        foreach ($grids as $grid) {
            $flatGrid[] = implode("", $grid);
        }
        return $flatGrid;
    }
}