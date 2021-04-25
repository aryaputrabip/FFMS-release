<?php

namespace App\Model\system;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class SysModel extends Model
{
    protected $connection = 'pgsql_secure';

    protected $primaryKey = 'sysid';

    protected $table = "sysdata";

    protected $guarded = [];

    public $timestamps = false;
}
