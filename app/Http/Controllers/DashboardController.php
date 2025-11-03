<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
	public function index()
	{
		$user = Auth::user();
		$tasks = $user->tasks()->latest()->take(5)->get();
		
		return view('user.dashboard.dashboard', [
			'user' => $user,
			'tasks' => $tasks
		]);
	}
}