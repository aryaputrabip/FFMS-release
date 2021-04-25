<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $connection = 'pgsql_secure';

    protected $fillable = [
        'name'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
