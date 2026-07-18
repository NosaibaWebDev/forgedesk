<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalProjects = Project::forClient($userId)->count();
        $activeProjects = Project::forClient($userId)->active()->count();
        $completedProjects = Project::forClient($userId)->where('status', 'completed')->count();
        $totalSpent = Project::forClient($userId)->sum('paid_amount');
        $unreadMessages = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        $recentProjects = Project::forClient($userId)
            ->withTaskCounts()
            ->latest()
            ->take(5)
            ->get();

        $upcomingDeadlines = Task::whereHas('project', function ($q) use ($userId) {
            $q->forClient($userId);
        })
            ->with('project')
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(14))
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentMessages = Message::with(['sender', 'receiver', 'project'])
            ->whereHas('project', function ($q) use ($userId) {
                $q->forClient($userId);
            })
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('client.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'totalSpent',
            'unreadMessages',
            'recentProjects',
            'upcomingDeadlines',
            'recentMessages'
        ));
    }
}
