<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ProfileController extends Controller
{
	/**
	 * Display the authenticated user's profile form.
	 */
	public function show(): View
	{
		return view('user.settings.profile', [
			'user' => Auth::user()
		]);
	}

	/**
	 * Update the authenticated user's profile information.
	 */
	public function update(Request $request): RedirectResponse
	{
		$user = Auth::user();
			
		$request->validate([
			'username' => [
				'required',
				'string',
				'max:255',
				Rule::unique('users')->ignore($user->id),
			],
			'current_password' => 'required_with:new_password',
			'new_password' => 'nullable|string|min:6|confirmed',
		]);

		// Wrap in transaction for data consistency
		DB::transaction(function () use ($user, $request) {
			// Update username
			$user->username = $request->username;

			// Update password if provided
			if ($request->filled('new_password')) {
				// Verify current password
				if (!Hash::check($request->current_password, $user->password)) {
					throw ValidationException::withMessages([
						'current_password' => ['The current password is incorrect.']
					]);
				}
				
				$user->password = Hash::make($request->new_password);
			}

			$user->save();
		});

		return back()->with('success', 'Profile updated successfully!');
	}
}