<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes;

    const DEVICE_STRATEGY = 'device_strategy';
    const FEE_STRATEGY = 'fee_strategy';

    protected $primaryKey = 'skey';

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function get($key) {
        $value = Setting::find($key);
        // set default value
        if(empty($value)) {
            switch($key) {
                case self::DEVICE_STRATEGY:
                    // default
                    $value = [
                		"sync_interval"  => "3600", //单位秒
                		"adv_url"            => "http://7xr4wn.dl1.z0.glb.clouddn.com/kaichang_dev.avi",
                		"app_version"       => "1",
                		"app_url"        => "http://" . env('SERVER_DOMAIN', 'test.com'). "/static/apk/mcsclient.apk",
                		"app_package"    => "com.lingyunstrong.mcsclient",
                		"app_start_class" => "MainActivity",
                    ];
                    $value = json_encode($value);
                    break;
                case self::FEE_STRATEGY:
                    // default
                    break;
            }
        }
        return $value;
    }
}
