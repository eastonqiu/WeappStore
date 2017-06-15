<?php

namespace App\Http\Controllers\API\Device;

use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Device;

class DeviceController extends Controller {
	use Helpers;

	function __construct() {
	}

	public function syncSetting(Request $request) {
		return Device::syncSetting($request->input('mac'), $request->input('device'));
	}

	public function syncBattery(Request $request) {
		return Device::syncBattery(
			$request->input('device_id'),
			$request->input('device'),
			$request->input('batteries')
		);
	}

	public function removeBattery(Request $request) {
		return Device::removeBattery(
			$request->input('device_id'),
			$request->input('batteries')
		);
	}

	public function borrowConfirm(Request $request) {
		return Device::borrowConfirm(
			$request->input('device_id'),
			$request->input('orderid'),
			$request->input('battery'),
			$request->input('status')
		);
	}

	public function returnBack(Request $request) {
		return  Device::returnBack(
			$request->input('device_id'),
			$request->input('battery')
		);
	}

	public function pushCommand(Request $request) {
		return [1,2];
	}

	public function getLogsToken(Request $request) {
		return [1,2];
	}
}
