<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestProject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GuestProjectRepositoryInterface
{
	/**
	 * Get paginated projects for a guest user.
	 */
	public function getForGuest(string $guestId, int $perPage = 20): LengthAwarePaginator;

	/**
	 * Get recent projects for a guest user (for dashboard).
	 */
	public function getRecentForGuest(string $guestId, int $limit = 5): Collection;

	/**
	 * Find a project by ID and guest ID.
	 */
	public function findByIdAndGuest(int $id, string $guestId): ?GuestProject;

	/**
	 * Create a new guest project.
	 */
	public function create(array $data): GuestProject;

	/**
	 * Update a guest project.
	 */
	public function update(GuestProject $project, array $data): bool;

	/**
	 * Delete a guest project.
	 */
	public function delete(GuestProject $project): bool;
}

