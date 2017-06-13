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

    public static function syncSetting($mac, $deviceInfo) {
        $device = Device::where('mac', $mac)->first();
        $device['last_sync'] = time();
        $device['soft_ver'] = $deviceInfo['soft_ver'];
        $device['device_ver'] = $deviceInfo['device_ver'];
        $device['push_id'] = $deviceInfo['push_id'];
        $device->save();
        $strategy = $device->getDeviceStrategy();
        $strategy['device_id'] = $device['id'];
		return $strategy;
	}

	public static function syncBattery($deviceId, $deviceInfo, $slots) {
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
                    'device_id' => $battery['device_id'],
                    'slot' => $battery['slot'],
                    'power' => $battery['power'],
                    'voltage' => $battery['voltage'],
                    'current' => $battery['current'],
                    'temperature' => $battery['temperature'],
                    'last_sync' => time(),
                ]);
            }

            // 更新槽位信息
            Slot::firstOrCreate(['device_id' => $deviceId, 'slot' => $battery['slot']])->update([
                'battery_id' => $battery['id'],
                'status' => $battery['status'],
                'last_sync' => time(),
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
                return Errors::success('confirm success'));
            }
            // 其他状态重试，则直接返回成功, 间隔时间不超过15s
            if($order && $_GET['retry'] && (time() - $order['updated_at'] < 15)) {
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
                    LOG::WARN('roll back battery order info, battery:' . $order['battery_id']);
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

			// status: 0:push后先回复充电宝ID信息， 1：代表确认用户拿走电池 2：代表借出失败(用户未拿走等)
			switch ($data['status']) {
				
				case 2:
				case 3:
				// 4 主控板无应答 5 主控板没有确认信息 11 机器工作中 12 四轴超时无响应 13 电池卡在通道 14 机械手报警 15 异常断电
				case 4:
				case 5:
				// 6 mini: 没有合适的电池借出
				case 6:
                // 7 mini: 红外异常
                case 7:
                // 8 mini: 电机故障 9 解锁5v失败
                case 8:
                case 9:
                // 10 没有充电宝
                case 10:
				case 11:
				case 12:
				case 13:
				case 14:
				case 15:
				case 16:
				// 30 因同步时间不成功而无法执行借订单 31 因上一个订单未完成而无法执行新订单
				case 30:
				case 31:
				// 32 设备重发3次STATUS=0都没有收到平台应答，通知平台取消该订单
				case 32:
					// 确认借出失败: 库存回滚, 退还押金到账户余额
					$ret = true;
					if($ret) {
						LOG::DEBUG('battery amount rollback success');
						// 假设第一次确认过，但第二次却借出未拿走，需要清除调第一次的确认记录
						if($order['status'] == ORDER_STATUS_RENT_CONFIRM_FIRST) {
							if(C::t('#mcs#mcs_battery')->update($order['battery_id'], array('status'=>BATTERY_INSIDE))) {
								LOG::DEBUG('roll back battery status success');
							} else {
								LOG::DEBUG('roll back battery status fail');
							}
						}

						$returnTime = time();
						$return_shop_station_id = C::t('#mcs#mcs_station')->getShopStationId($sid);
						$orderMsg['return_station'] = C::t('#mcs#mcs_shop_station')->getTitle($return_shop_station_id);
						// $orderMsg['return_station'] = C::t('#mcs#mcs_station')->getTitle($sid);
						$usefee = 0;
						if($data['status'] == 2) {
							$error_cause = $data['result_msg'] ? :'借出未拿走，自动退还';
							$status = ORDER_STATUS_RENT_FAIL;
						}
						else if($data['status'] == 3) {
							$error_cause = '网络超时，押金自动退回账户';
							$status = ORDER_STATUS_TIMEOUT_REFUND;
						}
						else if($data['status'] == 4) {
							$error_cause = '主控板无应答';
							$status = ORDER_STATUS_MAINCONTROL_NO_RESPONSE;
						}
						else if($data['status'] == 5) {
							$error_cause = '主控板没有确认信息';
							$status = ORDER_STATUS_MAINCONTROL_NO_CONFIRM;
						}
						else if($data['status'] == 6) {
							$error_cause = '没有合适的电池借出(mini)';
							$status = ORDER_STATUS_NO_MINI_BATTERY;
						}
						else if($data['status'] == 7) {
							$error_cause = '红外异常';
							$status = ORDER_STATUS_INFRARED_ERROR;
						}
						else if($data['status'] == 8) {
							$error_cause = '电机故障';
							$status = ORDER_STATUS_MOTOR_ERROR;
						}
						else if($data['status'] == 9) {
							$error_cause = '解锁5V失败';
							$status = ORDER_STATUS_UNLOCK_5V_FAIL;
						}
						else if($data['status'] == 10) {
							$error_cause = '没有充电宝';
							$status = ORDER_STATUS_NO_BATTERY;
						}
						else if($data['status'] == 11) {
							$error_cause = '机器工作中';
							$status = ORDER_STATUS_STATION_WORKING;
						}
						else if($data['status'] == 12) {
							$error_cause = '四轴超时无响应';
							$status = ORDER_STATUS_AXIS_NO_RESPONSE;
						}
						else if($data['status'] == 13) {
							$error_cause = '电池卡在通道';
							$status = ORDER_STATUS_BATTERY_STUCK;
						}
						else if($data['status'] == 14) {
							$error_cause = '机械手报警';
							$status = ORDER_STATUS_ARM_ALARM;
						}
						else if($data['status'] == 15) {
							$error_cause = '异常断电';
							$status = ORDER_STATUS_UNEXPECTED_OUTAGE;
						}
						else if($data['status'] == 16) {
							$orderMsg['battery'] = '通道异常';
							$status = ORDER_STATUS_CHANNEL_ERROR;
						}
						else if($data['status'] == 30) {
							$error_cause = '同步时间失败';
							$status = ORDER_STATUS_SYNC_TIME_FAIL;
						}
						else if($data['status'] == 31) {
							$error_cause = '上一单未完成';
							$status = ORDER_STATUS_LAST_ORDER_UNFINISHED;
						}
						else if($data['status'] == 32) {
							$error_cause = '网络确认无应答';
							$status = ORDER_STATUS_NETWORK_NO_RESPONSE;
						}

						// 芝麻信用不需要退款, 直接撤销订单即可
						$orderMsg['refund_fee'] = $order['platform'] != PLATFORM_ZHIMA ? $order['price'] : 0;
						// 更新订单状态
						// $status = $data['status'] == 2? ORDER_STATUS_RENT_FAIL : ORDER_STATUS_TIMEOUT_REFUND;
						$ret = C::t('#mcs#mcs_tradelog')->update($orderid, [
						    'status' => $status,
                            'return_station'=>$sid,
                            'return_time'=>$returnTime,
                            'usefee'=>$usefee,
                            'message' => serialize($orderMsg),
                            'lastupdate' => time(),
                            'return_shop_id' => $order['borrow_shop_id'],
                            'return_shop_station_id' => $order['borrow_shop_station_id'],
                            'return_station_name' => $order['borrow_station_name'],
                            'return_city' => $order['borrow_city'],
                            'return_device_ver' => $order['borrow_device_ver'],
                        ]);
						if($ret) {
							LOG::DEBUG('sucess to update to order status');
						} else {
							LOG::ERROR('fail to update to order status');
							echo json_encode(makeErrorData(ERR_SERVER_DB_FAIL, 'battery amount rollback fail, db server fail')); exit;
						}

						// 押金退回账户余额
						if($order['platform'] != PLATFORM_ZHIMA) {
							if(C::t('#mcs#mcs_user')->returnBack($uid, $orderMsg['refund_fee'], $order['price'])) {
								LOG::DEBUG('sucess to return money to user account');
							} else {
								LOG::ERROR('fail to return money to user account');
								echo json_encode(makeErrorData(ERR_SERVER_DB_FAIL, 'battery amount rollback fail, db server fail')); exit;
							}
							$deposit = $order['price'];
						}
						// 芝麻信用撤销订单
						else {
							// 待撤销, 定时任务撤销该订单
							C::t('#mcs#mcs_trade_zhima')->update($orderid, ['status' => ZHIMA_ORDER_CANCEL_WAIT, 'update_time'=>time()]);
							LOG::DEBUG('update zhima order waitting for cancel, orderid: ' . $orderid);
							$deposit = 0;
						}

						// 推送消息
						if($data['status'] == 2) {
							$wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'difftime'=>($returnTime-$order['borrow_time']), 'returntime'=>$returnTime, 'usefee'=>$usefee, 'battery'=>$error_cause, 'return_station'=>$orderMsg['return_station'], 'needAdapterFee'=>false);
							$type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
							addMsgToQueue($type, getReturnMsg($wxmsg));
						} else {
							$wxmsg = array('openid'=>$openid, 'platform'=>$platform, 'orderid'=>$orderid, 'refund'=>$deposit, 'refundTime'=>time(), 'isBattery'=>true, 'cause'=>$error_cause);
							$type = $platform == PLATFORM_WX ? WX_TEMPLATE : ALIPAY_TEMPLATE;
							addMsgToQueue($type, getRefundMsg($wxmsg));
						}
						echo json_encode(makeErrorData(ERR_NORMAL, 'battery amount rollback success'));
					} else {
						LOG::ERROR('battery amount rollback fail');
						echo json_encode(makeErrorData(ERR_REQUEST_FAIL, 'battery amount rollback fail'));
					}
					exit;
				default:
					echo json_encode(makeErrorData(ERR_PARAMS_INVALID, 'invalid status')); exit;
			}
		} else {
			echo json_encode(makeErrorData(ERR_PARAMS_INVALID, 'invalid parameter 2'));
		}
		exit;
	}

	public static function returnBack($deviceId, $battery) {
		return [1,2];
	}

    public function getDeviceStrategy() {
        $strategy = $this->deviceStrategy;
        if(empty($strategy)) {
            $strategy = Settings::get(Settings::DEVICE_STRATEGY);
        } else {
            $strategy = $strategy['value'];
        }
        return json_decode($strategy, true);
    }

    public function deviceStrategy() {
        $this->hasOne(DeviceStrategy::class);
    }
}
