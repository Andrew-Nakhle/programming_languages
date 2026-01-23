<?php

namespace App\Http\Requests\Flat;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'governorate'=>['required','string'],
            'city'=>['required','string'],
            'price'=>['required','decimal:0,2'],
            'address'=>['required','string'],
            'rooms'=>['required','integer','min:1'],
            'space'=>['required','integer','min:1'],
            'has_elevator'=>['required','boolean'],
            'is_furnished'=>['required','boolean'],
            'floor'=>['required','integer','min:1'],
            'available_date'=>['nullable','date','after:today'],
            'flat_image'=>['required','image'],
            'section'=>['required','string','in:luxury apartments,standard apartments,bed spaces,others'],
        ];
    }
}
