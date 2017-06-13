<?php

namespace App\Common;

class Errors {
    const NORMAL = 0;
    const INVALID_DEVICE_ID = 1;
    const INVALID_ORDER_ID = 2;
    const INVALID_BATTERY_ID = 3;

    public static function error($errcode, $errmsg) {
        return ['errcode' = > $errcode, 'errmsg' = >$errmsg];
    }

    public static function success($errmsg) {
        return ['errcode' = > $self::NORMAL, 'errmsg' = >$errmsg];
    }
}
