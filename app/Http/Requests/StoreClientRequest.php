<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'שם הלקוח הוא חובה.',
            'email.required' => 'דוא"ל הוא חובה.',
            'email.email' => 'כתובת דוא"ל לא תקינה.',
            'email.unique' => 'כתובת דוא"ל זו כבר רשומה במערכת.',
            'password.required' => 'הסיסמה היא חובה.',
            'password.min' => 'הסיסמה חייבת להכיל לפחות :min תווים.',
            'password.confirmed' => 'אימות הסיסמה אינו תואם.',
            'password.regex' => 'הסיסמה חייבת להכיל לפחות אות אחת וספרה אחת.',
        ];
    }
}
