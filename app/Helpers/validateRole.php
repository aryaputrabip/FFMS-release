<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class validateRole {
    public function checkAuth($role){
        $this->authorize($role);
        $role = Auth::user()->role_id;

        return $role;
    }

    public function defineLayout($role){
        switch ($role){
            case 'sudata':
                return 'layouts.app_admin';
                break;
            case 'admdata':
                return 'layout.app_admin';
                break;
            case 'csdata':
                return 'layout.app_cs';
                break;
            default:
                return 'layouts.app_unauthorized';
                break;
        }
    }
}
