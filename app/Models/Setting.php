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
                    // $value = xxx;
                    break;
                case self::FEE_STRATEGY:
                    // default
                    break;
            }
        }
        return $value;
    }
}
