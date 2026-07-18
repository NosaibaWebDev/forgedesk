<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project)
    {
        $this->authorize('manageTasks', $project);
        $validated = $request->validated();
        $project->tasks()->create($validated);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_created'));
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);
        $task->update($request->validated());

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_updated'));
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $task->delete();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_deleted'));
    }

    public function toggleStatus(Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $task->cycleStatus();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_status_updated'));
    }

    public function updateStatus(Request $request, Project $project, Task $task)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);

        $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed',
        ]);

        $task->update(['status' => $request->status]);

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_status_updated'));
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
            $path = $file->store('task-images/' . $project->id, 'local');
            $task->images()->create([
                'uploaded_by' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_images_uploaded'));
    }

    public function destroyImage(Project $project, Task $task, TaskImage $image)
    {
        $this->authorize('manageTasks', $project);
        abort_unless($task->project_id === $project->id, 404);
        abort_unless($image->task_id === $task->id, 404);

        if ($image->file_path && Storage::disk('local')->exists($image->file_path)) {
            Storage::disk('local')->delete($image->file_path);
        }
        $image->delete();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', __('task_image_deleted'));
    }
}
