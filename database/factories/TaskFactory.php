<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
	protected $model = Task::class;

	public function definition(): array
	{
		return [
			'user_id' => User::factory(),
			'title' => fake()->sentence(),
			'description' => fake()->paragraph(),
			'deadline' => fake()->dateTimeBetween('now', '+1 month'),
			'priority' => fake()->randomElement(TaskPriority::cases())->value,
			'status' => fake()->randomElement(TaskStatus::cases())->value,
			'notes' => fake()->optional()->paragraph(),
		];
	}
}

