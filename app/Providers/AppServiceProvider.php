<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\TimeEntry;
use App\Models\User;
use App\Observers\MessageObserver;
use App\Policies\ClientPolicy;
use App\Policies\MessagePolicy;
use App\Policies\ProjectFilePolicy;
use App\Policies\ProjectPolicy;
use App\Policies\TimeEntryPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Message::observe(MessageObserver::class);

        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(User::class, ClientPolicy::class);
        Gate::policy(ProjectFile::class, ProjectFilePolicy::class);
        Gate::policy(TimeEntry::class, TimeEntryPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);

        RateLimiter::for('login', function ($request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('exports', function ($request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
