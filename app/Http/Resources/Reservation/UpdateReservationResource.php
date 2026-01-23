<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'start_time'=>$this->start_time,
            'ent_time'=>$this->end_time,
            'status'=>$this->status,
            'price'=>$this->price,
            'user_id'=>$this->user_id,
            'flat_id'=>$this->flat_id,
        ];
    }
}
