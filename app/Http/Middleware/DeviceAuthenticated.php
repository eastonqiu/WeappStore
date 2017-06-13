<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;
use Dingo\Api\Routing\Helpers;
use App\Common\Errors;

class DeviceAuthenticated
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
        // check signature

        // check device id or mac
        $device = $request->only('device_id', 'mac');
        if(Device::where('id', $device['device_id'])->orWhere('mac', $device['mac'])->count() == 0) {
            return response()->json(Errors::error(Errors::DEVICE_INVALID_ID, 'invalid device id or mac'));
        }
        return $next($request);
    }
}
