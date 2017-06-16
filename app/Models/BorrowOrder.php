<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Common\Errors;
use EasyWeChat;
use EasyWeChat\Payment\Order;
use Log;
use App\Models\Device;

class BorrowOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'orderid';

    const PRODUCT_LIST = [
        '1' => ['name' => '充电宝', 'price' => 10000], // 单位是分
    ];

    protected $guarded = [
        'id', 'refund_no', 'refundable', 'created_at', 'updated_at', 'deleted_at'
    ];

    const ORDER_STATUS_WAIT_PAY = 0;
    const ORDER_STATUS_PAID = 1;

    // 借出
    const ORDER_STATUS_CONFIRM = 2;
    // sub status
    const ORDER_STATUS_BORROW_CONFIRM_FIRST = 21;

    // 归还
    const ORDER_STATUS_RETURN = 3;
    const ORDER_STATUS_DEPOSIT_OUT_NOT_RETURN = 31; // 超时未归还
    const ORDER_STATUS_DEPOSIT_OUT_RETURN = 32; // 超时归还

    // 失败, 故障原因见 sub status
    const ORDER_STATUS_FAIL = 4;
    // sub status
    const ORDER_SUB_STATUS_FAIL = [
        41 => '网络超时',
        42 => '电机故障',
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
            $order['refund_no'] = self::ORDER_CANT_REFUND;
            $order['status'] = self::ORDER_STATUS_PAID;
            $order->save();
            Log::debug('pay by account balance money');

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
            'notify_url'       => config('SERVER_DOMAIN') . "/pay_notify", // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $user['openid'], // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ]);

        $result = EasyWechat::payment()->prepare($wechatOrder);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            return Errors::error(Errors::ORDER_PAY_NEW, $prepayId);
        } else {
            Log::error("wechat unify order fail: return code: {$result->return_code}, result code: {$result->result_code}");
            return Errors::error(Errors::ORDER_WECHAT_ORDER_FAIL, "wechat unify order fail");
        }
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
		return sprintf("FTJ-%u%02u%02u-%02u%02u%02u-%05u", $year, $mon, $mday, $h, $m, $s, $sn);
    }

}
