<?php

namespace App\Http\Requests\Reservation;

use App\Models\Flat;
use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_time'=>['required','date','after:today'],
            'end_time'=>['required','date','after:start_time'],
//            'flat_id' => ['required','exists:flats,id'],
        'id'=>['required','integer','exists:reservations,id'],

        ];
    }
    public function withValidator($validator){
        $validator->after(function ($validator) {
            $reservationId = $this->input('id');
          $reservation = Reservation::find($reservationId);
            if (!$reservation) { $validator->errors()->add('id', 'Reservation not found.'); return; }
         $flat= $reservation->flat;
            if ($flat && $flat->available_date !== null) {
                if ($this->start_time < $flat->available_date) {
                    $validator->errors()->add('start_time', 'Start time must be after or equal to flat available_date.');
                }
                if ($this->end_time < $flat->available_date) {
                    $validator->errors()->add('end_time', 'End time must be after or equal to flat available_date.');
                }
            }
        });

    }
}
