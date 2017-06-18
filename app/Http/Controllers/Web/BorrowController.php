<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\BorrowOrder;
use App\Models\Device;
use Illuminate\Http\Request;

class BorrowController extends Controller {

    /*
     * 借流程首页
     */
    public function index(Request $request, $deviceId) {
        if(empty(Device::find($deviceId))) {
            abort(404);
        }
        $productId = array_keys(BorrowOrder::PRODUCT_LIST)[0];
        return view('borrow.index', ['dId'=> $deviceId, 'pId' => $productId]);
    }

    /*
     * 下单流程
     */
    public function order(Request $request) {
        $deviceId = $request->input('dId');
        $productId = $request->input('pId');
        return BorrowOrder::createOrder(session('user_id'), $deviceId, $productId);
    }
}
