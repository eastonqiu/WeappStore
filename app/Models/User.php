<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\DB;
use EasyWeChat;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    const PLATFORM_WECHAT = 1;
    const PLATFORM_ALIPAY = 2;
    const PLATFORM_ZHIMA = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    protected $guarded = [
        'id', 'remember_token', 'balance', 'deposit', 'refund', 'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static $rules = [
        'name' => 'required',
        'email' => 'required|unique:users',
        'password' => 'required'
    ];

    public static function returnDeposit($userId, $platform, $refund, $deposit) {
        if(in_array($platform, [self::PLATFORM_WECHAT, self::PLATFORM_ALIPAY])) {
            return User::where('id', $userId)->where('deposit', '>=', $deposit)->update([
                'balance' => DB::raw('balance + ' . $refund),
                'deposit' => DB::raw('deposit - ' . $deposit),
            ]);
        } else {
            // 芝麻信用
            return false;
        }
    }

    public static function pay($userId, $price) {
        return User::where('id', $userId)->where('balance', '>=', $price)->update([
            'deposit' => DB::raw('deposit + ' . $price),
            'balance' => DB::raw('balance - ' . $price),
        ]);
	}

	public static function payMore($userId, $more, $deposit) {
        return User::where('id', $userId)->where('balance', '>=', $more)->update([
            'deposit' => DB::raw('deposit + ' . $deposit),
            'balance' => DB::raw('balance - ' . $more),
        ]);
	}

    public function withdraw() {
        if($this['balance'] <= 0) {
            return true;
        }

        // 先处理账户余额
        $refund = $this['balance'];
        $this['balance'] = 0;
        $this['refund'] = $this['refund'] + $refund;
        if(empty($this->save())) {
            Log::error('convert balance to refund account fail');
            return false;
        }
        // 新增提现记录
        $withdrawLog = WithdrawLog::create([
            'user_id' => $this['id'],
            'refund' => $refund,
            'request_time' => date("y-m-d H:i:s",time()),
            'status' => WithdrawLog::WITHDRAW_APPLY_STATUS,
        ]);
        // 若数据操作失败则回退
        if(empty($withdrawLog)) {
            Log::error('create withdraw log fail, roll back user balance account');
            $this['balance'] = $refund;
            $this['refund'] = $this['refund'] - $refund;
            $this->save();
            return false;
        }

        // 只退本次提现申请记录
    	$totalRefund = $refund;

    	$orders = BorrowOrder::getRefundableOrders($user['id']);
    	$refundDetail = array();

    	foreach($orders as $order) {
    		$orderid = $order['orderid'];
            $refundable = $order['refundable'];
    		// check
    		if($refundable <= 0) {
    			Log::debug("order $orderid all refund");
                $order['refund_no'] = self::ORDER_REFUND_FINISH;
    			$order->save();
    			continue;
    		}

            $refundNow = $refund > $refundable ? $refundable : $refund;
    		Log::debug('try to refund:' . $refundNow . ', orderid: ' . $orderid);

            $refundResult = EasyWeChat::payment()->refund($orderid, $orderid."-R".$order['refund_no'], $order['paid'], $refundNow);
            if ( $ret['return_code'] == 'SUCCESS' && $ret['result_code'] == 'SUCCESS' ) {
                $refundResult = true;
                LOG::DEBUG('wxpay refund success');
            } else if($ret['err_code'] == 'NOTENOUGH' || $ret['err_code'] == 'SYSTEMERROR') {
                // 以下策略保证尽可能的少分单退款
                // 若微信支付账户 未结算金额不足,则暂停此次退款,等到账户余额充足再自动退款
                // 若微信返回系统错误, 则等待下一轮退款再重试
                LOG::DEBUG('try again next time');
                break;
            } else if($ret['err_code'] == 'REFUND_FEE_MISMATCH') {
                // 若出现订单金额不一致的问题, 则代表前一次提交的一次退款失败了, 然后紧接着分单尝试退了部分
                // 等到下一轮尝试退款时,这个订单该退的金额和之前不一致了(别的单退了部分)
                // 则这次采用同样的退款编号但金额不同的退款会失败, 需要将退款编号更新一下再次尝试退款
                LOG::DEBUG('REFUND_FEE_MISMATCH, increment refundno, and try again next time');
                C::t('#mcs#mcs_tradelog')->update($orderid, array('refundno' => $order['refundno'] + 1, 'lastupdate' => time()));
                break;
            } else {
                $refundResult = false;
            }

    		if ($refundResult) {
    			LOG::DEBUG('refund success, orderid: ' . $orderid . ', refund:' . $refundFee);
    			$order['refunded'] += $refundFee;
    			$refundno = $order['price'] == $order['refunded'] ? ORDER_ALL_REFUNDED : ($order['refundno']+1);
    			$refundDetail[] = [$orderid, $refundFee]; // 记录退款详情

    			$ret = C::t('#mcs#mcs_tradelog')->update($orderid, array('refundno' => $refundno, 'refunded'=>$order['refunded'], 'lastupdate' => time()));
    			if(! $ret) {
    				LOG::ERROR('update order refund no fail');
    			}
    		} else {
    			LOG::ERROR('wxpay refund fail, orderid: ' . $orderid . ', refund:' . $refundFee . ', ret:' . print_r($ret, true));
    			LOG::ERROR('continue next order');
    			continue;
    		}


    		$refund -= $refundFee;
    		$refund = round($refund, 2);
    		LOG::DEBUG('left:' . $refund);
    		if($refund <= 0)
    			break;
    	}

    	if($refund > 0) {
    		LOG::ERROR('refund no complete, and left:' . $refund);
    	} else {
    		LOG::DEBUG('refund all success');
    	}

    	$refund = $totalRefund - $refund;
    	$refund = round($refund, 2);

    	$ret = true;
    	if($refund > 0) {
    		$ret = C::t('#mcs#mcs_user')->refund($uid, $refund);
    	}
    	if($ret) {
    		LOG::DEBUG('update user account money success');
    	} else {
    		LOG::ERROR('update user account money fail');
    	}
    	return ['refunded' => $refund, 'batch_no' => $batchNo? : '', 'detail' => $refundDetail];
    }
}
