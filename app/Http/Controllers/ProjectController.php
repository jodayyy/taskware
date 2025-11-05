<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
	public function __construct(
		private readonly ProjectRepositoryInterface $projectRepository,
		private readonly ProjectService $projectService
	) {
	}

	/**
	 * Show the form for creating a new project.
	 */
	public function create(): View
	{
		$user = Auth::user();
		return view('user.projects.create', compact('user'));
	}

	/**
	 * Display a listing of projects for the authenticated user.
	 */
	public function index(Request $request): View|JsonResponse
	{
		$user = Auth::user();
		$projects = $this->projectRepository->getForUser($user->id);
		
		if ($request->wantsJson()) {
			return response()->json($projects);
		}
		
		return view('user.projects.index', compact('user', 'projects'));
	}

	/**
	 * Store a newly created project.
	 */
	public function store(StoreProjectRequest $request): RedirectResponse|JsonResponse
	{
		$validated = $request->validated();
		$user = Auth::user();

		$project = $this->projectService->createForUser($user->id, [
			'title' => $validated['title'],
			'description' => $validated['description'],
		]);

		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Project created successfully!',
				'project' => $project
			]);
		}

		return redirect()->route('projects.index')->with('success', 'Project created successfully!');
	}

	/**
	 * Display the specified project.
	 */
	public function show(Request $request, Project $project): View|JsonResponse
	{
		$this->authorize('view', $project);

		$user = Auth::user();
		$project->load('tasks');
		
		if ($request->wantsJson()) {
			return response()->json([
				'project' => $project,
				'user' => $user
			]);
		}
		
		return view('user.projects.show', compact('project', 'user'));
	}

	/**
	 * Update the specified project.
	 */
	public function update(UpdateProjectRequest $request, Project $project): RedirectResponse|JsonResponse
	{
		$this->authorize('update', $project);

		$validated = $request->validated();

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

		return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
	}

	/**
	 * Remove the specified project.
	 */
	public function destroy(Project $project): RedirectResponse
	{
		$this->authorize('delete', $project);

		$this->projectRepository->delete($project);

		return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
	}
}

