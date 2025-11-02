<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
	public function index()
	{
		return view('user.dashboard.dashboard', [
			'user' => Auth::user()
		]);
	}
}