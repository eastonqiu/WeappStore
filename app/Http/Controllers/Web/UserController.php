<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {

    /*
     * 用户档案
     */
    public function profile(Request $request) {
        $user = User::find(session('user_id'));
        $user['balance'] = round($user['balance'] / 100, 2);
        $user['deposit'] = round($user['deposit'] / 100, 2);
        $user['refund'] = round($user['refund'] / 100, 2);
        return view('user.profile', ['user'=> $user]);
    }

    /*
     * 余额提现页面
     */
    public function withdraw(Request $request) {
        $user = User::find(session('user_id'));
        $user['balance'] = round($user['balance'], 2);
        $user['deposit'] = round($user['deposit'], 2);
        $user['refund'] = round($user['refund'], 2);
        return view('user.withdraw', ['user'=> $user]);
    }

    /*
     * 提现申请
     */
    public function withdrawApply(Request $request) {
        $result = User::withdraw(session('user_id'));
        return $result;
    }

    /*
     * 租借记录
     */
    public function orders(Request $request) {
        $user = User::find(session('user_id'));
        $user['balance'] = round($user['balance'], 2);
        $user['deposit'] = round($user['deposit'], 2);
        $user['refund'] = round($user['refund'], 2);
        return view('user.orders', ['user'=> $user]);
    }

    /*
     * 提现记录
     */
    public function withdraws(Request $request) {
        $user = User::find(session('user_id'));
        $user['balance'] = round($user['balance'], 2);
        $user['deposit'] = round($user['deposit'], 2);
        $user['refund'] = round($user['refund'], 2);
        return view('user.withdraws', ['user'=> $user]);
    }

}
