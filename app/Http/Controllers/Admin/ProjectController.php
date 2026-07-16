<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('user')
            ->managedByAdmin(auth()->id())
            ->latest()
            ->paginate(15);
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')
            ->managedByAdmin(auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.projects.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $client = User::where('id', $value)->where('role', 'client')->first();
                    if (! $client || $client->admin_id !== auth()->id()) {
                        $fail('הלקוח שנבחר אינו זמין.');
                    }
                },
            ],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $project = Project::create($validated);
        return redirect()->route('admin.projects.show', $project)->with('success', 'הפרויקט נוצר בהצלחה.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        $project->load(['user', 'tasks.assignee', 'tasks.images.uploader', 'files.uploader', 'messages.sender', 'messages.receiver']);
        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $clients = User::where('role', 'client')
            ->managedByAdmin(auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.projects.edit', compact('project', 'clients'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'completed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && !$project->completed_at) {
            $validated['completed_at'] = now();
        }

        $project->update($validated);
        return redirect()->route('admin.projects.show', $project)->with('success', 'הפרויקט עודכן בהצלחה.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'הפרויקט נמחק בהצלחה.');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
        ]);

        $project->status = $validated['status'];
        if ($validated['status'] === 'completed' && !$project->completed_at) {
            $project->completed_at = now();
        }
        $project->save();

        return response()->json([
            'success' => true,
            'status' => $project->status_label,
            'progress' => $project->progress,
        ]);
    }

    public function exportCsv()
    {
        $projects = Project::with('user')
            ->managedByAdmin(auth()->id())
            ->latest()
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="projects-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($projects) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['שם פרויקט', 'לקוח', 'סטטוס', 'עדיפות', 'תקציב', 'שולם', 'תאריך התחלה', 'תאריך יעד', 'התקדמות']);

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->title,
                    $project->user->name,
                    $project->status_label,
                    $project->priority_label,
                    $project->budget ?? 0,
                    $project->paid_amount,
                    $project->start_date?->format('d/m/Y') ?? '',
                    $project->due_date?->format('d/m/Y') ?? '',
                    $project->progress . '%',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $projects = Project::with('user')
            ->managedByAdmin(auth()->id())
            ->latest()
            ->get();

        $html = $this->buildPdfHtml($projects);

        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="projects-' . now()->format('Y-m-d') . '.html"',
        ];

        return response($html, 200, $headers);
    }

    public function exportProjectCsv(Project $project)
    {
        $this->authorize('export', $project);
        $project->load(['tasks.assignee', 'files.uploader']);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $project->title . '-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($project) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['שם פרויקט', 'לקוח', 'סטטוס', 'עדיפות', 'תקציב', 'שולם', 'יתרה', 'התחלה', 'יעד', 'הושלם ב', 'התקדמות']);
            fputcsv($file, [
                $project->title,
                $project->user->name,
                $project->status_label,
                $project->priority_label,
                $project->budget ?? 0,
                $project->paid_amount,
                ($project->budget ?? 0) - $project->paid_amount,
                $project->start_date?->format('d/m/Y') ?? '',
                $project->due_date?->format('d/m/Y') ?? '',
                $project->completed_at?->format('d/m/Y') ?? '',
                $project->progress . '%',
            ]);

            fputcsv($file, []);
            fputcsv($file, ['משימות']);
            fputcsv($file, ['כותרת', 'סטטוס', 'עדיפות', 'מוקצה ל', 'שעות מוערכות', 'שעות בפועל', ' תאריך יעד']);

            foreach ($project->tasks as $task) {
                fputcsv($file, [
                    $task->title,
                    $task->status_label,
                    $task->priority_label,
                    $task->assignee?->name ?? '-',
                    $task->estimated_hours ?? '-',
                    $task->actual_hours ?? '-',
                    $task->due_date?->format('d/m/Y') ?? '',
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['קבצים']);
            fputcsv($file, ['שם קובץ', 'גודל', 'הועלה על ידי', 'תאריך']);

            foreach ($project->files as $fileRecord) {
                fputcsv($file, [
                    $fileRecord->original_name,
                    $fileRecord->formatted_size,
                    $fileRecord->uploader?->name ?? '-',
                    $fileRecord->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportProjectPdf(Project $project)
    {
        $this->authorize('export', $project);
        $project->load(['user', 'tasks.assignee', 'files.uploader']);

        $taskRows = '';
        foreach ($project->tasks as $t) {
            $statusColors = [
                'pending' => '#f59e0b', 'in_progress' => '#3b82f6', 'review' => '#8b5cf6', 'completed' => '#22c55e',
            ];
            $color = $statusColors[$t->status] ?? '#6b7280';
            $assigneeName = $t->assignee?->name ?? '-';
            $estHours = $t->estimated_hours ?? '-';
            $actHours = $t->actual_hours ?? '-';
            $taskDue = $t->due_date?->format('d/m/Y') ?? '-';
            $taskRows .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($t->title) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'><span style='color:{$color};font-weight:600'>" . e($t->status_label) . "</span></td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($t->priority_label) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($assigneeName) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;text-align:center'>" . e($estHours) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;text-align:center'>" . e($actHours) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($taskDue) . "</td>
            </tr>";
        }

        $fileRows = '';
        foreach ($project->files as $f) {
            $uploaderName = $f->uploader?->name ?? '-';
            $fileRows .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($f->original_name) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($f->formatted_size) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($uploaderName) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($f->created_at->format('d/m/Y H:i')) . "</td>
            </tr>";
        }

        $statusColors = [
            'pending' => '#f59e0b', 'in_progress' => '#3b82f6', 'review' => '#8b5cf6',
            'completed' => '#22c55e', 'cancelled' => '#ef4444',
        ];
        $sColor = $statusColors[$project->status] ?? '#6b7280';
        $startDate = $project->start_date?->format('d/m/Y') ?? '-';
        $dueDate = $project->due_date?->format('d/m/Y') ?? '-';
        $completedDate = $project->completed_at?->format('d/m/Y') ?? '-';

        $html = "<!DOCTYPE html><html dir='rtl' lang='he'><head><meta charset='UTF-8'><style>
            body{font-family:Arial,sans-serif;padding:40px;color:#1f2937}
            h1{font-size:22px;margin-bottom:4px}
            h2{font-size:16px;margin:24px 0 8px;color:#374151}
            .subtitle{color:#6b7280;margin-bottom:24px}
            .info-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:20px}
            .info-item{padding:8px;background:#f9fafb;border-radius:6px}
            .info-label{font-size:12px;color:#6b7280}
            .info-value{font-size:14px;font-weight:600;margin-top:2px}
            table{width:100%;border-collapse:collapse}
            th{background:#f3f4f6;padding:10px 8px;text-align:right;font-size:13px;color:#374151;border-bottom:2px solid #d1d5db}
            @media print{body{padding:20px}}
        </style></head><body>
            <h1>" . e($project->title) . "</h1>
            <p class='subtitle'>ForgeDesk Studio — דוח פרויקט | תאריך: " . e(now()->format('d/m/Y H:i')) . "</p>
            <div class='info-grid'>
                <div class='info-item'><div class='info-label'>לקוח</div><div class='info-value'>" . e($project->user->name) . "</div></div>
                <div class='info-item'><div class='info-label'>סטטוס</div><div class='info-value'><span style='color:{$sColor}'>" . e($project->status_label) . "</span></div></div>
                <div class='info-item'><div class='info-label'>עדיפות</div><div class='info-value'>" . e($project->priority_label) . "</div></div>
                <div class='info-item'><div class='info-label'>תקציב</div><div class='info-value'>₪" . number_format($project->budget ?? 0) . "</div></div>
                <div class='info-item'><div class='info-label'>שולם</div><div class='info-value'>₪" . number_format($project->paid_amount) . "</div></div>
                <div class='info-item'><div class='info-label'>התקדמות</div><div class='info-value'>" . e($project->progress) . "%</div></div>
                <div class='info-item'><div class='info-label'>התחלה</div><div class='info-value'>" . e($startDate) . "</div></div>
                <div class='info-item'><div class='info-label'>יעד</div><div class='info-value'>" . e($dueDate) . "</div></div>
                <div class='info-item'><div class='info-label'>הושלם ב</div><div class='info-value'>" . e($completedDate) . "</div></div>
            </div>
            " . ($project->description ? "<p><strong>תיאור:</strong> " . e($project->description) . "</p>" : '') . "
            <h2>משימות (" . $project->tasks->count() . ")</h2>
            <table><thead><tr><th>משימה</th><th>סטטוס</th><th>עדיפות</th><th>מוקצה ל</th><th>שעות מוערכות</th><th>שעות בפועל</th><th>יעד</th></tr></thead><tbody>{$taskRows}</tbody></table>
            <h2>קבצים (" . $project->files->count() . ")</h2>
            <table><thead><tr><th>קובץ</th><th>גודל</th><th>הועלה על ידי</th><th>תאריך</th></tr></thead><tbody>{$fileRows}</tbody></table>
            <script>window.onload=function(){window.print()}</script>
        </body></html>";

        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="' . $project->title . '-' . now()->format('Y-m-d') . '.html"',
        ];

        return response($html, 200, $headers);
    }

    private function buildPdfHtml($projects): string
    {
        $rows = '';
        foreach ($projects as $p) {
            $statusColors = [
                'pending' => '#f59e0b', 'in_progress' => '#3b82f6', 'review' => '#8b5cf6',
                'completed' => '#22c55e', 'cancelled' => '#ef4444',
            ];
            $color = $statusColors[$p->status] ?? '#6b7280';
            $rows .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($p->title) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($p->user->name) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'><span style='color:{$color};font-weight:600'>" . e($p->status_label) . "</span></td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($p->priority_label) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;text-align:left'>₪" . number_format($p->budget ?? 0) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;text-align:left'>₪" . number_format($p->paid_amount) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($p->start_date?->format('d/m/Y')) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($p->due_date?->format('d/m/Y')) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;text-align:center'>" . e($p->progress) . "%</td>
            </tr>";
        }

        return "<!DOCTYPE html><html dir='rtl' lang='he'><head><meta charset='UTF-8'><style>
            body{font-family:Arial,sans-serif;padding:40px;color:#1f2937}
            h1{font-size:24px;margin-bottom:4px}
            .subtitle{color:#6b7280;margin-bottom:24px}
            table{width:100%;border-collapse:collapse}
            th{background:#f3f4f6;padding:10px 8px;text-align:right;font-size:13px;color:#374151;border-bottom:2px solid #d1d5db}
            @media print{body{padding:20px}}
        </style></head><body>
            <h1>ForgeDesk Studio — דוח פרויקטים</h1>
            <p class='subtitle'>תאריך: " . now()->format('d/m/Y H:i') . " | סה\"כ: " . $projects->count() . " פרוикטים</p>
            <table>
                <thead><tr>
                    <th>פרויקט</th><th>לקוח</th><th>סטטוס</th><th>עדיפות</th>
                    <th>תקציב</th><th>שולם</th><th>התחלה</th><th>יעד</th><th>התקדמות</th>
                </tr></thead>
                <tbody>{$rows}</tbody>
            </table>
            <script>window.onload=function(){window.print()}</script>
        </body></html>";
    }
}
