<?php

namespace App\Modules\Core\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $guarded = [];
    protected $hidden = ['password'];
}
