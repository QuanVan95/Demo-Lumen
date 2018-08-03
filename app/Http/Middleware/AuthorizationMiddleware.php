<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizationMiddleware
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
        if ($request->input('app_id') != 'APP001' || $request->input('app_key') != '88e436ac6c9423d946ba02d59c6a2637' ) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }
}


