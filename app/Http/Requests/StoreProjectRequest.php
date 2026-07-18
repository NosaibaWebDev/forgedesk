<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'בחירת לקוח היא חובה.',
            'user_id.exists' => 'הלקוח שנבחר אינו קיים.',
            'title.required' => 'שם הפרויקט הוא חובה.',
            'title.max' => 'שם הפרויקט לא יכול לעלות על :max תווים.',
            'status.required' => 'בחירת סטטוס היא חובה.',
            'status.in' => 'סטטוס לא תקין.',
            'priority.required' => 'בחירת עדיפות היא חובה.',
            'priority.in' => 'עדיפות לא תקינה.',
            'budget.numeric' => 'התקציב חייב להיות מספר.',
            'budget.min' => 'התקציב לא יכול להיות שלילי.',
            'hourly_rate.numeric' => 'התעריף השעתי חייב להיות מספר.',
            'hourly_rate.min' => 'התעריף השעתי לא יכול להיות שלילי.',
            'estimated_hours.numeric' => 'השעות המוערכות חייבות להיות מספר.',
            'estimated_hours.min' => 'השעות המוערכות לא יכולות להיות שליליות.',
            'start_date.date' => 'תאריך התחלה לא תקין.',
            'due_date.date' => 'תאריך יעד לא תקין.',
            'due_date.after_or_equal' => 'תאריך היעד חייב להיות אחרי או שווה לתאריך ההתחלה.',
        ];
    }
}
