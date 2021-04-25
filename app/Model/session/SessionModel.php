<?php

namespace App\Model\session;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class SessionModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "sessiondata";

    protected $guarded = [];

    public $timestamps = false;
}
