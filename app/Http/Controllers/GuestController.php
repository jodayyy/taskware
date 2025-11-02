<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuestController extends Controller
{
    /**
     * Start a guest session
     */
    public function start(Request $request)
    {
        // Check if there's an existing guest ID from browser storage
        $existingGuestId = $request->input('existing_guest_id');
        
        // Create guest tables if they don't exist
        $this->createGuestTables();
        
        // Occasionally clean up old guest users (5% chance)
        if (rand(1, 100) <= 5) {
            $this->cleanupOldGuestUsers();
        }
        
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
        

        
        return view('user.dashboard.dashboard', [
            'user' => (object) $guestUser,
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
     * Create guest database tables
     */
    private function createGuestTables()
    {
        $connection = DB::connection('guest_sqlite');
        
        if (!Schema::connection('guest_sqlite')->hasTable('guest_users')) {
            Schema::connection('guest_sqlite')->create('guest_users', function ($table) {
                $table->id();
                $table->string('guest_id')->unique();
                $table->string('username')->default('Guest');
                $table->timestamps();
            });
        }
        
        // Add other guest tables as needed for tasks, etc.
    }
    
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
        $this->createGuestTables(); // Ensure tables exist
        
        return DB::connection('guest_sqlite')->table('guest_users')
            ->where('guest_id', $guestId)
            ->exists();
    }
    
    /**
     * Clean up old guest users (older than 30 days)
     */
    private function cleanupOldGuestUsers()
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        DB::connection('guest_sqlite')->table('guest_users')
            ->where('updated_at', '<', $thirtyDaysAgo)
            ->delete();
    }
    

}