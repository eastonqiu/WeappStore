<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class WechatController extends Controller
{

    public function __construct() {
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve() {
        Log::info('request arrived.');

        $wechat = app('wechat');
        $wechat->server->setMessageHandler(function($message){
            return "欢迎关注 overtrue！";
        });

        Log::info('return response.');

        return $wechat->server->serve();
    }

    public function payNotify() {
        
    }
}
