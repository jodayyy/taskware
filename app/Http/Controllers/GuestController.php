<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestProjectRequest;
use App\Http\Requests\StoreGuestTaskRequest;
use App\Http\Requests\UpdateGuestProjectRequest;
use App\Http\Requests\UpdateGuestTaskRequest;
use App\Models\GuestProject;
use App\Models\GuestTask;
use App\Models\GuestUser;
use App\Repositories\GuestProjectRepositoryInterface;
use App\Repositories\GuestTaskRepositoryInterface;
use App\Services\GuestService;
use App\Services\ProjectService;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuestController extends Controller
{
	public function __construct(
		private readonly GuestService $guestService,
		private readonly GuestTaskRepositoryInterface $guestTaskRepository,
		private readonly GuestProjectRepositoryInterface $guestProjectRepository,
		private readonly TaskService $taskService,
		private readonly ProjectService $projectService
	) {
	}

	/**
	 * Start a guest session
	 */
	public function start(Request $request): RedirectResponse
	{
		$guestId = $this->guestService->startSession($request);
		
		// Store guest ID in session
		session(['guest_id' => $guestId, 'is_guest' => true]);
		
		return redirect()->route('guest.dashboard');
	}
	
	/**
	 * Show guest dashboard
	 */
	public function dashboard(): View|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		// Get recent tasks for guest user
		$tasks = $this->guestTaskRepository->getRecentForGuest($guestId);
		$projects = $this->guestProjectRepository->getRecentForGuest($guestId);
			
		return view('user.dashboard.dashboard', [
			'user' => $guestUser,
			'tasks' => $tasks,
			'projects' => $projects,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Show guest profile settings
	 */
	public function showProfile(): View|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		return view('user.settings.profile', [
			'user' => $guestUser,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Update guest profile
	 */
	public function updateProfile(Request $request): RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$request->validate([
			'username' => 'required|string|max:255',
		]);
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$guestUser->update([
			'username' => $request->username,
		]);
		
		return back()->with('success', 'Profile updated successfully!');
	}
	
	/**
	 * End guest session (but preserve data)
	 */
	public function logout(Request $request): RedirectResponse
	{
		// Only clear session data, not the guest user data from database
		$request->session()->forget(['guest_id', 'is_guest']);
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		
		return redirect()->route('welcome');
	}

	/**
	 * Display tasks for guest user
	 */
	public function indexTasks(Request $request): View|JsonResponse|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$tasks = $this->guestTaskRepository->getForGuest($guestId);
		
		if ($request->wantsJson()) {
			return response()->json($tasks);
		}
		
		return view('user.tasks.index', [
			'user' => $guestUser,
			'tasks' => $tasks,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}

	/**
	 * Show the form for creating a new task for guest user
	 */
	public function createTask(): View|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$projects = $this->guestProjectRepository->getForGuest($guestId, 1000)->items(); // Get all projects
		
		return view('user.tasks.create', [
			'user' => $guestUser,
			'projects' => collect($projects),
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}

	/**
	 * Store a new task for guest user
	 */
	public function storeTask(StoreGuestTaskRequest $request): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		$task = $this->taskService->createForGuest($guestId, [
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
			
		return redirect()->route('guest.dashboard')->with('success', 'Task created successfully!');
	}
	
	/**
	 * Display a specific task for guest user
	 */
	public function showTask(Request $request, int $id): View|JsonResponse|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		$task = $this->guestTaskRepository->findByIdAndGuest($id, $guestId);
		
		if (!$task) {
			abort(404);
		}
		
		if ($request->wantsJson()) {
			return response()->json([
				'task' => $task,
				'user' => $guestUser
			]);
		}
			
		$projects = $this->guestProjectRepository->getForGuest($guestId, 1000)->items(); // Get all projects
		
		return view('user.tasks.show', [
			'user' => $guestUser,
			'task' => $task,
			'projects' => collect($projects),
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Update a task for guest user
	 */
	public function updateTask(UpdateGuestTaskRequest $request, int $id): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$task = $this->guestTaskRepository->findByIdAndGuest($id, $guestId);
		
		if (!$task) {
			abort(404);
		}
		
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
		
		return redirect()->route('guest.tasks.task-details', $id)->with('success', 'Task updated successfully!');
	}

	/**
	 * Delete a task for guest user
	 */
	public function destroyTask(int $id): RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
		
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$task = $this->guestTaskRepository->findByIdAndGuest($id, $guestId);
		
		if (!$task) {
			abort(404);
		}
		
		$this->guestTaskRepository->delete($task);
		
		return redirect()->route('guest.tasks.index')->with('success', 'Task deleted successfully!');
	}

	/**
	 * Display projects for guest user
	 */
	public function indexProjects(Request $request): View|JsonResponse|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$projects = $this->guestProjectRepository->getForGuest($guestId);
		
		if ($request->wantsJson()) {
			return response()->json($projects);
		}
		
		return view('user.projects.index', [
			'user' => $guestUser,
			'projects' => $projects,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}

	/**
	 * Show the form for creating a new project for guest user
	 */
	public function createProject(): View|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		return view('user.projects.create', [
			'user' => $guestUser,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}

	/**
	 * Store a new project for guest user
	 */
	public function storeProject(StoreGuestProjectRequest $request): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		$project = $this->projectService->createForGuest($guestId, [
			'title' => $validated['title'],
			'description' => $validated['description'],
		]);
			
		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Project created successfully!',
				'project' => $project
			]);
		}
			
		return redirect()->route('guest.projects.index')->with('success', 'Project created successfully!');
	}
	
	/**
	 * Display a specific project for guest user
	 */
	public function showProject(Request $request, int $id): View|JsonResponse|RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
			
		$project = $this->guestProjectRepository->findByIdAndGuest($id, $guestId);
		
		if (!$project) {
			abort(404);
		}
		
		$project->load('tasks');
		
		if ($request->wantsJson()) {
			return response()->json([
				'project' => $project,
				'user' => $guestUser
			]);
		}
			
		return view('user.projects.show', [
			'user' => $guestUser,
			'project' => $project,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Update a project for guest user
	 */
	public function updateProject(UpdateGuestProjectRequest $request, int $id): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$project = $this->guestProjectRepository->findByIdAndGuest($id, $guestId);
		
		if (!$project) {
			abort(404);
		}
		
		$this->projectService->updateProject($project, [
			'title' => $validated['title'],
			'description' => $validated['description'],
		]);
		
		$project->refresh();
		
		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Project updated successfully!',
				'project' => $project
			]);
		}
		
		return redirect()->route('guest.projects.show', $id)->with('success', 'Project updated successfully!');
	}

	/**
	 * Delete a project for guest user
	 */
	public function destroyProject(int $id): RedirectResponse
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
		
		$guestId = session('guest_id');
		$guestUser = $this->guestService->getGuestUser($guestId);
		
		if (!$guestUser) {
			return redirect()->route('welcome');
		}
		
		$project = $this->guestProjectRepository->findByIdAndGuest($id, $guestId);
		
		if (!$project) {
			abort(404);
		}
		
		$this->guestProjectRepository->delete($project);
		
		return redirect()->route('guest.projects.index')->with('success', 'Project deleted successfully!');
	}
}
