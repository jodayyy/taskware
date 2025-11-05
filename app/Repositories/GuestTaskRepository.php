<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GuestTaskRepository implements GuestTaskRepositoryInterface
{
	/**
	 * Cache TTL in seconds (5 minutes).
	 */
	private const CACHE_TTL = 300;

	/**
	 * Get paginated tasks for a guest user.
	 */
	public function getForGuest(string $guestId, int $perPage = 20): LengthAwarePaginator
	{
		// Don't cache paginated results - they change too frequently and cache invalidation is complex
		// Instead, we'll rely on the query optimization and eager loading for performance
		// For production with high traffic, consider using cache tags (Redis) for better invalidation
		return GuestTask::where('guest_id', $guestId)
			->select(['id', 'title', 'status', 'deadline', 'priority', 'created_at'])
			->with('guestUser') // Eager load guest user relationship
			->latest()
			->paginate($perPage);
	}

	/**
	 * Get recent tasks for a guest user (for dashboard).
	 */
	public function getRecentForGuest(string $guestId, int $limit = 5): Collection
	{
		$cacheKey = "guest.{$guestId}.tasks.recent.{$limit}";
		
		return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($guestId, $limit) {
			return GuestTask::where('guest_id', $guestId)
				->with('guestUser') // Eager load guest user relationship
				->latest()
				->take($limit)
				->get();
		});
	}

	/**
	 * Find a task by ID and guest ID.
	 */
	public function findByIdAndGuest(int $id, string $guestId): ?GuestTask
	{
		return GuestTask::where('id', $id)
			->where('guest_id', $guestId)
			->first();
	}

	/**
	 * Create a new guest task.
	 */
	public function create(array $data): GuestTask
	{
		$task = GuestTask::create($data);
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($task->guest_id);
		
		return $task;
	}

	/**
	 * Update a guest task.
	 */
	public function update(GuestTask $task, array $data): bool
	{
		$result = $task->update($data);
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($task->guest_id);
		
		return $result;
	}

	/**
	 * Delete a guest task.
	 */
	public function delete(GuestTask $task): bool
	{
		$guestId = $task->guest_id;
		$result = $task->delete();
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($guestId);
		
		return $result;
	}

	/**
	 * Invalidate all cache entries for a guest.
	 */
	private function invalidateGuestCache(string $guestId): void
	{
		// Invalidate recent tasks cache (used by dashboard)
		Cache::forget("guest.{$guestId}.tasks.recent.5");
	}
}

