<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::forClient(auth()->id())->withTaskCounts()->latest()->paginate(15);
        return view('client.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $project->load([
            'tasks.assignee',
            'tasks.images',
            'files.uploader',
            'messages' => function ($q) {
                $q->with(['sender', 'receiver'])->orderBy('created_at', 'asc');
            },
        ]);

        return view('client.projects.show', compact('project'));
    }

    public function uploadTaskImage(Request $request, Project $project, $task)
    {
        abort_unless($project->user_id === auth()->id(), 404);
        $task = $project->tasks()->findOrFail($task);

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

        return redirect()->route('client.projects.show', $project)
            ->with('success', __('task_images_uploaded'));
    }

    public function destroyTaskImage(Request $request, Project $project, $task, TaskImage $image)
    {
        abort_unless($project->user_id === auth()->id(), 404);
        $task = $project->tasks()->findOrFail($task);
        abort_unless($image->task_id === $task->id, 404);

        if ($image->file_path && Storage::disk('local')->exists($image->file_path)) {
            Storage::disk('local')->delete($image->file_path);
        }
        $image->delete();

        return redirect()->route('client.projects.show', $project)
            ->with('success', __('task_image_deleted'));
    }

    public function exportCsv()
    {
        $projects = Project::forClient(auth()->id())->with('tasks')->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="my-projects-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, [__('csv_project'), __('csv_status'), __('csv_priority'), __('csv_budget'), __('csv_hourly_rate'), __('csv_estimated_hours'), __('csv_estimated_total'), __('csv_paid'), __('csv_balance'), __('csv_start'), __('csv_due'), __('csv_tasks'), __('csv_progress')]);

            foreach ($projects as $p) {
                fputcsv($file, [
                    $p->title,
                    $p->status_label,
                    $p->priority_label,
                    $p->budget ?? 0,
                    $p->hourly_rate ?? '',
                    $p->estimated_hours ?? '',
                    $p->total_price ?? '',
                    $p->paid_amount,
                    ($p->budget ?? 0) - $p->paid_amount,
                    $p->start_date?->format('d/m/Y') ?? '',
                    $p->due_date?->format('d/m/Y') ?? '',
                    $p->tasks->count(),
                    $p->progress . '%',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProjectCsv(Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $project->load(['tasks.assignee', 'files.uploader']);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $project->title . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($project) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [__('csv_project_name'), __('csv_status'), __('csv_priority'), __('csv_budget'), __('csv_hourly_rate'), __('csv_estimated_hours'), __('csv_estimated_total'), __('csv_paid'), __('csv_balance'), __('csv_start'), __('csv_due'), __('csv_progress')]);
            fputcsv($file, [
                $project->title,
                $project->status_label,
                $project->priority_label,
                $project->budget ?? 0,
                $project->hourly_rate ?? '',
                $project->estimated_hours ?? '',
                $project->total_price ?? '',
                $project->paid_amount,
                ($project->budget ?? 0) - $project->paid_amount,
                $project->start_date?->format('d/m/Y') ?? '',
                $project->due_date?->format('d/m/Y') ?? '',
                $project->progress . '%',
            ]);

            fputcsv($file, []);
            fputcsv($file, [__('csv_tasks')]);
            fputcsv($file, [__('csv_task_title'), __('csv_task_status'), __('csv_task_priority'), __('csv_task_assigned'), __('csv_task_estimated'), __('csv_task_due')]);

            foreach ($project->tasks as $task) {
                fputcsv($file, [
                    $task->title,
                    $task->status_label,
                    $task->priority_label,
                    $task->assignee?->name ?? '-',
                    $task->estimated_hours ?? '-',
                    $task->due_date?->format('d/m/Y') ?? '',
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, [__('csv_files')]);
            fputcsv($file, [__('csv_file_name'), __('csv_file_size'), __('csv_file_date')]);

            foreach ($project->files as $fileRecord) {
                fputcsv($file, [
                    $fileRecord->original_name,
                    $fileRecord->formatted_size,
                    $fileRecord->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
