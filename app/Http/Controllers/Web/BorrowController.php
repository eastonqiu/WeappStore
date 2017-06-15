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
    public function borrow() {
        return view('borrow.index');
    }
}
