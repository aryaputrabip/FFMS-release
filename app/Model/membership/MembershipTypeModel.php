<?php

namespace App\Model\membership;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MembershipTypeModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "membership_type";

    protected $guarded = [];

    public $timestamps = false;
}
