<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'client')->where('admin_id', $this->user()->id))],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'hourly_rate' => 'nullable|numeric|min:0',
            'estimated_hours' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => ['nullable', 'date', function ($attr, $value, $fail) {
                if ($value && $this->start_date && $value < $this->start_date) {
                    $fail(__('validation.after_or_equal', ['attribute' => __('due_date'), 'date' => $this->start_date]));
                }
            }],
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'בחירת לקוח היא חובה.',
            'user_id.exists' => 'הלקוח שנבחר אינו קיים.',
            'title.required' => 'שם הפרויקט הוא חובה.',
            'status.required' => 'בחירת סטטוס היא חובה.',
            'priority.required' => 'בחירת עדיפות היא חובה.',
            'paid_amount.numeric' => 'סכום השולם חייב להיות מספר.',
            'paid_amount.min' => 'סכום השולם לא יכול להיות שלילי.',
        ];
    }
}
