<?php

namespace App\Model\membership;

use Illuminate\Database\Eloquent\Model;

class membershipListCacheModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "membership_memberlist";

    protected $guarded = [];

    public $timestamps = false;
}
