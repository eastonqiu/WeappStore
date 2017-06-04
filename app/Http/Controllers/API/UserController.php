<?php 

namespace App\Http\Controllers\API;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller {
	use Helpers;
	
	function __construct() {
		// 		$this->middleware();
	}
	
	public function profile() {
		return $this->auth->user();
	}

	public function logout() {
        $this->auth->logout();
        return response()->json(['msg' => 'ok']);;
    }
}
