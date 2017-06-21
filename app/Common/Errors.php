<?php

namespace App\Common;

class Errors {
    const NORMAL = 0; // 成功/正常
    const INVALID_DEVICE_ID = 1; // 设备ID不合法
    const INVALID_ORDER_ID = 2; // 订单号不合法
    const INVALID_BATTERY_ID = 3; //电池ID不合法
    const INVALID_PARAMS = 4; // 参数不合法

    const ORDER_PAY_BY_ACCOUNT = 5; // 账户内支付
    const ORDER_PAY_NEW = 6; // 产生新的支付
    const ORDER_STOCK_NO_ENOUGH = 7; // 库存不足
    const ORDER_WECHAT_ORDER_FAIL = 8; // 微信统一下单失败

    const USER_ACCOUNT_REFUND_FAIL = 9; // 用户账号退款失败

    const USER_ACCOUNT_WITHDRAW_BALANCE_NOT_ENOUGH = 10; // 用户账号余额不足提现
    const SYSTEM_DB_ERROR = 11; // 数据库错误

    public static function error($errcode, $errmsg) {
        return ['errcode' => $errcode, 'errmsg' =>$errmsg];
    }

    public static function success($errmsg) {
        return ['errcode' => self::NORMAL, 'errmsg' =>$errmsg];
    }
}
