<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ValidateRole extends Controller
{
    public function checkAuthADM(){
        $this->authorize('sudata');
        $role = Auth::user()->role_id;

        return $role;
    }

    public function checkAuthCS(){
        $this->authorize('csdata');
        $role = Auth::user()->role_id;

        return $role;
    }

    public function checkAuthALL(){
        if(Auth::user()->role_id == 1){
            $this->authorize('sudata');
        }else if(Auth::user()->role_id == 3){
            $this->authorize('csdata');
        }
        $role = Auth::user()->role_id;

        return $role;
    }

    public function defineLayout($role){
        switch ($role){
            case 1: //SUDATA
                return 'layouts.app_admin';
                break;
            case 3: //CSDATA
                return 'layouts.app_cs';
                break;
            default:
                return 'layouts.app_unauthorized';
                break;
        }
    }
}
