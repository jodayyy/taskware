<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthGuard
{
    /**
     * Handle an incoming request - ensure user is authenticated.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('welcome')->with('error', 'Please login to continue.');
        }
        
        return $next($request);
    }
}