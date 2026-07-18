<?php

namespace App\Enums;

enum Priority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match($this) {
            self::Low => __('low'),
            self::Medium => __('medium'),
            self::High => __('high'),
            self::Urgent => __('urgent'),
        };
    }

    public function textColor(): string
    {
        return match($this) {
            self::Low => 'text-ink-muted',
            self::Medium => 'text-amber-600',
            self::High => 'text-orange-600',
            self::Urgent => 'text-red-600',
        };
    }
}
