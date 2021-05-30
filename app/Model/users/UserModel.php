<?php

namespace App\Model\users;

use Illuminate\Database\Eloquent\Model;

class UserModel extends Model
{
    protected $connection = 'pgsql_secure';

    protected $primaryKey = 'id';

    protected $table = "users";

    protected $guarded = [];

    public $timestamps = false;
}
