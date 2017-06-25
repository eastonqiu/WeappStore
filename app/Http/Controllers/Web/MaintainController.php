<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Http\Controllers\Controller;
use App\Models\BorrowOrder;
use App\Models\Device;
use Illuminate\Http\Request;

class MaintainController extends Controller {

    /*
     * 安装
     */
    public function install(Request $request, $deviceId) {
        if(empty(Device::find($deviceId))) {
            abort(404);
        }
        return view('maintain.install', ['deviceId'=> $deviceId]);
    }

}
