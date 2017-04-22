<?php

namespace App;

use App\User;
use TCG\Voyager\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weapp extends Model
{
    use SoftDeletes;

    public function author_id() {
    	return $this->belongsTo(User::class);
    }

    public function categories() {
    	return $this->belongsToMany(Category::class, 'weapp_category')->withTimestamps();
    }
}
