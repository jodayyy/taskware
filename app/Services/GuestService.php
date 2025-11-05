<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GuestUser;
use Illuminate\Http\Request;

class GuestService
{
	/**
	 * Start or resume a guest session.
	 *
	 * @return string The guest ID
	 */
	public function startSession(Request $request): string
	{
		$existingGuestId = $request->input('existing_guest_id');

		if ($existingGuestId && $this->guestExists($existingGuestId)) {
			// Resume existing guest session
			return $existingGuestId;
		}

		// For new guests, check if localStorage provided an ID first
		if ($existingGuestId) {
			// If localStorage has an ID but it doesn't exist in DB, recreate it
			if (!$this->guestExists($existingGuestId)) {
				$this->createGuestUser($existingGuestId);
			}
			return $existingGuestId;
		}

		// Create a new guest ID using browser fingerprint for consistency
		$browserFingerprint = substr(md5($request->userAgent() . '_' . $request->ip()), 0, 16);
		$guestId = 'guest_' . $browserFingerprint;

		// Check if this browser already has a guest user, if so reuse it
		if (!$this->guestExists($guestId)) {
			$this->createGuestUser($guestId);
		}

		return $guestId;
	}

	/**
	 * Get or create a guest user.
	 */
	public function getOrCreateGuest(string $guestId): GuestUser
	{
		return GuestUser::firstOrCreate(
			['guest_id' => $guestId],
			['username' => 'Guest']
		);
	}

	/**
	 * Get guest user by ID.
	 */
	public function getGuestUser(string $guestId): ?GuestUser
	{
		return GuestUser::where('guest_id', $guestId)->first();
	}

	/**
	 * Check if guest user exists.
	 */
	public function guestExists(string $guestId): bool
	{
		return GuestUser::where('guest_id', $guestId)->exists();
	}

	/**
	 * Create a guest user record.
	 */
	private function createGuestUser(string $guestId): GuestUser
	{
		return GuestUser::create([
			'guest_id' => $guestId,
			'username' => 'Guest',
		]);
	}
}

