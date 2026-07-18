<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending => __('pending'),
            self::InProgress => __('in_progress'),
            self::Review => __('review'),
            self::Completed => __('completed'),
            self::Cancelled => __('cancelled'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending => 'amber',
            self::InProgress => 'blue',
            self::Review => 'violet',
            self::Completed => 'emerald',
            self::Cancelled => 'red',
        };
    }

    public function badgeClasses(): string
    {
        return match($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            self::InProgress => 'bg-blue-50 text-blue-700 ring-blue-600/20',
            self::Review => 'bg-violet-50 text-violet-700 ring-violet-600/20',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Cancelled => 'bg-red-50 text-red-700 ring-red-600/20',
        };
    }

    public function next(): self
    {
        return match($this) {
            self::Pending => self::InProgress,
            self::InProgress => self::Review,
            self::Review => self::Completed,
            self::Completed => self::Pending,
            self::Cancelled => self::Cancelled,
        };
    }
}
