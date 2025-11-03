<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
	/**
	 * Display a listing of tasks for the authenticated user.
	 */
	public function index()
	{
		$user = Auth::user();
		$tasks = $user->tasks()->latest()->get();
		
		return view('user.tasks.index', compact('user', 'tasks'));
	}

	/**
	 * Store a newly created task.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'deadline' => 'required|date',
			'priority' => 'required|in:low,normal,urgent',
			'notes' => 'nullable|string',
		]);

		$task = Auth::user()->tasks()->create([
			'title' => $request->title,
			'description' => $request->description,
			'deadline' => $request->deadline,
			'priority' => $request->priority,
			'status' => 'to_do',
			'notes' => $request->notes,
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
	public function show(Task $task)
	{
		// Ensure the task belongs to the authenticated user
		if ($task->user_id !== Auth::id()) {
			abort(404);
		}

		$user = Auth::user();
		return view('user.tasks.task-details', compact('task', 'user'));
	}

	/**
	 * Update the specified task.
	 */
	public function update(Request $request, Task $task)
	{
		// Ensure the task belongs to the authenticated user
		if ($task->user_id !== Auth::id()) {
			abort(404);
		}

		$request->validate([
			'title' => 'required|string|max:255',
			'description' => 'required|string',
			'deadline' => 'required|date',
			'priority' => 'required|in:low,normal,urgent',
			'status' => 'required|in:to_do,in_progress,done',
			'notes' => 'nullable|string',
		]);

		$task->update([
			'title' => $request->title,
			'description' => $request->description,
			'deadline' => $request->deadline,
			'priority' => $request->priority,
			'status' => $request->status,
			'notes' => $request->notes,
		]);

		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Task updated successfully!',
				'task' => $task
			]);
		}

		return redirect()->route('tasks.task-details', $task)->with('success', 'Task updated successfully!');
	}

	/**
	 * Remove the specified task.
	 */
	public function destroy(Task $task)
	{
		// Ensure the task belongs to the authenticated user
		if ($task->user_id !== Auth::id()) {
			abort(404);
		}

		$task->delete();

		return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
	}
}