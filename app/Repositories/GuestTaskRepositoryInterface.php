<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\GuestTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GuestTaskRepositoryInterface
{
	/**
	 * Get paginated tasks for a guest user.
	 */
	public function getForGuest(string $guestId, int $perPage = 20): LengthAwarePaginator;

	/**
	 * Get recent tasks for a guest user (for dashboard).
	 */
	public function getRecentForGuest(string $guestId, int $limit = 5): Collection;

	/**
	 * Find a task by ID and guest ID.
	 */
	public function findByIdAndGuest(int $id, string $guestId): ?GuestTask;

	/**
	 * Create a new guest task.
	 */
	public function create(array $data): GuestTask;

	/**
	 * Update a guest task.
	 */
	public function update(GuestTask $task, array $data): bool;

	/**
	 * Delete a guest task.
	 */
	public function delete(GuestTask $task): bool;
}

