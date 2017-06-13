<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowOrder extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'orderid';

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

    // 失败, 故障原因见 sub status
    const ORDER_STATUS_FAIL = 4;
    // sub status
    const ORDER_SUB_STATUS_FAIL = [
        41 => '网络超时',
        42 => '电机故障',
    ];


    /*
		订单更新幂等性检查, 保证多次请求的结果和一次请求的结果是一致的
		即短时间内并发多次更新, 只能有一次更新是有效的, 防止多次更新造成的一系列错误问题
		解决由于前端多次触发或由于网络重试导致的多次更新问题
		通过lastupdate的更新锁来实现, 3s内并发的请求均视为同一个请求
		可用于支付回调,借出确认,归还等等订单更新的场景
		返回是否合法, 即可是否可继续往下执行
	*/
	public static function idempotent($orderid)
	{
		return BorrowOrder::where('orderid', $orderid)
                ->whereRaw('unix_timestamp(updated_at) < ' . (time()-3))
                ->update(['updated_at' => date("y-m-d H:i:s",time())];
	}


}
