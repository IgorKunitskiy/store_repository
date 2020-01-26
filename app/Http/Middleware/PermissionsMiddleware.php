<?php

namespace App\Http\Middleware;

use App\Models\User;
use Auth;
use Closure;
use Illuminate\Validation\UnauthorizedException;

class PermissionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        if (!$user->can('changeStatus', User::class)) {
            throw new UnauthorizedException('Only admins can edit orders statuses.');
        }
        return $next($request);
    }
}
