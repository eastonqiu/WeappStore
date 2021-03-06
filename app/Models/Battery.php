<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Battery extends Model
{
	use SoftDeletes;

    protected $guarded = [
        'created_at', 'updated_at', 'deleted_at'
    ];

	const BATTERY_INSIDE = 0;
	const BATTERY_OUTSIDE = 1;

	public function device() {
		return $this->belongsTo(Device::class);
	}
}
