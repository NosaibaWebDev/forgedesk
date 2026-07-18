<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'הסיסמה הנוכחית היא חובה.',
            'password.required' => 'הסיסמה החדשה היא חובה.',
            'password.min' => 'הסיסמה חייבת להכיל לפחות :min תווים.',
            'password.confirmed' => 'אימות הסיסמה אינו תואם.',
            'password.regex' => 'הסיסמה חייבת להכיל לפחות אות אחת וספרה אחת.',
        ];
    }
}
