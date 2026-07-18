<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        abort_unless($project->user_id === auth()->id(), 404);

        $validated = $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,bmp,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar,7z',
            'notes' => 'nullable|string|max:500',
        ]);

        $count = 0;
        foreach ($request->file('files') as $file) {
            $originalName = $file->getClientOriginalName();
            $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                'project-files/' . $project->id,
                $storedName,
                'local'
            );

            $record = new ProjectFile();
            $record->project_id = $project->id;
            $record->uploaded_by = auth()->id();
            $record->original_name = $originalName;
            $record->stored_name = $storedName;
            $record->path = $path;
            $record->mime_type = $file->getMimeType();
            $record->size = $file->getSize();
            $record->notes = $validated['notes'] ?? null;
            $record->save();
            $count++;
        }

        return redirect()->route('client.projects.show', $project)
            ->with('success', $count . ' ' . __('files_uploaded'));
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        abort_unless($project->user_id === auth()->id(), 404);
        abort_unless($file->project_id === $project->id, 404);

        Storage::disk('local')->delete($file->path);
        $file->delete();

        return redirect()->route('client.projects.show', $project)
            ->with('success', __('file_deleted'));
    }

    public function download(Project $project, ProjectFile $file)
    {
        abort_unless($project->user_id === auth()->id(), 404);
        abort_unless($file->project_id === $project->id, 404);

        if (!Storage::disk('local')->exists($file->path)) {
            abort(404);
        }

        return Storage::disk('local')->download($file->path, $file->original_name);
    }
}
