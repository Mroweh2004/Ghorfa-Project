<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LandlordMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access landlord features.');
        }

        $user = Auth::user();

        if ($user->role !== 'landlord' && $user->role !== 'admin') {
            return redirect()->route('home')
                ->with('error', 'You need to be a landlord to access this page. <a href="' . route('landlord.apply') . '">Become a Landlord</a>');
        }

        return $next($request);
    }
}
