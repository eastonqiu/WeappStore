<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function stations() {
        return $this->hasMany(Station::class);
    }

    public function feeStrategy() {
        return $this->hasOne(FeeStrategy::class);
    }

    public function getFeeStrategy() {
        $strategy = $this->feeStrategy();
        if(empty($strategy)) {
            return FeeStrategy::defaultValue();
        } else {
            return json_decode($strategy['value'], true);
        }
    }

    /*
    *   中心经纬度周边的商铺列表
    *   @param $lng 经度
    *   @param $lat 纬度
    *   @param $enable 商铺状态是否启用
    */
    public static function nearbyShops($lng, $lat, $enable) {
        $data['ak'] = GAODE_MAP_SERVER_KEY;
    	$data['location'] = "$lng,$lat";
    	$data['geotable_id'] = GEOTABLE_ID;
    	$data['radius'] = 2000;	//半径2km
    	$data['page_size'] = 50; //返回数量，最大为50
    	$data['filter'] = "enable:1";
    	$api = "http://api.map.baidu.com/geosearch/v3/nearby";
    	$scurl = new sCurl( $api, 'GET', $data );
    	$ret = $scurl->sendRequest();
    	$ret = json_decode($ret, true);
    	if($ret['status'] == 0) {
    		$shop_stations = $ret['contents'];
    		foreach ($shop_stations as $k => $v) {
    			$shop_station_ids[] = $v['sid'];
    		}
    		return $shop_station_ids;
    	}
    	return false;
    }
}
