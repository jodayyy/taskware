<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\TaskRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
	public function __construct(
		private readonly TaskRepositoryInterface $taskRepository
	) {
	}

	public function index(): View
	{
		$user = Auth::user();
		$tasks = $this->taskRepository->getRecentForUser($user->id);
		
		return view('user.dashboard.dashboard', [
			'user' => $user,
			'tasks' => $tasks
		]);
	}
}