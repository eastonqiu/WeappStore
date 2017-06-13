<?php 

namespace App\Http\Controllers\API\Device;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeviceController extends Controller {
	use Helpers;
	
	function __construct() {
		// 		$this->middleware();
	}

	public function syncSetting() {
		return [1,2];
	}
	
	
}
