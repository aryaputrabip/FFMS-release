<?php

namespace App\Model\payment;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use DB;

class BankModel extends Model
{
    protected $connection = 'pgsql_secure';

    protected $primaryKey = 'id';

    protected $table = "bank_option";

    protected $guarded = [];

    public $timestamps = false;
}
