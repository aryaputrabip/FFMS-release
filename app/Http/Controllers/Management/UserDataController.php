<?php

namespace App\Http\Controllers\Management;

use App\Model\users\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ValidateRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function account()
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $title = 'Manage My Account';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $user_data = UserModel::where("id", Auth::user()->id)->first();

            if($user_data->role_id == 1){
                $user_data->role = "Administrator";
            }else{
                $user_data->role = "Customer Service";
            }

            return view('user.management.account_management',
                compact('title','username','app_layout','role','user_data'));
        }
    }

    public function getAccountData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        if($r->ajax()){
            $data['data'] = UserModel::from("users as USER")
                ->select(
                    'USER.id as id',
                    'USER.name as name',
                    'USER.role_id as type',
                    'USER.email as email'
                )->where("id", Auth::user()->id)->first();

            if($data['data']->type == 1){
                $data['data']->type = "Administrator";
            }else{
                $data['data']->type = "Customer Service";
            }

            return $data;
        }
    }

    public function editAccountData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        if($r->dataUserNama == "" || $r->dataUserNama == null){
            if(Auth::user()->role_id == 1){
                if(Auth::user()->role_id == 1){
                    return redirect()->route('suadmin.management.index')->with(['failed' => 'Account Gagal Diubah']);
                }elseif(Auth::user()->role_id == 2){
//              //
                }elseif(Auth::user()->role_id == 3){
                    return redirect()->route('cs.management.index')->with(['failed' => 'Account Gagal Diubah']);
                }
            }
        }

        if(isset($r->dataUserPass)){
            $data = UserModel::where('id', Auth::user()->id)->update([
                'name' => $r->dataUserNama,
                'password' => Hash::make($r->dataUserPass),
                'updated_at' => $date_now
            ]);
        }else{
            $data = UserModel::where('id', Auth::user()->id)->update([
                'name' => $r->dataUserNama,
                'updated_at' => $date_now
            ]);
        }

        if(Auth::user()->role_id == 1){
            if(Auth::user()->role_id == 1){
                return redirect()->route('suadmin.management.account')->with(['success' => 'Account Berhasil Diubah']);
            }elseif(Auth::user()->role_id == 2){
//              //
            }elseif(Auth::user()->role_id == 3){
                return redirect()->route('cs.management.account')->with(['success' => 'Account Berhasil Diubah']);
            }
        }
    }
}
