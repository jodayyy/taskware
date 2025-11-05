<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
	public function __construct(
		private readonly TaskRepositoryInterface $taskRepository,
		private readonly ProjectRepositoryInterface $projectRepository
	) {
	}

	public function index(): View
	{
		$user = Auth::user();
		$tasks = $this->taskRepository->getRecentForUser($user->id);
		$projects = $this->projectRepository->getRecentForUser($user->id);
		
		// Calculate statistics
		$totalProjects = Project::where('user_id', $user->id)->count();
		$totalTasks = Task::where('user_id', $user->id)->count();
		$inProgressTasks = Task::where('user_id', $user->id)
			->where('status', TaskStatus::IN_PROGRESS->value)
			->count();
		$urgentTasks = Task::where('user_id', $user->id)
			->where('priority', TaskPriority::URGENT->value)
			->count();
		
		return view('user.dashboard.dashboard', [
			'user' => $user,
			'tasks' => $tasks,
			'projects' => $projects,
			'totalProjects' => $totalProjects,
			'totalTasks' => $totalTasks,
			'inProgressTasks' => $inProgressTasks,
			'urgentTasks' => $urgentTasks,
		]);
	}
}