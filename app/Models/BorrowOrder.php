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
    const ORDER_STATUS_CONFIRM = 2;
    const ORDER_STATUS_RETURN = 3;

    // sub status
    // 借出
    const ORDER_STATUS_BORROW_CONFIRM_FIRST = 21;


}
