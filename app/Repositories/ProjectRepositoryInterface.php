<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProjectRepositoryInterface
{
	/**
	 * Get paginated projects for a user.
	 */
	public function getForUser(int $userId, int $perPage = 20): LengthAwarePaginator;

	/**
	 * Get recent projects for a user (for dashboard).
	 */
	public function getRecentForUser(int $userId, int $limit = 5): Collection;

	/**
	 * Find a project by ID.
	 */
	public function findById(int $id): ?Project;

	/**
	 * Create a new project.
	 */
	public function create(array $data): Project;

	/**
	 * Update a project.
	 */
	public function update(Project $project, array $data): bool;

	/**
	 * Delete a project.
	 */
	public function delete(Project $project): bool;
}

