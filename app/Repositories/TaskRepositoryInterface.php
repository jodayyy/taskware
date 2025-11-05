<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
	/**
	 * Get paginated tasks for a user.
	 */
	public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator;

	/**
	 * Get recent tasks for a user (for dashboard).
	 */
	public function getRecentForUser(int $userId, int $limit = 5): Collection;

	/**
	 * Find a task by ID.
	 */
	public function findById(int $id): ?Task;

	/**
	 * Create a new task.
	 */
	public function create(array $data): Task;

	/**
	 * Update a task.
	 */
	public function update(Task $task, array $data): bool;

	/**
	 * Delete a task.
	 */
	public function delete(Task $task): bool;
}

