<?php

namespace App\Model\membership;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MembershipModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'mship_id';

    protected $table = "membership";

    protected $guarded = [];

    public $timestamps = false;
}
