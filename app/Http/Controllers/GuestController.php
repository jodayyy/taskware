<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestTaskRequest;
use App\Http\Requests\UpdateGuestTaskRequest;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuestController extends Controller
{
	/**
	 * Start a guest session
	 */
	public function start(Request $request)
	{
		// Check if there's an existing guest ID from browser storage
		$existingGuestId = $request->input('existing_guest_id');
		
        // Guest tables are managed by migrations; no schema work in request path
		
		if ($existingGuestId && $this->guestExists($existingGuestId)) {
			// Resume existing guest session
			$guestId = $existingGuestId;
		} else {
			// For new guests, check if localStorage provided an ID first
			if ($existingGuestId) {
				// If localStorage has an ID but it doesn't exist in DB, recreate it
				$guestId = $existingGuestId;
				if (!$this->guestExists($guestId)) {
					$this->createGuestUser($guestId);
				}
			} else {
				// Create a new guest ID using browser fingerprint for consistency
				$browserFingerprint = substr(md5($request->userAgent() . '_' . $request->ip()), 0, 16);
				$guestId = 'guest_' . $browserFingerprint;
				
				// Check if this browser already has a guest user, if so reuse it
				if (!$this->guestExists($guestId)) {
					// Create a new guest user record
					$this->createGuestUser($guestId);
				}
			}
		}
		
		// Store guest ID in session
		session(['guest_id' => $guestId, 'is_guest' => true]);
		
		return redirect()->route('guest.dashboard');
	}
	
	/**
	 * Show guest dashboard
	 */
	public function dashboard()
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->getGuestUser($guestId);
			
		// Get recent tasks for guest user
		$tasks = DB::connection('guest_sqlite')->table('guest_tasks')
			->where('guest_id', $guestId)
			->orderBy('created_at', 'desc')
			->limit(5)
			->get();
			
        // Convert tasks to objects with necessary properties
		$tasks = $tasks->map(function ($task) {
			return (object) [
				'id' => $task->id,
				'title' => $task->title,
				'description' => $task->description,
				'deadline' => \Carbon\Carbon::parse($task->deadline),
				'priority' => $task->priority,
				'status' => $task->status,
				'notes' => $task->notes,
                'created_at' => \Carbon\Carbon::parse($task->created_at),
                'status_label' => TaskStatus::from($task->status)->label(),
			];
		});
			
		return view('user.dashboard.dashboard', [
			'user' => (object) $guestUser,
			'tasks' => $tasks,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Show guest profile settings
	 */
	public function showProfile()
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->getGuestUser($guestId);
			
		return view('user.settings.profile', [
			'user' => (object) $guestUser,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Update guest profile
	 */
	public function updateProfile(Request $request)
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$request->validate([
			'username' => 'required|string|max:255',
		]);
			
		$guestId = session('guest_id');
		
		DB::connection('guest_sqlite')->table('guest_users')
			->where('guest_id', $guestId)
			->update([
				'username' => $request->username,
				'updated_at' => now()
			]);
		
		return back()->with('success', 'Profile updated successfully!');
	}
	
	/**
	 * End guest session (but preserve data)
	 */
	public function logout(Request $request)
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
	public function indexTasks(Request $request)
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->getGuestUser($guestId);
		
		$tasks = DB::connection('guest_sqlite')->table('guest_tasks')
			->where('guest_id', $guestId)
			->select(['id','title','status','deadline','priority','created_at'])
			->orderBy('created_at', 'desc')
			->paginate(20);
		
		// Map each item to preserve labels and Carbon instances while keeping paginator
        $tasks->getCollection()->transform(function ($task) {
			return (object) [
				'id' => $task->id,
				'title' => $task->title,
				'description' => null,
				'deadline' => Carbon::parse($task->deadline),
				'priority' => $task->priority,
				'status' => $task->status,
				'notes' => null,
                'created_at' => Carbon::parse($task->created_at),
                'status_label' => TaskStatus::from($task->status)->label(),
			];
		});
		
		if ($request->wantsJson()) {
			return response()->json($tasks);
		}
		
		return view('user.tasks.index', [
			'user' => (object) $guestUser,
			'tasks' => $tasks,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}

	/**
	 * Store a new task for guest user
	 */
	public function storeTask(StoreGuestTaskRequest $request)
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
			
		$taskId = DB::connection('guest_sqlite')->table('guest_tasks')->insertGetId([
			'guest_id' => $guestId,
			'title' => $validated['title'],
			'description' => $validated['description'],
			'deadline' => $validated['deadline'],
			'priority' => $validated['priority'],
			'status' => 'to_do',
			'notes' => $validated['notes'] ?? null,
			'created_at' => now(),
			'updated_at' => now()
		]);
			
		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Task created successfully!',
				'task_id' => $taskId
			]);
		}
			
		return redirect()->route('guest.dashboard')->with('success', 'Task created successfully!');
	}
	
	/**
	 * Display a specific task for guest user
	 */
	public function showTask(Request $request, $id)
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
			
		$guestId = session('guest_id');
		$guestUser = $this->getGuestUser($guestId);
			
		$task = DB::connection('guest_sqlite')->table('guest_tasks')
			->where('id', $id)
			->where('guest_id', $guestId)
			->first();
			
		if (!$task) {
			abort(404);
		}
		
		// Convert to object with necessary properties for the view
		$taskObj = (object) [
			'id' => $task->id,
			'title' => $task->title,
			'description' => $task->description,
			'deadline' => \Carbon\Carbon::parse($task->deadline),
			'priority' => $task->priority,
			'status' => $task->status,
			'notes' => $task->notes,
			'created_at' => \Carbon\Carbon::parse($task->created_at),
			'updated_at' => \Carbon\Carbon::parse($task->updated_at),
            'priority_label' => TaskPriority::from($task->priority)->label(),
            'status_label' => TaskStatus::from($task->status)->label(),
		];
		
		if ($request->wantsJson()) {
			return response()->json([
				'task' => $taskObj,
				'user' => $guestUser
			]);
		}
			
		return view('user.tasks.show', [
			'user' => (object) $guestUser,
			'task' => $taskObj,
			'is_guest' => true,
			'guest_id' => $guestId
		]);
	}
	
	/**
	 * Update a task for guest user
	 */
	public function updateTask(UpdateGuestTaskRequest $request, $id)
	{
		$validated = $request->validated();
			
		$guestId = session('guest_id');
		
		$updated = DB::connection('guest_sqlite')->transaction(function () use ($id, $guestId, $validated) {
			return DB::connection('guest_sqlite')->table('guest_tasks')
				->where('id', $id)
				->where('guest_id', $guestId)
				->update([
					'title' => $validated['title'],
					'description' => $validated['description'],
					'deadline' => $validated['deadline'],
					'priority' => $validated['priority'],
					'status' => $validated['status'],
					'notes' => $validated['notes'] ?? null,
					'updated_at' => now()
				]);
		});
		
		if (!$updated) {
			abort(404);
		}
		
		if ($request->wantsJson()) {
			return response()->json([
				'message' => 'Task updated successfully!'
			]);
		}
		
		return redirect()->route('guest.tasks.task-details', $id)->with('success', 'Task updated successfully!');
	}

	/**
	 * Delete a task for guest user
	 */
	public function destroyTask($id)
	{
		if (!session('is_guest')) {
			return redirect()->route('welcome');
		}
		
		$guestId = session('guest_id');
		
		$deleted = DB::connection('guest_sqlite')->table('guest_tasks')
			->where('id', $id)
			->where('guest_id', $guestId)
			->delete();
		
		if (!$deleted) {
			abort(404);
		}
		
		return redirect()->route('guest.tasks.index')->with('success', 'Task deleted successfully!');
	}
	
	/**
	 * Create guest database tables
	 */
    // Schema creation moved to migrations
	
	/**
	 * Create a guest user record
	 */
	private function createGuestUser($guestId)
	{
		DB::connection('guest_sqlite')->table('guest_users')->insert([
			'guest_id' => $guestId,
			'username' => 'Guest',
			'created_at' => now(),
			'updated_at' => now()
		]);
	}
	
	/**
	 * Get guest user data
	 */
	private function getGuestUser($guestId)
	{
		return DB::connection('guest_sqlite')->table('guest_users')
			->where('guest_id', $guestId)
			->first();
	}
	
	/**
	 * Check if guest user exists
	 */
	private function guestExists($guestId)
	{
		return DB::connection('guest_sqlite')->table('guest_users')
			->where('guest_id', $guestId)
			->exists();
	}
    
    // Cleanup moved to scheduled command
}