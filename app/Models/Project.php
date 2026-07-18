<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
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
        'hourly_rate',
        'estimated_hours',
        'paid_amount',
        'start_date',
        'due_date',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'priority' => Priority::class,
            'budget' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'estimated_hours' => 'decimal:2',
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

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function getProgressAttribute(): int
    {
        if ($this->hasAttribute('total_tasks_count')) {
            $total = $this->total_tasks_count ?? 0;
            $completed = $this->completed_tasks_count ?? 0;
        } else {
            $total = $this->tasks()->count();
            $completed = $total > 0
                ? $this->tasks()->whereIn('status', ['completed', 'review'])->count()
                : 0;
        }

        if ($total === 0) {
            return 0;
        }

        return (int) round(($completed / $total) * 100);
    }

    public function getTotalPriceAttribute(): ?float
    {
        if ($this->hourly_rate !== null && $this->estimated_hours !== null) {
            return round($this->hourly_rate * $this->estimated_hours, 2);
        }
        return null;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getPriorityLabelAttribute(): string
    {
        return $this->priority->label();
    }

    public function getNextStatusAttribute(): ProjectStatus
    {
        return $this->status->next();
    }

    public function getRemainingBudgetAttribute(): float
    {
        return ($this->budget ?? 0) - $this->paid_amount;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (Project $project) {
            if ($project->isDirty('status') && $project->status === ProjectStatus::Completed && !$project->completed_at) {
                $project->completed_at = now();
            }
        });
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

    public function scopeWithTaskCounts($query)
    {
        return $query->withCount([
            'tasks as total_tasks_count',
            'tasks as completed_tasks_count' => function ($q) {
                $q->whereIn('status', ['completed', 'review']);
            },
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', ProjectStatus::Completed);
    }
}
