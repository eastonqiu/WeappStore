<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;
use Dingo\Api\Routing\Helpers;
use App\Common\Errors;
use App\Models\User;

class Authenticate
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
    public function handle($request, Closure $next, $guard = null) {
        if(! empty(session('user'))) {
            return $next($request);
        }

        $wechatUser = session('wechat.oauth_user'); // 拿到授权用户资料
        if(empty($wechatUser)) {
            return response(view('auth.fail'));
        }
        // dd($wechatUser);

        //dd($wechatUser);

        $user = User::where('openid', $wechatUser->id)->first();
        // dd($wechatUser->id);
        if(empty($user)) {
            // new user
            $user = [
                'name' => str_random(20),
                'password' => bcrypt(str_random(20)),
                'email' => str_random(20),
                'openid' => $wechatUser->id,
                'platform' => User::PLATFORM_WECHAT,
                // 'nickname' => $wechatUser->nickname,
                // 'avatar' => $wechatUser->avatar,
                // 'sex' => $wechatUser->original['sex'],
                // 'country' => $wechatUser->original['country'],
                // 'province' => $wechatUser->original['province'],
                // 'city' => $wechatUser->original['city'],
            ];
            $user = User::create($user);
        }

        session(["user_id" => $user['id']]);

        return $next($request);
    }
}
