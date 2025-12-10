<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'governorate'=>$this->governorate,
            'city'=>$this->city,
            'price'=>$this->price,
           'rooms'=>$this->rooms,
            'space'=>$this->space,
            'floor'=>$this->floor,
            'has_elevator'=>$this->has_elevator,
            'is_furnished'=>$this->is_furnished


        ];
    }
}
