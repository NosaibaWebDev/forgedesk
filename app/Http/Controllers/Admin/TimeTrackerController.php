<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeTrackerController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $today = Carbon::today();

        $runningEntry = TimeEntry::forUser($userId)->running()->first();

        $query = TimeEntry::forUser($userId)
            ->with(['project.user', 'task'])
            ->where('date', '>=', now()->subDays(60));

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('user_id', $request->client_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get()
            ->groupBy(function ($entry) {
                return $entry->date->format('Y-m-d');
            });

        $totalToday = TimeEntry::forUser($userId)
            ->forDate($today)
            ->where('is_running', false)
            ->sum('duration_minutes');

        $totalWeek = TimeEntry::forUser($userId)
            ->where('date', '>=', now()->startOfWeek())
            ->where('is_running', false)
            ->sum('duration_minutes');

        $totalMonth = TimeEntry::forUser($userId)
            ->where('date', '>=', now()->startOfMonth())
            ->where('is_running', false)
            ->sum('duration_minutes');

        $totalFiltered = $entries->sum(function ($dayEntries) {
            return $dayEntries->where('is_running', false)->sum('duration_minutes');
        });

        $projects = Project::active()
            ->managedByAdmin(auth()->id())
            ->orderBy('title')
            ->get();
        $clients = User::where('role', 'client')
            ->managedByAdmin(auth()->id())
            ->orderBy('name')
            ->get();

        return view('admin.timetracker.index', compact(
            'runningEntry', 'entries', 'totalToday', 'totalWeek', 'totalMonth', 'totalFiltered', 'projects', 'clients'
        ));
    }

    public function exportCsv(Request $request)
    {
        $userId = auth()->id();

        $query = TimeEntry::forUser($userId)
            ->with(['project.user', 'task'])
            ->where('date', '>=', now()->subDays(60));

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('user_id', $request->client_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="time-entries-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($entries) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, [__('csv_timer_date'), __('csv_timer_start'), __('csv_timer_end'), __('csv_timer_duration'), __('csv_timer_project'), __('csv_timer_client'), __('csv_timer_task'), __('csv_timer_desc')]);

            foreach ($entries as $e) {
                $duration = $e->is_running ? __('csv_running') : $e->formatted_duration;
                fputcsv($file, [
                    $e->date->format('d/m/Y'),
                    $e->start_time->format('H:i'),
                    $e->end_time ? $e->end_time->format('H:i') : '-',
                    $duration,
                    $e->project?->title ?? '-',
                    $e->project?->user?->name ?? '-',
                    $e->task?->title ?? '-',
                    $e->description ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $userId = auth()->id();

        $query = TimeEntry::forUser($userId)
            ->with(['project.user', 'task'])
            ->where('date', '>=', now()->subDays(60));

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->whereHas('project', function ($q) use ($request) {
                $q->where('user_id', $request->client_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $entries = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $totalMinutes = $entries->where('is_running', false)->sum('duration_minutes');
        $totalDays = round($totalMinutes / 1440);

        $rows = '';
        foreach ($entries as $e) {
            $duration = $e->is_running ? __('csv_running') : $e->formatted_duration;
            $rows .= "<tr>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($e->date->format('d/m/Y')) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;font-family:monospace'>" . e($e->start_time->format('H:i')) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;font-family:monospace'>" . e($e->end_time ? $e->end_time->format('H:i') : '-') . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb;font-family:monospace;font-weight:600'>" . e($duration) . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($e->project?->title ?? '-') . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($e->project?->user?->name ?? '-') . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($e->task?->title ?? '-') . "</td>
                <td style='padding:8px;border-bottom:1px solid #e5e7eb'>" . e($e->description ?? '-') . "</td>
            </tr>";
        }

        $totalEntries = $entries->count();
        $nowDate = now()->format('d/m/Y H:i');

        $html = '<!DOCTYPE html><html dir="rtl" lang="he"><head><meta charset="UTF-8"><style>
            body{font-family:Arial,sans-serif;padding:40px;color:#1f2937}
            h1{font-size:22px;margin-bottom:4px}
            .subtitle{color:#6b7280;margin-bottom:24px}
            .summary{display:inline-block;background:#f0f4ff;padding:8px 16px;border-radius:8px;margin-bottom:20px;font-weight:600;color:#4c6ef5}
            table{width:100%;border-collapse:collapse}
            th{background:#f3f4f6;padding:10px 8px;text-align:right;font-size:13px;color:#374151;border-bottom:2px solid #d1d5db}
            @media print{body{padding:20px}}
        </style></head><body>
            <h1>' . e(__('pdf_timer_report')) . '</h1>
            <p class="subtitle">ForgeDesk Studio | ' . e(__('pdf_date')) . ' ' . e($nowDate) . ' | ' . e(__('pdf_total_entries')) . ' ' . e((string) $totalEntries) . '</p>
            <div class="summary">' . e(__('pdf_total_hours')) . ' ' . e($totalDays) . ' days</div>
            <table>
                <thead><tr>
                    <th>' . e(__('csv_timer_date')) . '</th><th>' . e(__('csv_timer_start')) . '</th><th>' . e(__('csv_timer_end')) . '</th><th>' . e(__('csv_timer_duration')) . '</th><th>' . e(__('csv_timer_project')) . '</th><th>' . e(__('csv_timer_client')) . '</th><th>' . e(__('csv_timer_task')) . '</th><th>' . e(__('csv_timer_desc')) . '</th>
                </tr></thead>
                <tbody>' . $rows . '</tbody>
            </table>
            <script>window.onload=function(){window.print()}</script>
        </body></html>';

        $headers = [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="time-entries-' . now()->format('Y-m-d') . '.html"',
        ];

        return response($html, 200, $headers);
    }

    public function start(Request $request)
    {
        $userId = auth()->id();

        $running = TimeEntry::forUser($userId)->running()->first();
        if ($running) {
            return redirect()->route('admin.timetracker.index')->with('error', __('already_running'));
        }

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id', $this->managedProjectRule()],
            'task_id' => ['nullable', 'exists:tasks,id', $this->taskBelongsToProjectRule($request->project_id)],
            'description' => 'nullable|string|max:255',
        ]);

        TimeEntry::create([
            'user_id' => $userId,
            'project_id' => $validated['project_id'] ?? null,
            'task_id' => $validated['task_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_time' => now(),
            'is_running' => true,
            'date' => Carbon::today(),
        ]);

        return redirect()->route('admin.timetracker.index')->with('success', __('timer_started'));
    }

    public function stop(TimeEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);
        abort_unless($entry->is_running, 400);

        $entry->update([
            'end_time' => now(),
            'duration_minutes' => $entry->start_time->diffInMinutes(now()),
            'is_running' => false,
        ]);

        return redirect()->route('admin.timetracker.index')->with('success', __('timer_stopped') . ' ' . $entry->fresh()->formatted_duration);
    }

    public function destroy(TimeEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);

        $entry->delete();

        return redirect()->route('admin.timetracker.index')->with('success', __('entry_deleted'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id', $this->managedProjectRule()],
            'task_id' => ['nullable', 'exists:tasks,id', $this->taskBelongsToProjectRule($request->project_id)],
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
        $duration = $start->diffInMinutes($end);

        TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'] ?? null,
            'task_id' => $validated['task_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_time' => $start,
            'end_time' => $end,
            'duration_minutes' => $duration,
            'is_running' => false,
            'date' => $validated['date'],
        ]);

        return redirect()->route('admin.timetracker.index')->with('success', __('entry_added'));
    }

    public function getTasks($projectId)
    {
        $project = Project::findOrFail($projectId);
        $this->authorize('view', $project);

        $tasks = Task::where('project_id', $projectId)->orderBy('title')->get(['id', 'title']);
        return response()->json($tasks);
    }

    public function edit(TimeEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);
        $projects = Project::active()
            ->managedByAdmin(auth()->id())
            ->orderBy('title')
            ->get();
        return view('admin.timetracker.edit', compact('entry', 'projects'));
    }

    public function update(Request $request, TimeEntry $entry)
    {
        abort_unless($entry->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id', $this->managedProjectRule()],
            'task_id' => ['nullable', 'exists:tasks,id', $this->taskBelongsToProjectRule($request->project_id)],
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
        $duration = $start->diffInMinutes($end);

        $entry->update([
            'project_id' => $validated['project_id'] ?? null,
            'task_id' => $validated['task_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_time' => $start,
            'end_time' => $end,
            'duration_minutes' => $duration,
            'date' => $validated['date'],
        ]);

        return redirect()->route('admin.timetracker.index')->with('success', __('entry_updated'));
    }

    private function managedProjectRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (! $value) {
                return;
            }
            $project = Project::find($value);
            if (! $project || $project->user?->admin_id !== auth()->id()) {
                $fail(__('invalid_project'));
            }
        };
    }

    private function taskBelongsToProjectRule(?string $projectId): \Closure
    {
        return function ($attribute, $value, $fail) use ($projectId) {
            if (! $value) {
                return;
            }
            $task = Task::find($value);
            if (! $task || ! $projectId || $task->project_id != $projectId) {
                $fail(__('invalid_task'));
            }
        };
    }
}
