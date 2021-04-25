<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberCacheModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'cid';

    protected $table = "cache_read";

    protected $guarded = [];

    public $timestamps = false;
}
