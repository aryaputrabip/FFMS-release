<?php

namespace App\Model\pt;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class LogPTModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "log_pt";

    protected $guarded = [];

    public $timestamps = false;
}
