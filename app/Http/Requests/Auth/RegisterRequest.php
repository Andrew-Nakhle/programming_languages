<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name'=>['required','string'],
            'last_name'=>['required','string'],
            'phone'=>['required','string','unique:users,phone','regex:/^\+?[0-9]{9,15}$/'],// 'regex:/^(\+9639\d{8}|09\d{8})$/'
            'password'=>['required','string','confirmed','min:8'],
            'avatar_path'=>['image','max:10240'],
            'id_card_path'=>['image','max:10240'],
            'birth_date'=>['required','date','before_or_equal:' . now()->subYears(16)->toDateString()],
        ];
    }
}
