<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'pgsql_secure';

    protected $fillable = [
        'name'
    ];
}
