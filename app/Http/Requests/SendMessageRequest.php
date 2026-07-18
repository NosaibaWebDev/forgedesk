<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:5000',
            'receiver_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'ההודעה היא חובה.',
            'body.max' => 'ההודעה לא יכולה לעלות על :max תווים.',
        ];
    }
}
