<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
	public function showLogin()
	{
		return view('authentication.login');
	}

	public function showRegister()
	{
		return view('authentication.register');
	}

	public function login(Request $request)
	{
		$request->validate([
			'username' => 'required|string',
			'password' => 'required|string',
		]);

		$credentials = $request->only('username', 'password');

		if (Auth::attempt($credentials, $request->boolean('remember'))) {
			$request->session()->regenerate();
			
			return redirect()->intended('/dashboard');
		}

		throw ValidationException::withMessages([
			'username' => ['The provided credentials do not match our records.'],
		]);
	}

	public function register(Request $request)
	{
		$request->validate([
			'username' => 'required|string|max:255|unique:users',
			'password' => 'required|string|min:6|confirmed',
		]);

		$user = User::create([
			'username' => $request->username,
			'password' => Hash::make($request->password),
		]);

		return redirect()->route('login')->with('success', 'Registration successful! Please login.');
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		
		return redirect()->route('welcome');
	}
}