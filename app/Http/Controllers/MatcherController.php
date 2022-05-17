<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchProfileResource;
use App\Models\Property;
use App\Models\SearchProfile;
use Illuminate\Http\Request;

class MatcherController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  Property $property
     * @return \Illuminate\Http\Response
     */
    public function show(Property $property)
    {
        $matches = SearchProfile::where('propertyType', $property->propertyType)->get();
        $score = 0;
        $loosematches = 0;
        $strictmatches = 0;

        $matches->map(function ($profile) use ($score, $property, $loosematches, $strictmatches) {

            //Check strict matching fields
            foreach ($profile->searchFields as $key => $profile_fields) {
                $property_field = $property->fields[$key]; //price

                // check for strict or loose match
                if (is_array($profile_fields)) {
                    // Check if our value is within the array

                    // If the first value is null, then we accept any value for the first one
                    if ($profile_fields[0] === null && $profile_fields[1] !== null && $property_field <= $profile_fields[1]) {
                        // Earn some score for a strict match
                        $score += 33;
                        $strictmatches += 1;

                    } elseif ($profile_fields[0] !== null && $profile_fields[1] === null && $property_field >= $profile_fields[0]) {
                        // Earn some score for a strict match
                        $score += 33;
                        $strictmatches += 1;

                    } elseif (!in_array(null, $profile_fields)) {
                        // If none is null
                        // Check if it's within range
                        list($max, $min) = array(max($profile_fields), min($profile_fields));

                        if ($property_field <= $max && $property_field >= $min) {
                            // Earn some score for a strict match
                            $score += 33;
                            $strictmatches += 1;

                        } else {
                            // Apply a 25% variation to search fields
                            $twentyfive = ceil(25 / 100);
                            $max = $twentyfive * $max;
                            $max = $twentyfive + $max;

                            $min = $twentyfive * $min;
                            $min = $twentyfive + $min;

                            if ($property_field <= $max && $property_field >= $min) {
                                // If there is a match now, that's a loose match
                                // Reward a lower score
                                $score += 10;
                                $loosematches += 1;

                            } else {
                                // No matching field
                                $score += 2;
                                $loosematches += 1;

                            }
                        }
                    }
                } elseif ($profile_fields === $property_field) {
                    // Earn some score for a direct hit
                    $score += 33;
                }
            }

            // Add new fields to each profile so we can sort by it
            $profile['score'] = $score;
            $profile['loosematches'] = $loosematches;
            $profile['strictmatches'] = $strictmatches;

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
}
