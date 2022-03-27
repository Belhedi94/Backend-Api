<?php

namespace App\Http\Middleware;

use App\Http\ResponseMessages;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->user()->is_active)
            return response()->json([
                'message' => ResponseMessages::BANNED
            ], Response::HTTP_FORBIDDEN);
        return $next($request);
    }
}
