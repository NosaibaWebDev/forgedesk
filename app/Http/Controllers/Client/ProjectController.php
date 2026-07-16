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
        $projects = Project::forClient(auth()->id())->with('tasks')->latest()->paginate(15);
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
            $path = $file->store('task-images/' . $project->id, 'public');
            $task->images()->create([
                'uploaded_by' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('client.projects.show', $project)
            ->with('success', 'תמונות המשימה הועלו בהצלחה.');
    }

    public function destroyTaskImage(Request $request, Project $project, $task, TaskImage $image)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        if ($image->file_path && Storage::disk('public')->exists($image->file_path)) {
            Storage::disk('public')->delete($image->file_path);
        }
        $image->delete();

        return redirect()->route('client.projects.show', $project)
            ->with('success', 'תמונת המשימה נמחקה בהצלחה.');
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
            fputcsv($file, ['פרויקט', 'סטטוס', 'עדיפות', 'תקציב', 'שולם', 'יתרה', 'התחלה', 'יעד', 'משימות', 'התקדמות']);

            foreach ($projects as $p) {
                fputcsv($file, [
                    $p->title,
                    $p->status_label,
                    $p->priority_label,
                    $p->budget ?? 0,
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

            fputcsv($file, ['שם פרויקט', 'סטטוס', 'עדיפות', 'תקציב', 'שולם', 'יתרה', 'התחלה', 'יעד', 'התקדמות']);
            fputcsv($file, [
                $project->title,
                $project->status_label,
                $project->priority_label,
                $project->budget ?? 0,
                $project->paid_amount,
                ($project->budget ?? 0) - $project->paid_amount,
                $project->start_date?->format('d/m/Y') ?? '',
                $project->due_date?->format('d/m/Y') ?? '',
                $project->progress . '%',
            ]);

            fputcsv($file, []);
            fputcsv($file, ['משימות']);
            fputcsv($file, ['כותרת', 'סטטוס', 'עדיפות', 'מוקצה ל', 'שעות מוערכות', 'תאריך יעד']);

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
            fputcsv($file, ['קבצים']);
            fputcsv($file, ['שם קובץ', 'גודל', 'תאריך']);

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
