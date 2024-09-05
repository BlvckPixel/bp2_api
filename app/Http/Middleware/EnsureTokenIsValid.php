<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $apiToken = $request->header('Authorization');

        if (!$apiToken) {
            return response()->json(['error' => 'API token missing'], 401);
        }

        $user = User::where('api_token', $apiToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Set the authenticated user
        Auth::setUser($user);

        return $next($request);
    }
}
