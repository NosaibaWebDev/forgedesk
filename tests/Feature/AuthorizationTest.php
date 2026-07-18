<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_own_clients_project(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client', 'admin_id' => $admin->id]);
        $project = Project::factory()->create(['user_id' => $client->id]);

        $response = $this->actingAs($admin)->get(route('admin.projects.show', $project));

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_other_admins_project(): void
    {
        $admin1 = User::factory()->create(['role' => 'admin']);
        $admin2 = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client', 'admin_id' => $admin2->id]);
        $project = Project::factory()->create(['user_id' => $client->id]);

        $response = $this->actingAs($admin1)->get(route('admin.projects.show', $project));

        $response->assertStatus(403);
    }

    public function test_client_can_access_own_project(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $project = Project::factory()->create(['user_id' => $client->id]);

        $response = $this->actingAs($client)->get(route('client.projects.show', $project));

        $response->assertStatus(200);
    }

    public function test_client_cannot_access_other_clients_project(): void
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $project = Project::factory()->create(['user_id' => $client2->id]);

        $response = $this->actingAs($client1)->get(route('client.projects.show', $project));

        $response->assertStatus(404);
    }

    public function test_login_rate_limiting_blocks_repeated_attempts(): void
    {
        config(['cache.store' => 'file']);
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        User::factory()->create(['email' => 'test@example.com', 'password' => Hash::make('correct')]);

        for ($i = 0; $i < 10; $i++) {
            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrong',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }
}
