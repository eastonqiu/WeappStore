<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;
use Dingo\Api\Routing\Helpers;
use App\Common\Errors;

class MaintainAuthenticated
{
    use Helpers;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // check device and maintain permission

        return $next($request);
    }
}
