<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\GuestTask;
use App\Repositories\GuestTaskRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;

class TaskService
{
	public function __construct(
		private readonly TaskRepositoryInterface $taskRepository,
		private readonly GuestTaskRepositoryInterface $guestTaskRepository
	) {
	}

	/**
	 * Create a task for an authenticated user.
	 */
	public function createForUser(int $userId, array $data): Task
	{
		$defaults = [
			'user_id' => $userId,
			'status' => TaskStatus::TO_DO->value,
		];

		return $this->taskRepository->create(array_merge($defaults, $data));
	}

	/**
	 * Create a task for a guest user.
	 */
	public function createForGuest(string $guestId, array $data): GuestTask
	{
		$defaults = [
			'guest_id' => $guestId,
			'status' => TaskStatus::TO_DO->value,
		];

		return $this->guestTaskRepository->create(array_merge($defaults, $data));
	}

	/**
	 * Update a task (works for both Task and GuestTask).
	 *
	 * @param Task|GuestTask $task
	 */
	public function updateTask(Task|GuestTask $task, array $data): bool
	{
		if ($task instanceof Task) {
			return $this->taskRepository->update($task, $data);
		}

		return $this->guestTaskRepository->update($task, $data);
	}
}

