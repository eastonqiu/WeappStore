<?php
namespace App\Common;

use Log;
use App\Models\User;
use EasyWeChat;

class Message {

    const TEXT_COLOR = "#000000";
    const REMARK_COLOR = "#007C4C";

    public static function push($platform, $data) {
        switch($platform) {
            case User::PLATFORM_WECHAT :
                EasyWeChat::notice()->send($data);
                break;
            default:
                Log::error("unsupport platform {$platform} to push msg");
                break;
        }
        Log::debug("finish push msg");
    }

    public static function borrow($platform, $data) {
        $msg = [];
        if($platform == User::PLATFORM_WECHAT) {
            $msg = [
                'touser' => $data['openid'],
                'template_id' => env('WECHAT_TEMPLATE_BORROW'),
                'url' => '',
                'data' => [
    				"first" => ["value" => "", "color" => self::TEXT_COLOR],
    				"keyword1" => ["value" => $data['borrow_station_name'], "color" => self::TEXT_COLOR],
    				"keyword2" => ["value" => date('Y-m-d H:i:s', $data['borrow_time']), "color" => self::TEXT_COLOR],
    				"keyword3" => ["value" => $data['orderid'], "color" => self::TEXT_COLOR],
    				"remark" => ["value" => "欢迎使用, 如有疑问请拨打400", "color" => self::REMARK_COLOR]
                ]
            ];
        } else {

        }
        self::push($platform, $msg);
    }

    public static function return($platform, $data) {
        $msg = [];
        if($platform == User::PLATFORM_WECHAT) {
            $msg = [
                'touser' => $data['openid'],
                'template_id' => env('WECHAT_TEMPLATE_RETURN'),
                'url' => '',
                'data' => [
    				"first" => ["value" => "", "color" => self::TEXT_COLOR],
    				"keyword1" => ["value" => $data['return_station_name'], "color" => self::TEXT_COLOR],
    				"keyword2" => ["value" => date('Y-m-d H:i:s', $data['return_time']), "color" => self::TEXT_COLOR],
    				"keyword3" => ["value" => $data['use_time'] . '秒', "color" => self::TEXT_COLOR],
    				"keyword4" => ["value" => $data['orderid'], "color" => self::TEXT_COLOR],
    				"remark" => ["value" => "此次租借产生费用{$data['usefee']}元，点击详情提取剩余押金。如有疑问，请致电。", "color" => self::REMARK_COLOR]
                ]
            ];
        } else {

        }
        self::push($platform, $msg);
    }

    public static function fail($platform, $data) {
        $msg = [];
        if($platform == User::PLATFORM_WECHAT) {
            $msg = [
                'touser' => $data['openid'],
                'template_id' => env('WECHAT_TEMPLATE_FAIL'),
                'url' => '',
                'data' => [
    				"first" => ["value" => '', "color" => self::TEXT_COLOR],
    				"keyword1" => ["value" => $data['orderid'], "color" => self::TEXT_COLOR],
    				"keyword2" => ["value" => "用户中心", "color" => self::TEXT_COLOR],
    				"remark" => ["value" => "非常抱歉，您此次租借未成功,如有疑问，请致电400。", "color" => self::REMARK_COLOR]
                ]
            ];
        } else {

        }
        self::push($platform, $msg);
    }

    public static function withdraw($platform, $data) {
        $msg = [];
        if($platform == User::PLATFORM_WECHAT) {
            $msg = [
                'touser' => $data['openid'],
                'template_id' => env('WECHAT_TEMPLATE_WITHDRAW'),
                'url' => '',
                'data' => [
    				"first" => ["value" => "提现申请通知！ ", "color" => self::TEXT_COLOR],
    				"keyword1" => ["value" => ($data['widthdraw'] . '元'), "color" => self::TEXT_COLOR],
    				"keyword2" => ["value" => date('Y-m-d H:i:s', time()), "color" => self::TEXT_COLOR],
    				"remark" => ["value" => "您好！发起提现后，款项将原路退回您原支付账户。点击详情查看提现记录。", "color" => self::REMARK_COLOR]
                ]
            ];
        } else {

        }
        self::push($platform, $msg);
    }
}
