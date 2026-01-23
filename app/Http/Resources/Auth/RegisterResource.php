<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone'=>$this->phone,
            'profile(avatar)_picture_path' =>  $this->avatar_path ? url('storage/'.$this->avatar_path) : null,
            'id_card_picture_path' => $this->id_card_path ? url('storage/'.$this->id_card_path) : null,
            'birth_date' => $this->birth_date,

        ];
    }
}
