<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Support\Facades\DB;

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

    public function withdraws() {
        return $this->hasMany(Withdraw::class);
    }

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
        $withdraw = Withdraw::create([
            'user_id' => $this['id'],
            'refund' => $refund,
            'request_time' => date("y-m-d H:i:s",time()),
            'status' => Withdraw::WITHDRAW_APPLY_STATUS,
        ]);
        // 若数据操作失败则回退
        if(empty($withdraw)) {
            Log::error('create withdraw log fail, roll back user balance account');
            $this['balance'] = $refund;
            $this['refund'] = $this['refund'] - $refund;
            $this->save();
            return false;
        }
        // 退款
        $withdraw->refund();
        return true;
    }
}
