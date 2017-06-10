<?php namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public $fillable = [
        'name',
        'display_name',
        'description'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|unique:roles',
        'display_name' => 'required'
    ];
}
