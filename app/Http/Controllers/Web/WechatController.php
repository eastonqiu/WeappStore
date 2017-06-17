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
        $response = EasyWeChat::payment()->handleNotify(function($notify, $successful){
            $order = BorrowOrder::find($notify->out_trade_no);
            if(empty($order)) {
                return false;
            }
            Log::debug("{$notify->out_trade_no} wechat pay notify")
            BorrowOrder::payNotify($notify->out_trade_no);
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 'paid';
            } else { // 用户支付失败
                $order->status = 'paid_fail';
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return $response;
    }
}
