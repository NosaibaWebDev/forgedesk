<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $conversations = Project::forClient($userId)
            ->whereHas('messages', function ($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->with(['messages' => function ($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
                $query->latest()->limit(1);
            }])
            ->withCount(['messages as unread_count' => function ($query) use ($userId) {
                $query->where('receiver_id', $userId)->where('is_read', false);
            }])
            ->latest()
            ->get();

        $projects = Project::forClient($userId)->latest()->get();

        return view('client.messages.index', compact('conversations', 'projects'));
    }

    public function show(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $userId = auth()->id();

        $messages = Message::where('project_id', $project->id)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('project_id', $project->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        \Illuminate\Support\Facades\Cache::forget("user_{$userId}_unread_messages");

        return view('client.messages.show', compact('project', 'messages'));
    }

    public function store(Request $request, Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $adminId = $project->user->admin_id ?? null;

        if (!$adminId) {
            return back()->with('error', __('no_admin_found'));
        }

        Message::create([
            'project_id' => $project->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $adminId,
            'body' => $validated['body'],
            'is_read' => false,
        ]);

        return redirect()->route('client.messages.show', $project)
            ->with('success', __('message_sent'));
    }
}
