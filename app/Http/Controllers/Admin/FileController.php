<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('manageFiles', $project);

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
                'public'
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

        return redirect()->route('admin.projects.show', $project)
            ->with('success', $count . ' קבצים הועלו בהצלחה.');
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        $this->authorize('manageFiles', $project);
        abort_unless($file->project_id === $project->id, 404);

        \Storage::disk('public')->delete($file->path);
        $file->delete();

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'הקובץ נמחק בהצלחה.');
    }

    public function download(Project $project, ProjectFile $file)
    {
        $this->authorize('view', $project);
        abort_unless($file->project_id === $project->id, 404);

        if (! \Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        return \Storage::disk('public')->download($file->path, $file->original_name);
    }
}
