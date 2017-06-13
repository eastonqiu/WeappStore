<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowOrder extends Model
{
    use SoftDeletes;
	
    protected $guarded = [
        'id', 'refund_no', 'refundable', 'created_at', 'updated_at', 'deleted_at'
    ];
}
