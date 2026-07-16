<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalProjects = Project::managedByAdmin($userId)->count();
        $activeProjects = Project::managedByAdmin($userId)->active()->count();
        $totalClients = User::where('role', 'client')->managedByAdmin($userId)->count();
        $pendingTasks = Task::where('status', 'pending')
            ->whereHas('project', fn ($q) => $q->managedByAdmin($userId))
            ->count();
        $totalRevenue = Project::managedByAdmin($userId)->sum('paid_amount');
        $unreadMessages = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->whereHas('project', fn ($q) => $q->managedByAdmin($userId))
            ->count();

        $activeProjectsList = Project::with('user')
            ->managedByAdmin($userId)
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $urgentTasks = Task::with(['project', 'assignee'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->where('priority', 'urgent')
            ->whereHas('project', fn ($q) => $q->managedByAdmin($userId))
            ->latest()
            ->take(5)
            ->get();

        $upcomingDeadlines = Task::with(['project', 'assignee'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(7))
            ->where('status', '!=', 'completed')
            ->whereHas('project', fn ($q) => $q->managedByAdmin($userId))
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentMessages = Message::with(['sender', 'receiver', 'project'])
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->whereHas('project', fn ($q) => $q->managedByAdmin($userId))
            ->latest()
            ->take(5)
            ->get();

        $recentClients = User::where('role', 'client')
            ->managedByAdmin($userId)
            ->withCount('projects')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'totalClients',
            'pendingTasks',
            'totalRevenue',
            'unreadMessages',
            'activeProjectsList',
            'urgentTasks',
            'upcomingDeadlines',
            'recentMessages',
            'recentClients'
        ));
    }

    public function removeProject(Project $project)
    {
        $this->authorize('update', $project);
        $project->update(['status' => 'cancelled']);
        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        Project::managedByAdmin(auth()->id())
            ->where('status', '!=', 'cancelled')
            ->update(['status' => 'cancelled']);
        return response()->json(['success' => true]);
    }
}
