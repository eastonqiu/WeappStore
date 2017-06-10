<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use JWTAuth;
use Illuminate\Http\Request;

class WechatController extends Controller
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    const PLATFORM_WECHAT = 1;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function auth(Request $request)
    {
        $wechatUser = session('wechat.oauth_user'); // 拿到授权用户资料
        //dd($wechatUser);

        //dd($wechatUser);

        $user = User::where('platform', WechatController::PLATFORM_WECHAT)->where('openid', $wechatUser->id)->first();
        // dd($wechatUser->id);
        if(empty($user)) {
            // new user
            $user = [
                'name' => $wechatUser->nickname,
                'password' => bcrypt(str_random(20)),
                'email' => str_random(20),
                'openid' => $wechatUser->id,
                'platform' => WechatController::PLATFORM_WECHAT,
                'nickname' => $wechatUser->nickname,
                'avatar' => $wechatUser->avatar,
                'sex' => $wechatUser->original['sex'],
                'country' => $wechatUser->original['country'],
                'province' => $wechatUser->original['province'],
                'city' => $wechatUser->original['city'],
            ];
            $user = User::create($user);
        }

        session()->forget('wechat.oauth_user');
        if($request->input('callback')) {
            return redirect($request->input('callback'))->withCookie(cookie('token', JWTAuth::fromUser($user)));
        }
        return 'auth success';
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.');

        $wechat = app('wechat');
        $wechat->server->setMessageHandler(function($message){
            return "欢迎关注 overtrue！";
        });

        Log::info('return response.');

        return $wechat->server->serve();
    }
}