<?php

namespace App\Http\Controllers;

use App\Models\ProjectFile;
use App\Models\TaskImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FileDownloadController extends Controller
{
    public function projectFile(ProjectFile $file)
    {
        $user = auth()->user();
        $project = $file->project;

        if ($user->isAdmin()) {
            abort_unless($project->user?->admin_id === $user->id, 403);
        } else {
            abort_unless($project->user_id === $user->id, 403);
        }

        if (!Storage::disk('local')->exists($file->path)) {
            abort(404);
        }

        return Storage::disk('local')->download($file->path, $file->original_name);
    }

    public function projectFilePreview(ProjectFile $file)
    {
        $user = auth()->user();
        $project = $file->project;

        if ($user->isAdmin()) {
            abort_unless($project->user?->admin_id === $user->id, 403);
        } else {
            abort_unless($project->user_id === $user->id, 403);
        }

        if (!Storage::disk('local')->exists($file->path)) {
            abort(404);
        }

        $mime = $file->mime_type ?? Storage::disk('local')->mimeType($file->path);

        return response()->stream(function () use ($file) {
            $stream = Storage::disk('local')->readStream($file->path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename*=UTF-8\'\'' . rawurlencode($file->original_name),
        ]);
    }

    public function taskImage(TaskImage $image)
    {
        $user = auth()->user();
        $task = $image->task;
        $project = $task->project;

        if ($user->isAdmin()) {
            abort_unless($project->user?->admin_id === $user->id, 403);
        } else {
            abort_unless($project->user_id === $user->id, 403);
        }

        if (!Storage::disk('local')->exists($image->file_path)) {
            abort(404);
        }

        $mime = $image->mime_type ?? Storage::disk('local')->mimeType($image->file_path);

        return response()->stream(function () use ($image) {
            $stream = Storage::disk('local')->readStream($image->file_path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename*=UTF-8\'\'' . rawurlencode($image->original_name),
        ]);
    }

    public static function signedUrl(string $type, int $id): string
    {
        $routeName = $type === 'task-image' ? 'file.download.task-image' : 'file.download.project-file';
        $paramName = $type === 'task-image' ? 'image' : 'file';

        return URL::temporarySignedRoute(
            $routeName,
            now()->addHours(2),
            [$paramName => $id],
            false
        );
    }
}
