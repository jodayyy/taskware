<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestProject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class GuestProjectRepository implements GuestProjectRepositoryInterface
{
	/**
	 * Cache TTL in seconds (5 minutes).
	 */
	private const CACHE_TTL = 300;

	/**
	 * Get paginated projects for a guest user.
	 */
	public function getForGuest(string $guestId, int $perPage = 20): LengthAwarePaginator
	{
		return GuestProject::where('guest_id', $guestId)
			->select(['id', 'title', 'description', 'created_at'])
			->with('guestUser')
			->latest()
			->paginate($perPage);
	}

	/**
	 * Get recent projects for a guest user (for dashboard).
	 */
	public function getRecentForGuest(string $guestId, int $limit = 5): Collection
	{
		$cacheKey = "guest.{$guestId}.projects.recent.{$limit}";
		
		return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($guestId, $limit) {
			return GuestProject::where('guest_id', $guestId)
				->with('guestUser')
				->latest()
				->take($limit)
				->get();
		});
	}

	/**
	 * Find a project by ID and guest ID.
	 */
	public function findByIdAndGuest(int $id, string $guestId): ?GuestProject
	{
		return GuestProject::where('id', $id)
			->where('guest_id', $guestId)
			->first();
	}

	/**
	 * Create a new guest project.
	 */
	public function create(array $data): GuestProject
	{
		$project = GuestProject::create($data);
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($project->guest_id);
		
		return $project;
	}

	/**
	 * Update a guest project.
	 */
	public function update(GuestProject $project, array $data): bool
	{
		$result = $project->update($data);
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($project->guest_id);
		
		return $result;
	}

	/**
	 * Delete a guest project.
	 */
	public function delete(GuestProject $project): bool
	{
		$guestId = $project->guest_id;
		$result = $project->delete();
		
		// Invalidate cache for this guest
		$this->invalidateGuestCache($guestId);
		
		return $result;
	}

	/**
	 * Invalidate all cache entries for a guest.
	 */
	private function invalidateGuestCache(string $guestId): void
	{
		// Invalidate recent projects cache (used by dashboard)
		Cache::forget("guest.{$guestId}.projects.recent.5");
	}
}

