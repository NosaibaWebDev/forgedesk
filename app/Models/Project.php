<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'budget',
        'paid_amount',
        'start_date',
        'due_date',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    public function getProgressAttribute(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->tasks()->whereIn('status', ['completed', 'review'])->count();

        return (int) round(($completed / $total) * 100);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'ממתין',
            'in_progress' => 'בתהליך',
            'review' => 'בבדיקה',
            'completed' => 'הושלם',
            'cancelled' => 'בוטל',
            default => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'נמוכה',
            'medium' => 'בינונית',
            'high' => 'גבוהה',
            'urgent' => 'דחופה',
            default => $this->priority,
        };
    }

    public function scopeForClient($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeManagedByAdmin($query, int $adminId)
    {
        return $query->whereHas('user', function ($q) use ($adminId) {
            $q->where('admin_id', $adminId);
        });
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }
}
