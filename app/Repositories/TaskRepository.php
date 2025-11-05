<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
	/**
	 * Cache TTL in seconds (5 minutes).
	 */
	private const CACHE_TTL = 300;

	/**
	 * Get paginated tasks for a user.
	 */
	public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator
	{
		// Don't cache paginated results - they change too frequently and cache invalidation is complex
		// Instead, we'll rely on the query optimization and eager loading for performance
		// For production with high traffic, consider using cache tags (Redis) for better invalidation
		return Task::where('user_id', $userId)
			->select(['id', 'title', 'status', 'deadline', 'priority', 'created_at'])
			->with('user') // Eager load user relationship
			->latest()
			->paginate($perPage);
	}

	/**
	 * Get recent tasks for a user (for dashboard).
	 */
	public function getRecentForUser(int $userId, int $limit = 5): Collection
	{
		$cacheKey = "user.{$userId}.tasks.recent.{$limit}";
		
		return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $limit) {
			return Task::where('user_id', $userId)
				->with('user') // Eager load user relationship
				->latest()
				->take($limit)
				->get();
		});
	}

	/**
	 * Find a task by ID.
	 */
	public function findById(int $id): ?Task
	{
		return Task::find($id);
	}

	/**
	 * Create a new task.
	 */
	public function create(array $data): Task
	{
		$task = Task::create($data);
		
		// Invalidate cache for this user
		$this->invalidateUserCache($task->user_id);
		
		return $task;
	}

	/**
	 * Update a task.
	 */
	public function update(Task $task, array $data): bool
	{
		$result = $task->update($data);
		
		// Invalidate cache for this user
		$this->invalidateUserCache($task->user_id);
		
		return $result;
	}

	/**
	 * Delete a task.
	 */
	public function delete(Task $task): bool
	{
		$userId = $task->user_id;
		$result = $task->delete();
		
		// Invalidate cache for this user
		$this->invalidateUserCache($userId);
		
		return $result;
	}

	/**
	 * Invalidate all cache entries for a user.
	 */
	private function invalidateUserCache(int $userId): void
	{
		// Invalidate recent tasks cache (used by dashboard)
		Cache::forget("user.{$userId}.tasks.recent.5");
	}
}

