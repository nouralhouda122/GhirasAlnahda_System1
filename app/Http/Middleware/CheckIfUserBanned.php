<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIfUserBanned
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->status === 'banned') {
            return response()->json([
                'message' => 'Your account is banned. Please contact support.',
                'code' => 403
            ], 403);
        }

        return $next($request);
    }
}

