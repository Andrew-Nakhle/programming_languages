<?php

namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'start_time'=>$this->start_time,
            'end_time'=>$this->end_time,
            'price'=>$this->price,
            'status'=>$this->status,
            'user_id'=>$this->user_id,
            'flat_id'=>$this->flat_id
        ];
    }
}
