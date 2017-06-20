<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Common\Errors;
use App\Common\Push;
use App\Common\Message;
use Illuminate\Support\Facades\DB;
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

    public function batteries() {
        return $this->hasMany(Battery::class);
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
                Battery::firstOrCreate(['id' => $battery['id']])->update([
                    'id' => $battery['id'],
                    'device_id' => $deviceId,
                    'slot' => $battery['slot'],
                    'power' => $battery['power'],
                    'voltage' => $battery['voltage'],
                    'current' => $battery['current'],
                    'temperature' => $battery['temperature'],
                    'battery_status' => $battery['battery_status'],
                    'last_sync' => date("y-m-d H:i:s",time()),
                ]);
            }

            // 更新槽位信息
            Slot::firstOrCreate(['device_id' => $deviceId, 'slot' => $battery['slot']])->update([
                'battery_id' => $battery['id'],
                'status' => $battery['slot_status'],
                // 'status' => $battery['status'],
                'last_sync' => date("y-m-d H:i:s",time()),
            ]);
        }

        return Errors::success('sync battery successfully');
	}

	public static function removeBattery($deviceId, $batteries) {
        $device = Device::find($deviceId);

        foreach ($batteries as $battery) {
            self::popup($deviceId, $battery['id']);
        }

        return Errors::success('remove battery successfully');
	}

	public static function borrowConfirm($deviceId, $orderid, $borrowBattery, $status) {
        $order = BorrowOrder::find($orderid);
        $device = Device::find($deviceId);
        if(empty($order)) {
            return Errors::error(Errors::INVALID_ORDER_ID, 'invalid orderid');
        }

        if($order['status'] != BorrowOrder::ORDER_STATUS_PAID
            && $order['sub_status'] != BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
            // 直接检查是否已经确认
            if($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH) {
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
        $feeStrategy = Device::getFeeStrategy($deviceId);
        $order['fee_strategy'] = json_encode($feeStrategy);
        // 借出电池信息
        $orderMsg = json_decode($order['msg'], true);
        $orderMsg['borrow_battery'] = $borrowBattery;

        switch($status) {
            case BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST :
                if($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
                    Log::debug('network problem, status confirm first has updated, it is ok');
                    return Errors::success('retry confirm success');
                }
            case BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH :
                if($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH) {
                    Log::debug('network problem, status confirm finish has updated, it is ok');
                    return Errors::success('retry confirm success');
                }
                // 判断若 status1与status0传过来的电池ID不同，则应用端两次确认的电池有出入，可能第一次确认要借出的电池有问题，进行了更换，需要将数据库里的数据进行回滚更新
                if($battery['status'] == BATTERY::BATTERY_OUTSIDE && ! empty($order['battery_id']) && $order['battery_id'] != $battery['id']) {
                    //回滚电池绑定的订单号
                    self::rollback($deviceId, $order['battery_id']);
                    Log::warning('roll back battery order info, battery:' . $order['battery_id']);
                }

                // 更新电池信息
                $battery['device_id'] = $deviceId;
                $battery['orderid'] = $orderid;
                $battery['status'] = BATTERY::BATTERY_OUTSIDE;
                $battery->save();
                self::popup($deviceId, $battery['id']);
                Log::debug('update battery and slot info');

                // 更新订单信息
                if($status == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
                    //仅仅是确认, 不更新状态
                    $order['battery_id'] = $battery['id'];
                    $order['status'] = BorrowOrder::ORDER_STATUS_BORROW_CONFIRM;
                    $order['sub_status'] = BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST;
                    $order['msg'] = json_encode($orderMsg);
                    $order->save();
                    Log::debug('update order info');
                } else {
                    $order['battery_id'] = $battery['id'];
                    $order['status'] = BorrowOrder::ORDER_STATUS_BORROW_CONFIRM;
                    $order['sub_status'] = BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH;
                    $order['msg'] = json_encode($orderMsg);
                    $order->save();

                    // 推送模板消息
                    Message::borrow($order['platform'], [
                        'openid'=>$order['openid'],
                        'orderid'=>$order['orderid'],
                        'borrow_station_name'=>$order['borrow_station_name'],
                        'borrow_time'=>$order['borrow_time'],
                    ]);
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

                if($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST) {
                    //回滚电池绑定的订单号
                    self::rollback($deviceId, $order['battery_id']);
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

                // 推送模板消息
                Message::fail($order['platform'], [
                    'openid'=>$order['openid'],
                    'orderid'=>$order['orderid'],
                ]);

                return Errors::success('order and battery rollback success');
            }
	}

	public static function returnBack($deviceId, $batteryInfo, $returnTimeFromDevice = NULL) {
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
    	if(! is_numeric($returnTimeFromDevice)) {
    		$returnTimeFromDevice = 0;
    	}
    	if(strlen($returnTimeFromDevice) == 13) {
    		$returnTimeFromDevice = ceil($returnTimeFromDevice / 1000);
    	}
        // 归还时间不能大于当前时间或者小于借出时间, 否则为非法, 采用当前时间归还
    	$returnTime = (empty($returnTimeFromDevice) || ($returnTimeFromDevice > time()) || ($returnTimeFromDevice < $order['borrow_time'])) ? time() : $returnTimeFromDevice;

        $device = Device::find($deviceId);
        // 更新电池信息
        $battery['device_id'] = $deviceId;
        $battery['status'] = BATTERY::BATTERY_INSIDE;
        $battery['slot'] = $batteryInfo['slot'];
        $battery['power'] = $batteryInfo['power'];
        $battery['temperature'] = $batteryInfo['temperature'];
        $battery['voltage'] = $batteryInfo['voltage'];
        $battery['current'] = $batteryInfo['current'];
        $battery->save();
        self::insertToSlot($deviceId, $battery['id'], $battery['slot']);
    	Log::debug('update battery status, inside station');

        // 更新订单状态
        $returnShopId = 0;
        if(!empty($device->station) && !empty($device->station->shop)) {
            $returnShopId = $device->station->shop->id;
        }
        $order['return_device_id'] = $deviceId;
        $order['return_device_ver'] = $device['device_ver'];
        $order['return_station_id'] = empty($device->station) ? 0 : $device->station->id;
        $order['return_shop_id'] = $returnShopId;
        $order['return_station_name'] = $device->getStationName();
        $order['return_time'] = $returnTime;
    	// 过滤重复归还
    	if($order['status'] != BorrowOrder::ORDER_STATUS_BORROW_CONFIRM) {
    		Log::error("the status of order:" . $orderid . " is wrong: " . $order['status']);
    		if ($order['sub_status'] == BorrowOrder::ORDER_SUB_STATUS_DEPOSIT_OUT_NOT_RETURN) {
                $order['sub_status'] = BorrowOrder::ORDER_SUB_STATUS_DEPOSIT_OUT_RETURN;
                $order->save();

                // 推送模板消息
                Message::return($order['platform'], [
                    'openid'=>$order['openid'],
                    'orderid'=>$order['orderid'],
                    'usetime'=>$returnTime - $order['borrow_time'],
                    'return_station_name' => $order['return_station_name'],
                    'return_time' => $returnTime,
                    'usefee' => $order['usefee'],
                ]);
    			return Errors::success('deposit reduced, can not return');
    		}
    		return Errors::success('error status, correct it success');
    	}

    	$orderMsg = json_decode($order['msg'], true);
    	$orderMsg['return_battery'] = $batteryInfo;

    	// 归还时间不能大于当前时间或者小于借出时间, 否则为非法, 采用当前时间归还
    	$returnTime = (empty($batteryInfo['time']) || ($batteryInfo['time'] > time()) || ($batteryInfo['time'] < $order['borrow_time'])) ? time() : $batteryInfo['time'];

    	$usefee = BorrowOrder::fee($order['orderid'], $returnTime);
        $usefee = 0;
        if($usefee > $order['price'])
    		$usefee = $order['price'];

        $returnDeposit = $order['price'] - $usefee;
        Log::debug('price:' . $order['price'] . ', usefee:' . $usefee);
    	Log::debug('start to refund to user account, refund:' . $returnDeposit);

        // 退款给用户
        if(empty(User::returnDeposit($order['user_id'], $order['platform'], $returnDeposit, $order['price']))) {
            Log::error('return deposit to user account fail');
            return Errors::error(Errors::USER_ACCOUNT_REFUND_FAIL, 'user account deposit return fail');
        }
        Log::debug('return deposit to user account ok');

        // 更新订单状态
        $order['status'] = BorrowOrder::ORDER_STATUS_RETURN;
        $order['sub_status'] = BorrowOrder::ORDER_SUB_STATUS_RETURN_NORMAL;
        $order['usefee'] = $usefee;
        $order['msg'] = json_encode($orderMsg);
    	if($usefee >= $order['price']) {
    		Log::debug('borrow too long time, desposit not enough');
    		$order['sub_status'] = BorrowOrder::ORDER_SUB_STATUS_DEPOSIT_OUT_RETURN;
            $order->save();

            // 推送模板消息
            Message::return($order['platform'], [
                'openid'=>$order['openid'],
                'orderid'=>$order['orderid'],
                'usetime'=>$returnTime - $order['borrow_time'],
                'return_station_name' => $order['return_station_name'],
                'return_time' => $returnTime,
                'usefee' => $order['usefee'],
            ]);

    		return Errors::success('desposit not enough, but can be returned');
    	}

		Log::debug('update order data');
        $order->save();

        // 推送模板消息
        Message::return($order['platform'], [
            'openid'=>$order['openid'],
            'orderid'=>$order['orderid'],
            'usetime'=>$returnTime - $order['borrow_time'],
            'return_station_name' => $order['return_station_name'],
            'return_time' => $returnTime,
            'usefee' => $order['usefee'],
        ]);
        return Errors::success("return battery {$battery['id']} ok");
	}

    public function getDeviceStrategy() {
        $strategy = $this->deviceStrategy;
        if(empty($strategy)) {
            return DeviceStrategy::defaultValue();
        } else {
            return json_decode($strategy['value'], true);
        }
    }

    public static function getFeeStrategy($deviceId) {
        $device = Device::where('id', $deviceId)->with('station.shop.feeStrategy')->first();
        if(! empty($device->station) && ! empty($device->station->shop) && ! empty($device->station->shop->feeStrategy)) {
            return $device->station->shop->feeStrategy->toArray();
        }
        return FeeStrategy::defaultValue();
    }

    public function deviceStrategy() {
        return $this->belongsTo(DeviceStrategy::class);
    }

    public function getStationName() {
        if(empty($this->station->name)) {
            return $this->id;
        }
        return $this->station->name;
    }

    public static function pushCmd($deviceId, $event, array $msg) {
        Log::debug("push cmd {$cmd} to {$deviceId} msg:" . json_encode($msg));
        $msg['msg_id'] = microtime(true);
    	$msg['device_id'] = $deviceId;
        Push::push($deviceId, $event, $msg);
    }

    public static function borrow($deviceId, $orderid) {
        self::pushCmd($deviceId, Push::PUSH_BORROW_BATTERY, ['orderid'=>$orderid]);
    }

    /*
        是否有库存
    */
    public static function hasStock($deviceId) {
        return Device::find($deviceId)->usable > 0;
    }

    /*
        弹出电池
    */
    public static function popup($deviceId, $batteryId) {
        $battery = Battery::find($batteryId);
        $battery['device_id'] = $deviceId;
        $battery['status'] = Battery::BATTERY_OUTSIDE;
        $battery->save();

        Slot::where('device_id', $deviceId)->where('slot', $battery['slot'])->update([
            'battery_id' => 0,
            // 'status' => , // 空槽
        ]);

        Device::where('id', $deviceId)->where('total', '>', 0)->where('usable', '>', 0)->update([
            'total' => DB::raw('total - 1'),
            'usable' => DB::raw('usable - 1'),
            'empty' => DB::raw('empty + 1'),
        ]);
    }

    /*
        弹出电池失败回滚
    */
    public static function rollback($deviceId, $batteryId) {
        $rollBackBattery = Battery::find($batteryId);
        $rollBackBattery['device_id'] = $deviceId;
        $rollBackBattery['status'] = BATTERY::BATTERY_INSIDE;
        $rollBackBattery['orderid'] = '';
        $rollBackBattery->save();

        Slot::where('device_id', $deviceId)->where('slot', $rollBackBattery['slot'])->update([
            'battery_id' => $batteryId,
        ]);

        Device::where('id', $deviceId)->where('empty', '>', 0)->update([
            'total' => DB::raw('total + 1'),
            'usable' => DB::raw('usable + 1'),
            'empty' => DB::raw('empty - 1'),
        ]);
    }

    /*
        插入电池
    */
    public static function insertToSlot($deviceId, $batteryId, $slot) {
        Battery::where('id', $batteryId)->update([
            'device_id' => $deviceId,
            'slot' => $slot,
            'status' => Battery::BATTERY_INSIDE,
        ]);

        Slot::where('device_id', $deviceId)->where('slot', $slot)->update([
            'battery_id' => $batteryId,
        ]);

        Device::where('id', $deviceId)->where('empty', '>', 0)->update([
            'total' => DB::raw('total + 1'),
            'usable' => DB::raw('usable + 1'),
            'empty' => DB::raw('empty - 1'),
        ]);
    }
}
