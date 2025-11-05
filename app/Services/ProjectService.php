<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GuestProject;
use App\Models\Project;
use App\Repositories\GuestProjectRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;

class ProjectService
{
	public function __construct(
		private readonly ProjectRepositoryInterface $projectRepository,
		private readonly GuestProjectRepositoryInterface $guestProjectRepository
	) {
	}

	/**
	 * Create a project for an authenticated user.
	 */
	public function createForUser(int $userId, array $data): Project
	{
		$defaults = [
			'user_id' => $userId,
		];

		return $this->projectRepository->create(array_merge($defaults, $data));
	}

	/**
	 * Create a project for a guest user.
	 */
	public function createForGuest(string $guestId, array $data): GuestProject
	{
		$defaults = [
			'guest_id' => $guestId,
		];

		return $this->guestProjectRepository->create(array_merge($defaults, $data));
	}

	/**
	 * Update a project (works for both Project and GuestProject).
	 *
	 * @param Project|GuestProject $project
	 */
	public function updateProject(Project|GuestProject $project, array $data): bool
	{
		if ($project instanceof Project) {
			return $this->projectRepository->update($project, $data);
		}

		return $this->guestProjectRepository->update($project, $data);
	}
}

