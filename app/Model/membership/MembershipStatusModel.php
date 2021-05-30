<?php

namespace App\Model\membership;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MembershipStatusModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "membership_status";

    protected $guarded = [];

    public $timestamps = false;
}
