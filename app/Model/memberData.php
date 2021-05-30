<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class memberData extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "memberdata";

    protected $guarded = [];

    public $timestamps = false;
}
