<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TaskRepositoryTest extends TestCase
{
	use RefreshDatabase;

	private TaskRepository $repository;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new TaskRepository();
		Cache::flush();
	}

	public function test_get_for_user_returns_paginated_tasks(): void
	{
		$user = User::factory()->create();
		Task::factory()->count(25)->create(['user_id' => $user->id]);

		$result = $this->repository->getForUser($user->id, 20);

		$this->assertCount(20, $result->items());
		$this->assertEquals(25, $result->total());
	}

	public function test_get_recent_for_user_returns_limited_tasks(): void
	{
		$user = User::factory()->create();
		Task::factory()->count(10)->create(['user_id' => $user->id]);

		$result = $this->repository->getRecentForUser($user->id, 5);

		$this->assertCount(5, $result);
	}

	public function test_create_task_stores_in_database(): void
	{
		$user = User::factory()->create();

		$task = $this->repository->create([
			'user_id' => $user->id,
			'title' => 'Test Task',
			'description' => 'Test Description',
			'deadline' => '2024-12-31',
			'priority' => 'normal',
			'status' => 'to_do',
		]);

		$this->assertDatabaseHas('tasks', [
			'id' => $task->id,
			'title' => 'Test Task',
		]);
	}

	public function test_create_invalidates_cache(): void
	{
		$user = User::factory()->create();
		$cacheKey = "user.{$user->id}.tasks.recent.5";
		
		// Prime the cache
		Cache::remember($cacheKey, 300, fn() => collect());
		$this->assertTrue(Cache::has($cacheKey));

		$this->repository->create([
			'user_id' => $user->id,
			'title' => 'Test Task',
			'description' => 'Test Description',
			'deadline' => '2024-12-31',
			'priority' => 'normal',
			'status' => 'to_do',
		]);

		// Cache should be invalidated
		$this->assertFalse(Cache::has($cacheKey));
	}

	public function test_update_task_modifies_database(): void
	{
		$user = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $user->id]);

		$result = $this->repository->update($task, ['title' => 'Updated Title']);

		$this->assertTrue($result);
		$this->assertDatabaseHas('tasks', [
			'id' => $task->id,
			'title' => 'Updated Title',
		]);
	}

	public function test_delete_task_removes_from_database(): void
	{
		$user = User::factory()->create();
		$task = Task::factory()->create(['user_id' => $user->id]);

		$result = $this->repository->delete($task);

		$this->assertTrue($result);
		$this->assertDatabaseMissing('tasks', ['id' => $task->id]);
	}
}

