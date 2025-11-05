<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\TaskRepositoryInterface;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
	public function __construct(
		private readonly TaskRepositoryInterface $taskRepository,
		private readonly ProjectRepositoryInterface $projectRepository,
		private readonly TaskService $taskService
	) {
	}

	/**
	 * Show the form for creating a new task.
	 */
	public function create(): View
	{
		$user = Auth::user();
		$projects = $this->projectRepository->getForUser($user->id, 1000)->items();
		return view('user.tasks.create', [
			'user' => $user,
			'projects' => collect($projects),
		]);
	}

	/**
	 * Display a listing of tasks for the authenticated user.
	 */
	public function index(Request $request): View|JsonResponse
	{
		$user = Auth::user();
		$tasks = $this->taskRepository->getForUser($user->id);
		
		if ($request->wantsJson()) {
			return response()->json($tasks);
		}
		
		return view('user.tasks.index', compact('user', 'tasks'));
	}

	/**
	 * Store a newly created task.
	 */
	public function store(StoreTaskRequest $request): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
		$user = Auth::user();

		$task = $this->taskService->createForUser($user->id, [
			'title' => $validated['title'],
			'description' => $validated['description'],
			'deadline' => $validated['deadline'],
			'priority' => $validated['priority'],
			'notes' => $validated['notes'] ?? null,
			'project_id' => !empty($validated['project_id']) ? $validated['project_id'] : null,
		]);

		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Task created successfully!',
				'task' => $task
			]);
		}

		return redirect()->route('dashboard')->with('success', 'Task created successfully!');
	}

	/**
	 * Display the specified task.
	 */
	public function show(Request $request, Task $task): View|JsonResponse
	{
		$this->authorize('view', $task);

		$user = Auth::user();
		
		if ($request->wantsJson()) {
			return response()->json([
				'task' => $task,
				'user' => $user
			]);
		}
		
		$projects = $this->projectRepository->getForUser($user->id, 1000)->items();
		return view('user.tasks.show', [
			'task' => $task,
			'user' => $user,
			'projects' => collect($projects),
		]);
	}

	/**
	 * Update the specified task.
	 */
	public function update(UpdateTaskRequest $request, Task $task): RedirectResponse|JsonResponse
	{
		$this->authorize('update', $task);

		$validated = $request->validated();

		$this->taskService->updateTask($task, [
			'title' => $validated['title'],
			'description' => $validated['description'],
			'deadline' => $validated['deadline'],
			'priority' => $validated['priority'],
			'status' => $validated['status'],
			'notes' => $validated['notes'] ?? null,
			'project_id' => !empty($validated['project_id']) ? $validated['project_id'] : null,
		]);

		$task->refresh();

		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Task updated successfully!',
				'task' => $task
			]);
		}

		return redirect()->route('tasks.show', $task)->with('success', 'Task updated successfully!');
	}

	/**
	 * Remove the specified task.
	 */
	public function destroy(Task $task): RedirectResponse
	{
		$this->authorize('delete', $task);

		$this->taskRepository->delete($task);

		return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
	}
}