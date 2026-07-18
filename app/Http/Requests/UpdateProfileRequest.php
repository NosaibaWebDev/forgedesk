<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|max:2048',
            'preferred_language' => ['required', Rule::in(['he', 'ar'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'שדה השם הוא חובה.',
            'name.string' => 'שדה השם חייב להיות טקסט.',
            'name.max' => 'שדה השם לא יכול לעלות על :max תווים.',
            'email.required' => 'שדה הדוא"ל הוא חובה.',
            'email.email' => 'חייב להיות כתובת דוא"ל תקינה.',
            'email.unique' => 'כתובת דוא"ל זו כבר רשומה במערכת.',
            'phone.string' => 'שדה הטלפון חייב להיות טקסט.',
            'phone.max' => 'שדה הטלפון לא יכול לעלות על :max תווים.',
            'preferred_language.required' => 'בחירת שפה היא חובה.',
            'preferred_language.in' => 'שפה לא תקינה.',
        ];
    }
}
