<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsAdmin
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
        $user = Auth::user();

        if (is_null($user))
            return abort(403);

        /** @var User $user */
        if (!$user->is_admin)
            return abort(403);

        return $next($request);
    }
}
