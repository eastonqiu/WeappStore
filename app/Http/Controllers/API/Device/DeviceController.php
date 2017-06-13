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
		return Device::syncSetting($request->input('mac'));
	}

	public function syncBattery() {
		return Device::syncBattery(
			$request->input('device_id'),
			$request->input('device_info'),
			$request->input('batteries')
		);
	}

	public function removeBattery() {
		return Device::syncBattery(
			$request->input('device_id'),
			$request->input('$batteries')
		);
	}

	public function getLogsToken() {
		return [1,2];
	}

	public function borrowConfirm() {
		return [1,2];
	}

	public function returnBack() {
		return [1,2];
	}

	public function pushCommand() {
		return [1,2];
	}
}
