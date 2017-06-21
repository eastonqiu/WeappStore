<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;
use EasyWeChat;
use Illuminate\Support\Facades\DB;
use App\Common\Message;

class Withdraw extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    const WITHDRAW_APPLY_STATUS = 0;
    const WITHDRAW_FINISH_STATUS = 1;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function refund() {
        $user = $this->user;
    	$totalRefund = $this['refund'] - $this['refunded'];
        $totalRefund = $totalRefund > 0 ? $totalRefund : 0;
        $refund = $totalRefund;

    	$orders = BorrowOrder::getRefundableOrders($user['id']);
    	$detail = [];

    	foreach($orders as $order) {
    		$orderid = $order['orderid'];
            $refundable = $order['refundable'];
    		// check
    		if($refundable <= 0) {
    			Log::debug("order $orderid all refund");
                $order['refund_no'] = BorrowOrder::ORDER_REFUND_FINISH;
    			$order->save();
    			continue;
    		}

            $refundNow = $refund > $refundable ? $refundable : $refund;
    		Log::debug('try to refund:' . $refundNow . ', orderid: ' . $orderid);

            $useUnsettledFunds = time() - $order['borrow_time'] <= 86400; // 是否使用未结算账户
            $retryMax = 1; // 最多重试一次

            do {
                // 当天的订单用未结算金额退, 非当天的订单用可用余额退
                $refundAccount = $useUnsettledFunds ? 'REFUND_SOURCE_UNSETTLED_FUNDS' : 'REFUND_SOURCE_RECHARGE_FUNDS';
                $ret = EasyWeChat::payment()->refund($orderid, $orderid."-R".$order['refund_no'], $order['paid'], $refundNow, null, 'out_trade_no', $refundAccount);
                Log::debug(print_r($ret, true));
                if ( $ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS' ) {
                    $refundResult = true;
                    Log::debug('wechat pay refund success');
                    break;
                } else if($ret['err_code'] == 'SYSTEMERROR') {
                    // 若微信返回系统错误, 则等待下一轮退款再重试
                    Log::debug('try again next time');
                    break 2; // 直接退出本轮退款
                } else if($ret['err_code'] == 'NOTENOUGH') {
                    // 以下策略保证尽可能的少分单退款
                    // 若微信支付账户 未结算金额不足,则暂停此次退款,等到账户余额充足再自动退款
                    $useUnsettledFunds = ! $useUnsettledFunds; // 改用另一个退款方式尝试
                } else if($ret['err_code'] == 'REFUND_FEE_MISMATCH') {
                    // 若出现订单金额不一致的问题, 则代表前一次提交的一次退款失败了, 紧接着分单尝试退了部分
                    // 等到下一轮尝试退款时,这个订单该退的金额由于需退款金额不同, 和之前不一致了
                    // 则这次采用同样的退款编号但金额不同的退款会失败, 需要将退款编号更新一下再次尝试退款
                    Log::debug('REFUND_FEE_MISMATCH, increment refundno, and try again next time');
                    $order['refund_no'] = $order['refund_no'] + 1;
                    $order->save();
                } else {
                    $refundResult = false;
                    break;
                }
                $retryMax -= 1;
            } while($retryMax >= 0);

    		if ($refundResult) {
    			Log::debug('refund success, orderid: ' . $orderid . ', refund:' . $refundNow);
    			$order['refundable'] -= $refundNow;
    			$order['refund_no'] = $order['refundable'] <= 0 ? BorrowOrder::ORDER_REFUND_FINISH : ($order['refund_no']+1);
                if(! $order->save()) {
                    Log::error('update order refund info error');
                }
    			$detail[] = [$orderid, $refundNow]; // 退款详情
    		} else {
    			Log::error('wxpay refund fail, orderid: ' . $orderid . ', refund:' . $refundNow . ', ret:' . print_r($ret, true));
    			Log::error('continue next order');
    			continue;
    		}

    		$refund -= $refundNow;
    		Log::debug('left:' . $refund);
    		if($refund <= 0)
    			break;
    	}

    	$refunded = $totalRefund - $refund;

    	Log::debug('refunded: ' . $refunded);
    	if($refund > 0) {
    		Log::error('no complete, remain:' . $refund);
    	} else {
    		Log::debug('finish all refund');
    	}

    	if($refunded > 0) {
    	    $this['refunded'] = $this['refunded'] + $refunded;
            $this['detail'] = empty($this['detail']) ? $detail : array_merge(json_decode($this['detail'], true), $detail);
            $this['detail'] = json_encode($this['detail']);
            $this['refund_time'] = date("y-m-d H:i:s",time());
            if($refund == 0) {
                $this['status'] = self::WITHDRAW_FINISH_STATUS;
            }

            if(! $this->save()) {
        		Log::error('update withdraw log fail');
            }

    		$ret = User::where('id', $user['id'])->where('refund', '>=', $refunded)->update([
                'refund' => DB::raw('refund - ' . $refunded),
            ]);
        	if(! $ret) {
        		Log::error('update user account money fail');
        	}
    	}

        return $refund == 0; // 是否退款完成
    }

}
