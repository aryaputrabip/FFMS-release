<?php

namespace App\Model\marketing;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class MarketingModel extends Model
{
    protected $connection = 'pgsql';

    protected $primaryKey = 'mark_id';

    protected $table = "marketingdata";

    protected $guarded = [];

    public $timestamps = false;
}
