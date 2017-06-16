<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BorrowController extends Controller {

    /*
     * 借流程首页
     */
    public function index(Request $request) {
        $deviceId = $request->input('d_id');
        $productId = array_keys(BorrowOrder::PRODUCT_LIST)[0];
        return view('borrow.index', ['d_id'=> $deviceId, 'p_id' => $productId]);
    }

    /*
     * 下单流程
     */
    public function order(Request $request) {
        $deviceId = $request->input('d_id');
        $productId = $request->input('p_id');
        return BorrowOrder::createOrder(session('user')['id'], $productId, $deviceId);
    }
}
