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
}
