<?php

namespace App\Model\pt;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class PersonalTrainerModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'pt_id';

    protected $table = "ptdata";

    protected $guarded = [];

    public $timestamps = false;
}
