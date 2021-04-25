<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "memberdata";

    protected $guarded = [];

    public $timestamps = false;
}
