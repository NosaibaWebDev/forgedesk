<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('manageTasks', $project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,review,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $project->tasks()->create($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'המשימה נוספה בהצלחה.');
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,in_progress,review,completed',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'המשימה עודכנה בהצלחה.');
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $task->delete();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'המשימה נמחקה בהצלחה.');
    }

    public function toggleStatus(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $cycle = ['pending' => 'in_progress', 'in_progress' => 'review', 'review' => 'completed', 'completed' => 'pending'];
        $task->status = $cycle[$task->status] ?? 'pending';
        $task->save();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'סטטוס המשימה עודכן.');
    }

    public function uploadImage(Request $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $validated = $request->validate([
            'images' => 'required|array|max:5',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,gif,webp,bmp|max:5120',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('task-images/' . $project->id, 'public');
            $task->images()->create([
                'uploaded_by' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'תמונות המשימה הועלו בהצלחה.');
    }

    public function destroyImage(Project $project, Task $task, TaskImage $image)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);
        abort_unless($image->task_id === $task->id, 404);

        if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }
        $image->delete();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'תמונת המשימה נמחקה בהצלחה.');
    }
}
