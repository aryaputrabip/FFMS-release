<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberLogModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'log_id';

    protected $table = "logmember";

    protected $guarded = [];

    public $timestamps = false;
}
