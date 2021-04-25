<?php

namespace App\Model\member;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MemberCategoryModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'id';

    protected $table = "member_category";

    protected $guarded = [];

    public $timestamps = false;
}
