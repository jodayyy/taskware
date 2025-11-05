<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
	use RefreshDatabase;

	public function test_user_can_view_tasks_index(): void
	{
		$user = User::factory()->create();
		Task::factory()->count(5)->create(['user_id' => $user->id]);

		$response = $this->actingAs($user)->get('/tasks');

		$response->assertStatus(200);
		$response->assertViewIs('user.tasks.index');
	}

	public function test_user_can_create_task(): void
	{
		$user = User::factory()->create();

		$response = $this->actingAs($user)->post('/tasks', [
			'title' => 'Test Task',
			'description' => 'Test Description',
			'deadline' => '2024-12-31',
			'priority' => TaskPriority::NORMAL->value,
			'notes' => 'Test notes',
		]);

		$response->assertRedirect('/dashboard');
		$response->assertSessionHas('success');
		
		$this->assertDatabaseHas('tasks', [
			'user_id' => $user->id,
			'title' => 'Test Task',
			'status' => TaskStatus::TO_DO->value,
		]);
	}

	public function test_user_can_view_own_task(): void
	{
		$user = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $user->id]);

		$response = $this->actingAs($user)->get("/tasks/{$task->id}");

		$response->assertStatus(200);
		$response->assertViewIs('user.tasks.show');
	}

	public function test_user_cannot_view_other_user_task(): void
	{
		$user = User::factory()->create();
		$otherUser = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $otherUser->id]);

		$response = $this->actingAs($user)->get("/tasks/{$task->id}");

		$response->assertForbidden();
	}

	public function test_user_can_update_own_task(): void
	{
		$user = User::factory()->create();
		$task = Task::factory()->create([
			'user_id' => $user->id,
			'title' => 'Old Title',
		]);

		$response = $this->actingAs($user)->put("/tasks/{$task->id}", [
			'title' => 'New Title',
			'description' => $task->description,
			'deadline' => $task->deadline->format('Y-m-d'),
			'priority' => $task->priority->value,
			'status' => $task->status->value,
			'notes' => $task->notes,
		]);

		$response->assertRedirect();
		$response->assertSessionHas('success');
		
		$this->assertDatabaseHas('tasks', [
			'id' => $task->id,
			'title' => 'New Title',
		]);
	}

	public function test_user_can_delete_own_task(): void
	{
		$user = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $user->id]);

		$response = $this->actingAs($user)->delete("/tasks/{$task->id}");

		$response->assertRedirect('/tasks');
		$response->assertSessionHas('success');
		
		$this->assertDatabaseMissing('tasks', ['id' => $task->id]);
	}

	public function test_user_cannot_delete_other_user_task(): void
	{
		$user = User::factory()->create();
		$otherUser = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $otherUser->id]);

		$response = $this->actingAs($user)->delete("/tasks/{$task->id}");

		$response->assertForbidden();
	}
}

