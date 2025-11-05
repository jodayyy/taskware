<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\GuestTaskRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
	use RefreshDatabase;

	public function test_create_for_user_uses_repository(): void
	{
		$user = User::factory()->create();
		$mockRepository = Mockery::mock(TaskRepositoryInterface::class);
		
		$mockRepository->shouldReceive('create')
			->once()
			->with(Mockery::on(function ($data) use ($user) {
				return $data['user_id'] === $user->id 
					&& $data['status'] === TaskStatus::TO_DO->value;
			}))
			->andReturn(Task::factory()->make());

		$service = new TaskService($mockRepository, Mockery::mock(GuestTaskRepositoryInterface::class));
		
		$service->createForUser($user->id, [
			'title' => 'Test Task',
		]);
	}

	public function test_create_for_user_sets_default_status(): void
	{
		$user = User::factory()->create();
		$repository = new \App\Repositories\TaskRepository();
		$service = new TaskService(
			$repository,
			Mockery::mock(GuestTaskRepositoryInterface::class)
		);

		$task = $service->createForUser($user->id, [
			'title' => 'Test Task',
			'description' => 'Test Description',
			'deadline' => '2024-12-31',
			'priority' => 'normal',
		]);

		$this->assertEquals(TaskStatus::TO_DO->value, $task->status);
		$this->assertEquals($user->id, $task->user_id);
	}
}

