<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'searchProfileId'       => $this->id,
            'score'                 => $this->score,
            'strictMatchesCount'    => $this->strictmatches,
            'looseMatchesCount'     => $this->loosematches,
        ];
    }
}
