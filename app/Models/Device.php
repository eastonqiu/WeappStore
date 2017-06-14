<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Common\Errors;
use Log;

class Device extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id', 'created_at', 'orderid', 'updated_at', 'deleted_at'
    ];

    public function station() {
        return $this->belongsTo(Station::class);
    }

    public static function syncSetting($mac, $deviceInfo) {
        $device = Device::where('mac', $mac)->first();
        $device['last_sync'] = date("y-m-d H:i:s",time());
        $device['soft_ver'] = $deviceInfo['soft_ver'];
        $device['device_ver'] = $deviceInfo['device_ver'];
        $device['push_id'] = $deviceInfo['push_id'];
        $device->save();
        $strategy = $device->getDeviceStrategy();
        $strategy['device_id'] = $device['id'];
		return $strategy;
	}

	public static function syncBattery($deviceId, $deviceInfo, $batteries) {
        // device info
        $device = Device::find($deviceId);
        $device['total'] = $deviceInfo['total'];
        $device['usable'] = $deviceInfo['usable'];
        $device['empty'] = $deviceInfo['empty'];
        $device['sdcard'] = $deviceInfo['sdcard'];
        $device->save();

        // 槽位电池列表
        foreach($batteries as $battery) {
            if(empty($battery['slot']))
                continue;

            // 更新电池信息
            if(! empty($battery['id'])) {
                Battery::firstOrCreate(['id', $battery['id']])->update([
                    'id' => $battery['id'],
                    'device_id' => $battery['device_id'],
                    'slot' => $battery['slot'],
                    'power' => $battery['power'],
                    'voltage' => $battery['voltage'],
                    'current' => $battery['current'],
                    'temperature' => $battery['temperature'],
                    'last_sync' => date("y-m-d H:i:s",time()),
                ]);
            }

            // 更新槽位信息
            Slot::firstOrCreate(['device_id' => $deviceId, 'slot' => $battery['slot']])->update([
                'battery_id' => $battery['id'],
                'status' => $battery['status'],
                'last_sync' => date("y-m-d H:i:s",time()),
            ]);
        }

        return Errors::success('sync battery successfully');
	}

	public static function removeBattery($deviceId, $batteries) {
        $device = Device::find($deviceId);

        foreach ($batteries as $battery) {
            Battery::where(['id', $battery['id']])->update([
                'device_id' => 0,
            ]);

            Slot::firstOrCreate(['device_id' => $deviceId, 'slot' => $battery['slot']])->update([
                'battery_id' => 0,
                // 'status' => , // 空槽
            ]);
        }

        return Errors::success('sync battery successfully');
	}

	public static function borrowConfirm($deviceId, $orderid, $borrowBattery, $status) {

        $order = BorrowOrder::find($orderid);
        if(empty($order)) {
            return Errors::error(Errors::INVALID_ORDER_ID, 'invalid orderid');
        }

        if($order['status'] != BorrowOrder::ORDER_STATUS_PAID
            && $order['sub_status'] != BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
            // 直接检查是否已经确认
            if($order['status'] == BorrowOrder::ORDER_STATUS_BORROW_CONFIRM) {
                Log::debug('check, it is confirm now, need not retry, it is ok ' . $orderid . ', status:' . $order['status']);
                return Errors::success('confirm success');
            }
            // 其他状态重试，则直接返回成功, 间隔时间不超过15s
            if($order && $_GET['retry'] && (time() - strtotime($order['updated_at']) < 15)) {
                Log::debug('retry success, orderid:' . $orderid . ', status:' . $order['status']);
                return Errors::success('retry success');
            }
            Log::debug('invalid order, orderid:' . $orderid . ', status:' . $order['status']);
            Errors::error(Errors::INVALID_ORDER_ID, 'invalid orderid request');
        }

        $battery = Battery::find($borrowBattery['id']);
        if(empty($battery)) {
            return Errors::error(Errors::INVALID_BATTERY_ID, 'invalid battery');
        }
        // 收费策略
        $feeStrategy = Station::where('device_id', $deviceId)->shop->getFeeStrategy()->toArray();
        $borrowOrder['fee_strategy'] = json_encode($feeStrategy);
        // 借出电池信息
        $orderMsg = json_decode($order['msg']);
        $orderMsg['borrow_battery'] = $battery->toArray();

        switch($status) {
            case 0:
                if($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
                    Log::debug('network problem, status 0 has updated, it is ok');
                    return Errors::success('retry confirm success');
                }
            case 1:
                // 判断若 status1与status0传过来的电池ID不同，则应用端两次确认的电池有出入，可能第一次确认要借出的电池有问题，进行了更换，需要将数据库里的数据进行回滚更新
                if($battery['status'] == BATTERY::BATTERY_OUTSIDE && $order['battery_id'] != $battery['id']) {
                    //回滚电池绑定的订单号
                    Battery::where('id', $order['battery_id'])->update([
                        'status' => BATTERY::BATTERY_INSIDE,
                        'orderid' => '',
                    ]);
                    Log::warning('roll back battery order info, battery:' . $order['battery_id']);
                }

                // 更新电池信息
                $battery['device_id'] = $deviceId;
                $battery['orderid'] = $orderid;
                $battery['status'] = BATTERY::BATTERY_OUTSIDE;
                $battery->save();
                Log::debug('update battery info');

                // 更新订单信息
                if($status == 0) {
                    //仅仅是确认, 不更新状态
                    $order['battery_id'] = $battery['id'];
                    $order['sub_status'] = BorrowOrder::ORDER_STATUS_BORROW_CONFIRM_FIRST;
                    $order['msg'] = json_encode($orderMsg);
                    $order->save();
                    Log::debug('update order info');
                } else {
                    $order['battery_id'] = $battery['id'];
                    $order['status'] = BorrowOrder::ORDER_STATUS_BORROW_CONFIRM;
                    $order['sub_status'] = BorrowOrder::ORDER_STATUS_BORROW_CONFIRM_FIRST;
                    $order['msg'] = json_encode($orderMsg);
                    $order->save();

                    // 推送模板消息
                    // $deposit = $order['price'];
                    // if($order['platform'] == PLATFORM_ZHIMA) {
                    //     $deposit = 0;
                    // }
                    // $wxmsg = [
                    //             'openid'=>$openid,
                    //             'platform'=>$platform,
                    //             'orderid'=>$orderid,
                    //             'sid'=>$sid,
                    //             'battery'=>$order['battery_id'],
                    //             'borrow_station'=>$order['borrow_station_name'],
                    //             'renttime'=>$order['borrow_time'],
                    //             'price'=>$deposit,
                    //             'new_credit'=>$creditsInfo[0],
                    //             'total_credit'=>$creditsInfo[1],
                    //             'cable' => $order['cable'],
                    //         ];
                    // $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
                    // addMsgToQueue($type, getRentConfirmMsg($wxmsg));
                }
                return Errors::success('confirm success');
            default:
                // 错误状态处理
                // 确认借出失败: 库存回滚, 退还押金到账户余额
                Log::warning("{$orderid} fail status: {$status}");
                // 用户账号退款
                if(empty(User::returnDeposit($order['user_id'], $order['platform'], $order['price'], $order['price']))) {
                    return Errors::error(Errors::USER_ACCOUNT_REFUND_FAIL, 'user account refund fail');
                }

                if($order['sub_status'] == BorrowOrder::ORDER_STATUS_BORROW_CONFIRM_FIRST) {
                    //回滚电池绑定的订单号
                    Battery::where('id', $order['battery_id'])->update([
                        'status' => BATTERY::BATTERY_INSIDE,
                        'orderid' => '',
                    ]);
                    Log::warning('roll back battery order info, battery:' . $order['battery_id']);
                }

                // 更新订单状态
                $order['status'] = BorrowOrder::ORDER_STATUS_FAIL;
                $order['sub_status'] = $status;
                $order['usefee'] = 0;
                $order['msg'] = json_encode($orderMsg);
                $order['return_device_id'] = $order['borrow_device_id'];
                $order['return_device_ver'] = $order['borrow_device_ver'];
                $order['return_station_id'] = $order['borrow_station_id'];
                $order['return_shop_id'] = $order['borrow_shop_id'];
                $order['return_station_name'] = $order['borrow_station_name'];
                $order['return_time'] = time();
                $order->save();

                Log::debug("update order {$orderid}");

                // 推送消息
                // if($data['status'] == 2) {
                //     $wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'difftime'=>($returnTime-$order['borrow_time']), 'returntime'=>$returnTime, 'usefee'=>$usefee, 'battery'=>$error_cause, 'return_station'=>$orderMsg['return_station'], 'needAdapterFee'=>false);
                //     $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
                //     addMsgToQueue($type, getReturnMsg($wxmsg));
                // } else {
                //     $wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'refund'=>$deposit, 'refundTime'=>time(), 'isBattery'=>true, 'cause'=>$error_cause);
                //     $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
                //     addMsgToQueue($type, getRefundMsg($wxmsg));
                // }

                return Errors::success('order and battery rollback success');
            }
	}

	public static function returnBack($deviceId, $batteryInfo) {
        Log::debug("return back {$batteryInfo['id']} in $deviceId");
    	if(empty($batteryInfo) || empty($batteryInfo['id']) || empty($deviceId)) {
    		return Errors::error(ERRORS::INVALID_PARAMS, 'invalid parameter');
    	}

        $battery = Battery::find($batteryInfo['id']);
        if(empty($battery)) {
            Log::debug('new battery to business lib: ' . $batteryInfo['id']);
            return Errors::success('new battery to business battery lib');
        }

    	$orderid = $battery['orderid'];
    	Log::debug('orderid:' . $orderid);
        $order = BorrowOrder::find($orderid);
    	if(empty($orderid)) {
    		Log::error("the battery {$battery['id']} is not in any order.");
            $battery['status'] = Battery::BATTERY_INSIDE;
            $battery->save();
            return Errors::success('error order, correct it success');
    	}

    	// 幂等判断, 过滤重复并发请求
    	if(! BorrowOrder::idempotent($orderid)) {
    		Log::debug("return back repeated request {$battery['id']}, $orderid");
    		return Errors::success("repeated request {$battery['id']}, $orderid");
    	}

    	// 带归还时间验证, 单位为秒 数字长度为10, 长度为13是设备端传过来了单位为毫秒的, 这里进行规避
    	if(! is_numeric($batteryInfo['time'])) {
    		$batteryInfo['time'] = 0;
    	}
    	if(strlen($batteryInfo['time']) == 13) {
    		$batteryInfo['time'] = ceil($batteryInfo['time'] / 1000);
    	}

        $device = Device::find($deviceId);

        $battery['status'] = BATTERY::BATTERY_INSIDE;
        $battery['slot'] = $batteryInfo['slot'];
        $battery['power'] = $batteryInfo['power'];
        $battery['temperature'] = $batteryInfo['temperature'];
        $battery['voltage'] = $batteryInfo['voltage'];
        $battery['current'] = $batteryInfo['current'];
        $battery->save();
        Slot::where('device_id', $deviceId)->where('slot', $battery['slot'])->update([
            'battery_id' => $battery['id'],
        ]);
    	Log::debug('update battery status, inside station');

        // 更新订单状态
        $order['return_device_id'] = $deviceId;
        $order['return_device_ver'] = $device['device_ver'];
        $order['return_station_id'] = $device->station->id ? : 0;
        $order['return_shop_id'] = $device->station->shop->id ? : 0;
        $order['return_station_name'] = $device->getStationName();
        $order['return_time'] = $returnTime;
    	// 过滤重复归还
    	if($order['status'] != BorrowOrder::ORDER_STATUS_BORROW_CONFIRM) {
    		Log::error("the status of order:" . $orderid . " is wrong: " . $order['status']);
    		if ($order['sub_status'] == BorrowOrder::ORDER_STATUS_DEPOSIT_OUT_NOT_RETURN) {
                $order['sub_status'] = BorrowOrder::ORDER_STATUS_DEPOSIT_OUT_RETURN;
                $order->save();

    			// $wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'difftime'=>($returnTime-$order['borrow_time']), 'returntime'=>$returnTime, 'usefee'=>$order['usefee'], 'battery'=>$battery['id'], 'return_station'=>$station['title'], 'needAdapterFee'=> 0, 'needCableFee'=> 0);
    			// $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
    			// addMsgToQueue($type, getReturnMsg($wxmsg));
    			return Errors::success('deposit reduced, can not return');
    		}
    		return Errors::success('error status, correct it success');
    	}

    	$orderMsg = json_decode($order['msg'], true);
    	$orderMsg['battery_return'] = $batteryInfo;

    	// 归还时间不能大于当前时间或者小于借出时间, 否则为非法, 采用当前时间归还
    	$returnTime = (empty($batteryInfo['time']) || ($batteryInfo['time'] > time()) || ($batteryInfo['time'] < $order['borrow_time'])) ? time() : $batteryInfo['time'];

    	$usefee = calcFee($order['orderid'], $order['borrow_time'], $returnTime, $needAdapterFee, $needCableFee);
    	if($usefee > $order['price'])
    		$usefee = $order['price'];

        $returnDeposit = $order['price'] - $usefee;
        Log::debug('price:' . $order['price'] . ', usefee:' . $usefee);
    	Log::debug('start to refund to user account, refund:' . $orderMsg['refund_fee']);

        // 退款给用户
        if(empty(User::returnDeposit($order['user_id'], $order['platform'], $returnDeposit, $order['price']))) {
            Log::error('return deposit to user account fail');
            return Errors::error(Errors::USER_ACCOUNT_REFUND_FAIL, 'user account deposit return fail');
        }
        Log::debug('return deposit to user account ok');

        // 更新订单状态
        $order['status'] = BorrowOrder::ORDER_STATUS_RETURN;
        $order['usefee'] = $usefee;
        $order['msg'] = json_encode($orderMsg);
    	if($usefee >= $order['price']) {
    		Log::debug('borrow too long time, desposit not enough');
    		$order['status'] = BorrowOrder::ORDER_STATUS_DEPOSIT_OUT_RETURN;
            $order->save();

    		// $wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'difftime'=>($returnTime-$order['borrow_time']), 'returntime'=>$returnTime, 'usefee'=>$usefee, 'battery'=>$battery_id, 'return_station'=>$station['title'], 'needAdapterFee'=>$needAdapterFee, 'needCableFee'=>$needCableFee, 'new_credit'=>$creditsInfo[0], 'total_credit'=>$creditsInfo[1]);
    		// $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
    		// addMsgToQueue($type, getReturnMsg($wxmsg));

    		return Errors::success('desposit not enough, but can be returned');
    	}

		Log::debug('update order data');
        $order->save();

    	// $wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'difftime'=>($returnTime-$order['borrow_time']), 'returntime'=>$returnTime, 'usefee'=>$usefee, 'battery'=>$battery_id, 'return_station'=>$station['title'], 'needAdapterFee'=>$needAdapterFee, 'needCableFee'=>$needCableFee, 'new_credit'=>$creditsInfo[0], 'total_credit'=>$creditsInfo[1]);
    	// $type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
    	// addMsgToQueue($type, getReturnMsg($wxmsg));
    	// LOG::DEBUG("Refund template Succeed!");
        return Errors::success("return battery {$battery['id']} ok");
	}

    public function getDeviceStrategy() {
        $strategy = $this->deviceStrategy();
        if(empty($strategy)) {
            $strategy = Setting::get(Setting::DEVICE_STRATEGY);
        } else {
            $strategy = $strategy['value'];
        }
        return json_decode($strategy, true);
    }

    public function deviceStrategy() {
        $this->belongsTo(DeviceStrategy::class);
    }

    public function getStationName() {
        if(empty($this->station->name)) {
            return $this->id;
        }
    }

    public static function pushCmd($deviceId, $cmd) {

    }
}
