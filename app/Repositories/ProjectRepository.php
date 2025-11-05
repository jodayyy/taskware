<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProjectRepository implements ProjectRepositoryInterface
{
	/**
	 * Cache TTL in seconds (5 minutes).
	 */
	private const CACHE_TTL = 300;

	/**
	 * Get paginated projects for a user.
	 */
	public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator
	{
		return Project::where('user_id', $userId)
			->select(['id', 'title', 'description', 'created_at'])
			->with('user')
			->latest()
			->paginate($perPage);
	}

	/**
	 * Get recent projects for a user (for dashboard).
	 */
	public function getRecentForUser(int $userId, int $limit = 5): Collection
	{
		$cacheKey = "user.{$userId}.projects.recent.{$limit}";
		
		return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $limit) {
			return Project::where('user_id', $userId)
				->with('user')
				->latest()
				->take($limit)
				->get();
		});
	}

	/**
	 * Find a project by ID.
	 */
	public function findById(int $id): ?Project
	{
		return Project::find($id);
	}

	/**
	 * Create a new project.
	 */
	public function create(array $data): Project
	{
		$project = Project::create($data);
		
		// Invalidate cache for this user
		$this->invalidateUserCache($project->user_id);
		
		return $project;
	}

	/**
	 * Update a project.
	 */
	public function update(Project $project, array $data): bool
	{
		$result = $project->update($data);
		
		// Invalidate cache for this user
		$this->invalidateUserCache($project->user_id);
		
		return $result;
	}

	/**
	 * Delete a project.
	 */
	public function delete(Project $project): bool
	{
		$userId = $project->user_id;
		$result = $project->delete();
		
		// Invalidate cache for this user
		$this->invalidateUserCache($userId);
		
		return $result;
	}

	/**
	 * Invalidate all cache entries for a user.
	 */
	private function invalidateUserCache(int $userId): void
	{
		// Invalidate recent projects cache (used by dashboard)
		Cache::forget("user.{$userId}.projects.recent.5");
	}
}

