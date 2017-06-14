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
        return $this->hasMany(Station:class);
    }

    public function feeStrategy() {
        return $this->hasOne(FeeStrategy::class);
    }

    public function getFeeStrategy() {
        $strategy = $this->feeStrategy;
        if(empty($strategy)) {
            $strategy = Settings::get(Settings::FEE_STRATEGY);
        } else {
            $strategy = $strategy['value'];
        }
        return json_decode($strategy, true);
    }
}
