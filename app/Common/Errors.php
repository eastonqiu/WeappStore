<?php

namespace App\Common;

class Errors {
    const NORMAL = 0; // 成功/正常
    const INVALID_DEVICE_ID = 1; // 设备ID不合法
    const INVALID_ORDER_ID = 2; // 订单号不合法
    const INVALID_BATTERY_ID = 3; //电池ID不合法
    const INVALID_PARAMS = 4; // 参数不合法

    public static function error($errcode, $errmsg) {
        return ['errcode' => $errcode, 'errmsg' =>$errmsg];
    }

    public static function success($errmsg) {
        return ['errcode' => self::NORMAL, 'errmsg' =>$errmsg];
    }
}
