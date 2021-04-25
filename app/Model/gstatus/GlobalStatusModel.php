<?php

namespace App\Model\gstatus;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class GlobalStatusModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'gstatus_id';

    protected $table = "global_status";

    protected $guarded = [];

    public $timestamps = false;
}
