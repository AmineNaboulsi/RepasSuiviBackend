<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearerToken = $request->bearerToken();

        if (!$bearerToken) {
            return response()->json(['message' => 'Unauthorized: No token provided'], 401);
        }

        if (!Redis::ping()) {
            return response()->json(['message' => 'Serive meal cant reveive request now, pleasse try later'], 500);
        }

        $userData = Redis::get('auth:' . $bearerToken);

        if (!$userData) {
            return response()->json(['message' => 'Unauthorized: Invalid token'], 401);
        }
        
        return $next($request);
    }
}
