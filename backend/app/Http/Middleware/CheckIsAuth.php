<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsAuth
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @param Closure $next
     *
     * @return string
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return abort(403);
        }

        return $next($request);
    }
}
