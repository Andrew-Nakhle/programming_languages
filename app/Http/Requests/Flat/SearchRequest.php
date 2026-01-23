<?php

namespace App\Http\Requests\Flat;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'governorate'=>['nullable','string'],
            'city'=>['nullable','string'],
            'price'=>['nullable','numeric'],
            'address'=>['nullable','string'],
            'rooms'=>['nullable','integer','min:1'],
            'space'=>['nullable','integer','min:1','max:350'],
            'has_elevator'=>['nullable','boolean'],
            'is_furnished'=>['nullable','boolean'],
            'floor'=>['nullable','integer','min:1','max:163'],
            'section'=>['nullable','string','in:luxury apartments,standard apartments,bed spaces'],
            'no_filter'=>['nullable','boolean'],
        ];
    }
}
