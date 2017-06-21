<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Common\Errors;
use EasyWeChat;
use EasyWeChat\Payment\Order;
use Log;
use App\Models\User;
use App\Models\Device;
use App\Common\Message;

class BorrowOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'orderid';

    public $incrementing = false;

    const PRODUCT_LIST = [
        '1' => ['name' => '充电宝', 'price' => 1], // 单位是分
    ];

    protected $guarded = [
        'refund_no', 'refundable', 'created_at', 'updated_at', 'deleted_at'
    ];

    const ORDER_STATUS_WAIT_PAY = 0;
    const ORDER_STATUS_PAID = 1;

    // 借出
    const ORDER_STATUS_BORROW_CONFIRM = 2;
    // sub status
    const ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST = 21;
    const ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH = 22;

    // 归还
    const ORDER_STATUS_RETURN = 3;
    const ORDER_SUB_STATUS_RETURN_NORMAL = 31; // 正常归还
    const ORDER_SUB_STATUS_DEPOSIT_OUT_NOT_RETURN = 32; // 超时未归还
    const ORDER_SUB_STATUS_DEPOSIT_OUT_RETURN = 33; // 超时归还

    // 失败, 故障原因见 sub status
    const ORDER_STATUS_FAIL = 4;
    // sub status
    const ORDER_SUB_STATUS_FAIL_NETWORK_TIMEOUT = 41;

    // 支付异常
    const ORDER_PAY_EXCEPTION_STATUS = 5;

    const ORDER_STATUS_MAP = [
        self::ORDER_STATUS_WAIT_PAY => '未支付',
        // 借出
        self::ORDER_STATUS_BORROW_CONFIRM => '借出',
        self::ORDER_SUB_STATUS_BORROW_CONFIRM_FIRST => '正在借出',
        self::ORDER_SUB_STATUS_BORROW_CONFIRM_FINISH => '借出完成',
        // 归还
        self::ORDER_STATUS_RETURN => '归还',
        self::ORDER_SUB_STATUS_RETURN_NORMAL => '正常归还',
        self::ORDER_SUB_STATUS_DEPOSIT_OUT_NOT_RETURN => '租金扣完(未归还)',
        self::ORDER_SUB_STATUS_DEPOSIT_OUT_RETURN => '租金扣完(归还)',
        // 故障
        self::ORDER_STATUS_FAIL => '故障',
        self::ORDER_SUB_STATUS_FAIL_NETWORK_TIMEOUT => '网络超时',
        '42' => '无可借电池',
        '43' => '未知故障',
        self::ORDER_PAY_EXCEPTION_STATUS => '支付异常',
    ];

    // 提现退款相关
    const ORDER_CANT_REFUND = -1; // 账户内支付, 没有产生平台订单, 无法用于退款
    const ORDER_REFUND_FINISH = -2; // 此订单金额已全部退款


    /*
		幂等性检查, 多次请求的结果和一次请求的结果是一致的
	*/
	public static function idempotent($orderid)
	{
		return BorrowOrder::where('orderid', $orderid)
                ->whereRaw('unix_timestamp(updated_at) < ' . (time()-3))
                ->update(['updated_at' => date("y-m-d H:i:s",time())]);
	}

    public static function createOrder($userId, $deviceId, $productId, $platform = User::PLATFORM_WECHAT) {
        // 判断是否有库存
        if(! Device::hasStock($deviceId)) {
            Log::debug("{$deviceId} stock not enough");
            return Errors::error(Errors::ORDER_STOCK_NO_ENOUGH, "pay by account");
        }

        $price = self::PRODUCT_LIST[$productId]['price'];
        $user = User::find($userId);
        $orderid = self::_generateOrderId();

        Log::debug("{$user['id']} (balance: {$user['balance']}) prepare to borrow {$productId} (price: {$price}) in {$deviceId}, orderid: {$orderid}");

        $needPay = true;

        $device = Device::find($deviceId);

        $order = BorrowOrder::create([
			'orderid' => $orderid,
            'user_id' => $userId,
            'openid' => $user['openid'],
            'platform' => $platform,
			'price' => $price,
            'product_id' => $productId,
			'status' => self::ORDER_STATUS_WAIT_PAY,
			'borrow_device_id' => $deviceId,
			'borrow_station_id' => empty($device->station['id']) ? 0 : $device->station->id,
            'borrow_shop_id' => empty($device->station->shop->id) ? 0 : $device->station->shop->id,
            'borrow_device_ver' => $device['device_ver'],
            'borrow_station_name'  => $device->getStationName(),
            'borrow_time' => time()
		]);

        // 直接账户内支付
        if(User::pay($userId, $price)) {
            $needPay = false;
            BorrowOrder::where('orderid', $orderid)->update([
                'refund_no' => self::ORDER_CANT_REFUND,
                'status' => self::ORDER_STATUS_PAID,
            ]);
            Log::debug('pay by account balance money, order status update ok');

            // 给设备推送 借命令
            Device::borrow($deviceId, $orderid);
            return Errors::error(Errors::ORDER_PAY_BY_ACCOUNT, "pay by account");
        }

        $price = $price - $user['balance'];
        Log::debug("{$user['id']} need pay {$price}");
        // 调用微信支付统一下单

        $wechatOrder = new Order([
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => self::PRODUCT_LIST[$productId]['name'],
            'detail'           => self::PRODUCT_LIST[$productId]['name'],
            'out_trade_no'     => $orderid,
            'total_fee'        => $price, // 单位：分
            'notify_url'       => env('SERVER_DOMAIN') . "/pay_notify", // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $user['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ]);

        $payment = EasyWechat::payment();
        $result = $payment->prepare($wechatOrder);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            return Errors::error(Errors::ORDER_PAY_NEW, $payment->configForPayment($prepayId));
        } else {
            Log::error("wechat unify order fail: return code: {$result->return_code}, result code: {$result->result_code}");
            return Errors::error(Errors::ORDER_WECHAT_ORDER_FAIL, "wechat unify order fail");
        }
    }

    public static function payNotify($orderid, $paid, $platform) {
    	// 幂等判断, 过滤重复并发请求
    	if(! BorrowOrder::idempotent($orderid)) {
    		Log::debug("pay notify repeated request {$battery['id']}, $orderid");
    		return false;
    	}
    	$order = BorrowOrder::find($orderid);
    	$orderStatus = $order['status'];
    	$user = User::find($order['user_id']);
        Log::debug("pay notify $orderid, paid: $paid");
        if($order['status'] == self::ORDER_STATUS_WAIT_PAY){
			// 若是部分支付, 需扣除账户余额
			$price = $order['price'];
			$needPayMore = 0;

			if($paid < $price) {
				$balance = $user['balance'];
				Log::debug("balance: $balance, paid: $paid");
				if($balance + $paid < $price) {
					Log::error("balance not enough, please check {$user['id']} the paid: $paid");
					$order['status'] = self::ORDER_STATUS_PAID_NOT_ENOUGH;
                    $order['paid'] = $paid;
                    $order->save();
					return false;
				}
				$needPayMore = $price - $paid;
				Log::debug('need pay more: ' . $needPayMore);
			}
			if(! User::payMore($user['id'], $needPayMore, $order['price'])) {
                Log::error("user no enough balance, $orderid");
                $order['status'] = self::ORDER_PAY_EXCEPTION_STATUS;
                $order['paid'] = $paid;
                $order['refundable'] = $paid;
                $order->save();
                return false;
            }
            $order['status'] = self::ORDER_STATUS_PAID;
            $order['paid'] = $paid;
            $order['refundable'] = $paid;
            $order->save();

            // 给设备推送 借命令
            Device::borrow($order['borrow_device_id'], $orderid);
    	} else {
    		return false;
    	}
    	return true;
    }

    public static function fee($orderid, $returnTime) {
        $order = BorrowOrder::find($orderid);
        if(empty($order)) {
            return false;
        }
        $borrowTime = $order['borrow_time'];
        $feeStrategy = json_decode($order['fee_strategy'], true);
    	$useTime = $returnTime - $borrowTime;
        $useTime = $useTime > 0 ? $useTime : 0;
        $useFee = 0;
    	if ( !empty($feeStrategy['free_time']) && ($feeStrategy['free_time'] != 0) && $useTime <= ($feeStrategy['free_time'] * $feeStrategy['free_unit']) ) {
    		Log::debug('return battery in free time');
    		$useFee = 0;
    	} else {
    		// 每单位时间收费
    		// $useFee = ceil($useFee / $feeStrategy['fee_unit']) * $feeStrategy['fee'];
            // // 固定收费
            // $useFee = ($useFee > 0 ? $useFee : 0) + $feeStrategy['fixed'];
    	}

        $useFee = $useFee > 0 ? $useFee : 0;

    	return $useFee;
    }

    public function revertPaidOrder($errorStatus) {
        if($this['status'] != BorrowOrder::ORDER_STATUS_PAID) {
            Log::error("{$this['orderid']} cancel to refund fail, order status: {$this['status']}");
            return false;
        }

        if(empty(User::returnDeposit($this['user_id'], $this['platform'], $this['price'], $this['price']))) {
            Log::error("{$this['orderid']} cancel to refund fail, user id: {$this['user_id']}");
            return false;
        }

        $orderMsg = json_decode($this['msg'], true);
        if(isset($orderMsg['borrow_battery'])) {
            $orderMsg['return_battery'] = $orderMsg['borrow_battery'];
        }

        // 更新订单状态
        $this['status'] = BorrowOrder::ORDER_STATUS_FAIL;
        $this['sub_status'] = $errorStatus;
        $this['usefee'] = 0;
        $this['msg'] = json_encode($orderMsg);
        $this['return_device_id'] = $this['borrow_device_id'];
        $this['return_device_ver'] = $this['borrow_device_ver'];
        $this['return_station_id'] = $this['borrow_station_id'];
        $this['return_shop_id'] = $this['borrow_shop_id'];
        $this['return_station_name'] = $this['borrow_station_name'];
        $this['return_time'] = time();
        $this->save();

        // 推送模板消息
        Message::fail($this['platform'], [
            'openid'=>$this['openid'],
            'orderid'=>$this['orderid'],
        ]);

        Log::debug("revert orderid {$this['orderid']} success");
        return true;
    }

    public static function getRefundableOrders($userId) {
        return BorrowOrder::where('user_id', $userId)
                    ->where('status', '<>', BorrowOrder::ORDER_STATUS_WAIT_PAY)
                    ->where('refund_no', '>=', 0)
                    ->where('refundable', '>', '0')
                    ->orderBy('refundable', 'desc')
                    ->orderBy('borrow_time', 'asc')
                    ->get();
    }

    private static function _generateOrderId() {
        $date = getdate(time());
		$year = $date['year'];
        $mon = $date['mon'];
        $mday = $date['mday'];
		$h = $date['hours'];
        $m = $date['minutes'];
        $s = $date['seconds'];
		$sn = rand(1, 99999);
		return sprintf("XXX-%u%02u%02u-%02u%02u%02u-%05u", $year, $mon, $mday, $h, $m, $s, $sn);
    }

}
