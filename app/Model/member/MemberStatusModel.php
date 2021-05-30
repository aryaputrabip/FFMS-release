<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberStatusModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'mstatus_id';

    protected $table = "member_status";

    protected $guarded = [];

    public $timestamps = false;
}
