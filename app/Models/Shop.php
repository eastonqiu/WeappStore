<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use SoftDeletes;
	
    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];
}
