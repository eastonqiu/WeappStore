<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function device() {
        return $this->hasOne(Device::class);
    }

    public function shop() {
        return $this->belongsTo(Shop::class);
    }
}
