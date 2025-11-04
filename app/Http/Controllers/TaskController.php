<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
	/**
	 * Display a listing of tasks for the authenticated user.
	 */
	public function index(Request $request)
	{
		$user = Auth::user();
		$tasks = $user->tasks()
			->select(['id','title','status','deadline','priority','created_at'])
			->latest()
			->paginate(20);
		
		if ($request->wantsJson()) {
			return response()->json($tasks);
		}
		
		return view('user.tasks.index', compact('user', 'tasks'));
	}

	/**
	 * Store a newly created task.
	 */
	public function store(StoreTaskRequest $request)
	{
		$validated = $request->validated();

		$task = Auth::user()->tasks()->create([
			'title' => $validated['title'],
			'description' => $validated['description'],
			'deadline' => $validated['deadline'],
			'priority' => $validated['priority'],
			'status' => 'to_do',
			'notes' => $validated['notes'] ?? null,
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
	public function show(Request $request, Task $task)
	{
		$this->authorize('view', $task);

		$user = Auth::user();
		
		if ($request->wantsJson()) {
			return response()->json([
				'task' => $task,
				'user' => $user
			]);
		}
		
		return view('user.tasks.show', compact('task', 'user'));
	}

	/**
	 * Update the specified task.
	 */
	public function update(UpdateTaskRequest $request, Task $task)
	{
		$this->authorize('update', $task);

		$validated = $request->validated();

		$task->update([
			'title' => $validated['title'],
			'description' => $validated['description'],
			'deadline' => $validated['deadline'],
			'priority' => $validated['priority'],
			'status' => $validated['status'],
			'notes' => $validated['notes'] ?? null,
		]);

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
	public function destroy(Task $task)
	{
		$this->authorize('delete', $task);

		$task->delete();

		return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
	}
}