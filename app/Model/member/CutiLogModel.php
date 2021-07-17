<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class CutiLogModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "log_cuti";

    protected $guarded = [];

    public $timestamps = false;
}
