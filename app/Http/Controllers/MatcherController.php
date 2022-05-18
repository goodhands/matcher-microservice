<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchProfileResource;
use App\Models\Property;
use App\Models\SearchProfile;
use App\Repository\SearchProfileRepository;

class MatcherController extends Controller
{
    public function __construct(SearchProfileRepository $repository)
    {
        $this->repository = $repository;
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
                    $this->repository->checkNullableRange($search_value, $property_value, $type);

                    $this->repository->checkRange($search_value, $property_value, $type);

                    $this->repository->checkDeviationRange($search_value, $property_value, $type);
                } elseif ($search_value === $property_value) {
                    // Earn some score for a direct hit
                    $this->repository->addScore('strict');
                }

                $this->repository->addChecked($type);
            }

            // Add new fields to each profile so we can sort by it
            $profile['score'] = $this->repository->getScore();
            $profile['loosematches'] = $this->repository->getLooseMatches();
            $profile['strictmatches'] = $this->repository->getStrictMatches();

            $this->repository->resetCounts();

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
