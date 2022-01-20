<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!auth()->guard('sanctum')->guest()) {
            return response()->json([
                'message' => 'Bad request.'
            ], 400);
        }

        return $next($request);
    }
}
