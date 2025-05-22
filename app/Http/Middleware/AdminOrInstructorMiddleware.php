<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminOrInstructorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and has an admin role
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'instructor'])) {
            return $next($request);
        }

        // Redirect to login or unauthorized page if not an admin
        return redirect()->route('login')->withErrors(['error' => 'You need to login first.']);
    }
}
