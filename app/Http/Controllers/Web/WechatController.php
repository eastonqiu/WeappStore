<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\BorrowOrder;
use Illuminate\Http\Request;
use EasyWeChat;

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
        Log::debug("wechat pay notify");
        $response = EasyWeChat::payment()->handleNotify(function($notify, $successful){
            $order = BorrowOrder::find($notify->out_trade_no);
            if(empty($order)) {
                Log::error("{$notify->out_trade_no} does not exist");
                return true;
            }
            Log::debug("{$notify->out_trade_no} wechat pay notify");
            // 用户是否支付成功
            if ($successful) {
                if(BorrowOrder::payNotify($notify->out_trade_no, $notify->total_fee, User::PLATFORM_WECHAT)) {
                    Log::debug("{$notify->out_trade_no} wechat pay notify successfully");
                } else {
                    Log::error("{$notify->out_trade_no} wechat pay notify process fail");
                }
            } else {
                Log::error("{$notify->out_trade_no} user wechat pay notify fail");
            }
            return true; // 统一处理成功
        });
        return $response;
    }
}
