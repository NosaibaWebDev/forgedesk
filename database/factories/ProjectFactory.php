<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in_progress', 'review', 'completed', 'cancelled']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'budget' => fake()->randomFloat(2, 1000, 50000),
            'paid_amount' => 0,
            'start_date' => now()->subDays(fake()->numberBetween(1, 30)),
            'due_date' => now()->addDays(fake()->numberBetween(7, 90)),
        ];
    }
}
