<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeAppController extends Controller {
	
	function __construct() {
// 		$this->middleware();
	}
	
	public function index(Request $request) {
		return view('app.weapp.index');
	}
	
	public function detail(Request $request) {
		return view('app.weapp.detail');
	}
}
