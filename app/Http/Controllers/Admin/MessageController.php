<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendMessageRequest;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;

class MessageController extends Controller
{
    public function index()
    {
        $adminId = auth()->id();

        $conversations = Project::managedByAdmin($adminId)
        ->whereHas('messages', function ($query) use ($adminId) {
            $query->where('sender_id', $adminId)->orWhere('receiver_id', $adminId);
        })
        ->with(['user', 'messages' => function ($query) use ($adminId) {
            $query->where('sender_id', $adminId)->orWhere('receiver_id', $adminId);
            $query->latest()->limit(1);
        }])
        ->withCount(['messages as unread_count' => function ($query) use ($adminId) {
            $query->where('receiver_id', $adminId)->where('is_read', false);
        }])
        ->latest()
        ->get();

        return view('admin.messages.index', compact('conversations'));
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $adminId = auth()->id();

        $messages = Message::where('project_id', $project->id)
            ->where(function ($query) use ($adminId) {
                $query->where('sender_id', $adminId)->orWhere('receiver_id', $adminId);
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        Message::where('project_id', $project->id)
            ->where('receiver_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        \Illuminate\Support\Facades\Cache::forget("user_{$adminId}_unread_messages");

        $client = $project->user;

        return view('admin.messages.show', compact('project', 'messages', 'client'));
    }

    public function store(SendMessageRequest $request, Project $project)
    {
        $this->authorize('view', $project);

        Message::create([
            'project_id' => $project->id,
            'sender_id' => auth()->id(),
            'receiver_id' => $project->user_id,
            'body' => $request->validated('body'),
            'is_read' => false,
        ]);

        return redirect()->route('admin.messages.show', $project)
            ->with('success', __('message_sent'));
    }
}
