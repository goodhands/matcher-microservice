<?php

namespace App\Repository;

class SearchProfileRepository
{
    private int $strictmatches = 0;
    private int $loosematches = 0;
    private int $score = 0;
    private array $checked = array();

    public const STRICTSCORE = 33;
    public const LOOSESCORE = 10;
    public const DEFAULTSCORE = 2;

    public function addChecked($item)
    {
        $this->checked[] = $item;
    }

    public function getCheckedItems()
    {
        return $this->checked;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function getLooseMatches()
    {
        return $this->loosematches;
    }

    public function getStrictMatches()
    {
        return $this->strictmatches;
    }

    /**
     * This function accepts an array with 2 items
     * and compares their value with $compare based
     * on which of the items are null.
     *
     * @param array $range The array to compare against
     * @param string $compare The compare value
     */
    public function checkNullableRange(array $range, $compare, $key)
    {
        if (!in_array(null, $range) || in_array($key, $this->checked)) {
            return;
        }

        list($firstvalue, $secondvalue) = $range;

        if ($secondvalue === null && $compare >= $firstvalue) {
            $this->addScore('strict');
        } elseif ($firstvalue === null && $compare <= $secondvalue) {
            $this->addScore('strict');
        }
    }

    public function checkRange(array $range, $compare, $key)
    {
        if (in_array(null, $range) || in_array($key, $this->checked)) {
            return;
        }

        list($min, $max) = [min($range), max($range)];

        if ($compare <= $max && $compare >= $min) {
            $this->addScore('strict');
        }
    }

    public function checkDeviationRange(array $range, $compare, $key)
    {
        if (in_array(null, $range) || in_array($key, $this->checked)) {
            return;
        }

        $twentyfivepercent = 25 / 100;

        list($min, $max) = [min($range), max($range)];

        $twentyfivepercentmax = $twentyfivepercent * $max;

        $max = $twentyfivepercentmax + $max;

        $twentyfivepercentmin = $twentyfivepercent * $min;

        $min = $twentyfivepercentmin + $min;

        if ($compare <= $max && $compare >= $min) {
            // If there is a match now, that's a loose match
            // Reward a lower score
            $this->addScore('loose');
        }
    }

    /**
     * Resets the values after each iteration
     */
    public function resetCounts()
    {
        $this->loosematches = 0;
        $this->strictmatches = 0;
        $this->score = 0;
        $this->checked = array();
    }

    /**
     * Increments the score for the current result
     * depending on the $type supplied
     *
     * @param string $type One of strict, loose or empty
     */
    public function addScore($type)
    {
        switch ($type) {
            case 'strict':
                $this->score += self::STRICTSCORE;
                $this->strictmatches++;
                break;
            case 'loose':
                $this->score += self::LOOSESCORE;
                $this->loosematches++;
                break;
            default:
                $this->score += self::DEFAULTSCORE;
                $this->loosematches++;
                break;
        }
    }
}