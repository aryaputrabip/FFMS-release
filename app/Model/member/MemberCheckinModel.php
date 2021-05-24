<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberCheckinModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "logcheckin";

    protected $guarded = [];

    public $timestamps = false;
}
