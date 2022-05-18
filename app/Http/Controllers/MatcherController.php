<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchProfileResource;
use App\Models\Property;
use App\Models\SearchProfile;
use Illuminate\Http\Request;

class MatcherController extends Controller
{
    private int $strictmatches = 0;
    private int $loosematches = 0;
    private int $score = 0;
    private array $checked = array();

    private const STRICTSCORE = 33;
    private const LOOSESCORE = 10;
    private const DEFAULTSCORE = 2;

    /**
     * This function accepts an array with 2 items
     * and compares their value with $compare based
     * on which of the items are null.
     *
     * @param array $range The array to compare against
     * @param string $compare The compare value
     */
    private function checkNullableRange(array $range, $compare, $key)
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

    private function checkRange(array $range, $compare, $key)
    {
        if (in_array(null, $range) || in_array($key, $this->checked)) {
            return;
        }

        list($min, $max) = [min($range), max($range)];

        if ($compare <= $max && $compare >= $min) {
            $this->addScore('strict');
        }
    }

    private function checkDeviationRange(array $range, $compare, $key)
    {
        if (in_array(null, $range) || in_array($key, $this->checked)) {
            return;
        }

        $twentyfivepercent = 25 / 100;

        list($min, $max) = [min($range), max($range)];

        $max = $twentyfivepercent * $max;
        $max = $twentyfivepercent + $max;

        $min = $twentyfivepercent * $min;
        $min = $twentyfivepercent + $min;

        if ($compare <= $max && $compare >= $min) {
            // If there is a match now, that's a loose match
            // Reward a lower score
            $this->addScore('loose');
        }
    }

    /**
     * Resets the values after each iteration
     */
    private function resetCounts()
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
    private function addScore($type)
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

    /**
     * Display the specified resource.
     *
     * @param  Property $property
     * @return SearchProfileResource
     */
    public function show(Property $property)
    {
        $matches = SearchProfile::where('propertyType', $property->propertyType)->get();

        $matches->map(function ($profile) use ($property) {
            foreach ($profile->searchFields as $type => $search_value) {
                $property_value = $property->fields[$type];

                if (is_array($search_value)) {
                    $this->checkNullableRange($search_value, $property_value, $type);

                    $this->checkRange($search_value, $property_value, $type);

                    $this->checkDeviationRange($search_value, $property_value, $type);
                } elseif ($search_value === $property_value) {
                    // Earn some score for a direct hit
                    $this->addScore('strict');
                }

                $this->checked[] = $type;
            }

            \Log::debug("Before resetting counts " . print_r([
                $this->score,
                $this->loosematches,
                $this->strictmatches
            ], true));

            // Add new fields to each profile so we can sort by it
            $profile['score'] = $this->score;
            $profile['loosematches'] = $this->loosematches;
            $profile['strictmatches'] = $this->strictmatches;

            $this->resetCounts();

            return $profile;
        })->reject(function ($profile) use ($property) {
            // Remove results with miss matching fields
            $intersects = array_intersect(array_keys($property->fields), array_keys($profile->searchFields));

            return count($intersects) === 0;
        });

        $sorted = $matches->sort(function ($a, $b) {
            if ($a['score'] === $b['score']) {
                $return = 0;
            } else {
                $return = $a['score'] > $b['score'] ? -1 : 1;
            }

            return $return;
        });

        return SearchProfileResource::collection($sorted->values()->all());
    }

    // If the first value is null, then we accept any value for the first one
    // if ($profile_fields[0] === null && $profile_fields[1] !== null && $property_field <= $profile_fields[1]) {
    //     // Earn some score for a strict match
    //     $score += 33;
    //     $strictmatches += 1;

    // } elseif ($profile_fields[0] !== null && $profile_fields[1] === null && $property_field >= $profile_fields[0]) {
    //     // Earn some score for a strict match
    //     $score += 33;
    //     $strictmatches += 1;

    // } elseif (!in_array(null, $profile_fields)) {
    //     // If none is null
    //     // Check if it's within range
    //     list($max, $min) = array(max($profile_fields), min($profile_fields));

    //     if ($property_field <= $max && $property_field >= $min) {
    //         // Earn some score for a strict match
    //         $score += 33;
    //         $strictmatches += 1;

    //     } else {
    //         // Apply a 25% variation to search fields
    //         $twentyfive = 25 / 100;
    //         $max = $twentyfive * $max;
    //         $max = $twentyfive + $max;

    //         $min = $twentyfive * $min;
    //         $min = $twentyfive + $min;

    //         if ($property_field <= $max && $property_field >= $min) {
    //             // If there is a match now, that's a loose match
    //             // Reward a lower score
    //             $score += 10;
    //             $loosematches += 1;

    //         } else {
    //             // No matching field
    //             $score += 2;
    //             $loosematches += 1;

    //         }
    //     }
    // }
}
