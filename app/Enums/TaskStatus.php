<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Pending => __('pending'),
            self::InProgress => __('in_progress'),
            self::Review => __('review'),
            self::Completed => __('completed'),
        };
    }

    public function badgeClasses(): string
    {
        return match($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::InProgress => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Review => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        };
    }

    public function next(): self
    {
        return match($this) {
            self::Pending => self::InProgress,
            self::InProgress => self::Review,
            self::Review => self::Completed,
            self::Completed => self::Pending,
        };
    }
}
