<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createDemoData(): array
    {
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'מנהל דמו']);
        $clients = collect();
        foreach (['A', 'B', 'C', 'D', 'E'] as $letter) {
            $clients->push(User::factory()->create([
                'role' => 'client',
                'admin_id' => $admin->id,
                'name' => "לקוח {$letter} - חברה {$letter}",
            ]));
        }

        $statuses = ['pending', 'in_progress', 'review', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $projects = collect();

        foreach ($clients as $i => $client) {
            $projectCount = 3 + ($i % 2);
            for ($j = 0; $j < $projectCount; $j++) {
                $projects->push(Project::create([
                    'user_id' => $client->id,
                    'title' => "פרויקט " . chr(65 + $i) . "-{$j} - " . ['אתר', 'אפליקציה', 'מערכת', 'דף נחיתה'][$j % 4],
                    'description' => 'תיאור פרויקט דמו',
                    'status' => $statuses[($i + $j) % count($statuses)],
                    'priority' => $priorities[($i + $j) % count($priorities)],
                    'budget' => rand(3000, 25000),
                    'hourly_rate' => rand(150, 250),
                    'estimated_hours' => rand(10, 80),
                    'paid_amount' => rand(0, 5000),
                    'start_date' => now()->subDays(rand(1, 30)),
                    'due_date' => now()->addDays(rand(5, 60)),
                    'notes' => 'הערות דמו',
                ]));
            }
        }

        return compact('admin', 'clients', 'projects');
    }

    public function test_admin_dashboard_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertStatus(200);
    }

    public function test_admin_projects_index_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.projects.index'))->assertStatus(200);
    }

    public function test_admin_project_show_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();
        $this->actingAs($admin)->get(route('admin.projects.show', $project))->assertStatus(200);
    }

    public function test_admin_clients_index_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.clients.index'))->assertStatus(200);
    }

    public function test_admin_client_show_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $client = User::where('role', 'client')->first();
        $this->actingAs($admin)->get(route('admin.clients.show', $client))->assertStatus(200);
    }

    public function test_admin_messages_index_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.messages.index'))->assertStatus(200);
    }

    public function test_admin_settings_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.settings.index'))->assertStatus(200);
    }

    public function test_admin_timetracker_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.timetracker.index'))->assertStatus(200);
    }

    public function test_admin_export_csv(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.projects.export.csv'))->assertStatus(200);
    }

    public function test_admin_export_pdf(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.projects.export.pdf'))->assertStatus(200);
    }

    public function test_admin_project_export_csv(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();
        $this->actingAs($admin)->get(route('admin.projects.export.project.csv', $project))->assertStatus(200);
    }

    public function test_admin_project_export_pdf(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();
        $this->actingAs($admin)->get(route('admin.projects.export.project.pdf', $project))->assertStatus(200);
    }

    public function test_admin_timetracker_export_csv(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.timetracker.export.csv'))->assertStatus(200);
    }

    public function test_admin_timetracker_export_pdf(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.timetracker.export.pdf'))->assertStatus(200);
    }

    public function test_client_dashboard_renders(): void
    {
        $this->createDemoData();
        $client = User::where('role', 'client')->first();
        $this->actingAs($client)->get(route('client.dashboard'))->assertStatus(200);
    }

    public function test_client_projects_index_renders(): void
    {
        $this->createDemoData();
        $client = User::where('role', 'client')->first();
        $this->actingAs($client)->get(route('client.projects.index'))->assertStatus(200);
    }

    public function test_client_project_show_renders(): void
    {
        $this->createDemoData();
        $client = User::where('role', 'client')->first();
        $project = Project::where('user_id', $client->id)->first();
        $this->actingAs($client)->get(route('client.projects.show', $project))->assertStatus(200);
    }

    public function test_client_messages_index_renders(): void
    {
        $this->createDemoData();
        $client = User::where('role', 'client')->first();
        $this->actingAs($client)->get(route('client.messages.index'))->assertStatus(200);
    }

    public function test_client_export_csv(): void
    {
        $this->createDemoData();
        $client = User::where('role', 'client')->first();
        $this->actingAs($client)->get(route('client.projects.export.csv'))->assertStatus(200);
    }

    public function test_admin_create_project_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.projects.create'))->assertStatus(200);
    }

    public function test_admin_edit_project_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();
        $this->actingAs($admin)->get(route('admin.projects.edit', $project))->assertStatus(200);
    }

    public function test_admin_create_client_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('admin.clients.create'))->assertStatus(200);
    }

    public function test_admin_edit_client_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $client = User::where('role', 'client')->first();
        $this->actingAs($admin)->get(route('admin.clients.edit', $client))->assertStatus(200);
    }

    public function test_profile_edit_renders(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin)->get(route('profile.edit'))->assertStatus(200);
    }

    public function test_task_toggle_status(): void
    {
        $this->createDemoData();
        $admin = User::where('role', 'admin')->first();
        $project = Project::first();
        $task = $project->tasks()->first();
        if (!$task) {
            $this->markTestSkipped('No tasks found');
        }
        $oldStatus = $task->status->value;
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->actingAs($admin)->post(route('admin.projects.tasks.toggle', [$project, $task]))
            ->assertRedirect();
        $task->refresh();
        $this->assertNotEquals($oldStatus, $task->status->value);
    }
}
