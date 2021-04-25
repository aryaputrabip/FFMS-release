<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class CutiMemberModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "cutidata";

    protected $guarded = [];

    public $timestamps = false;
}
