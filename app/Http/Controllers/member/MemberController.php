<?php
namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();

        if(isset($role)){
            if($this->authAsSuAdminOrAdmin($role)){
                return redirect()->route('suadmin.index');
            }else{
                $title = 'Data Member';
                $username = Auth::user()->name;

                return view('member.index', compact('title','username','role'));
            }
        }
    }

    public function checkAuth(){
        $this->authorize('csdata');
        $role = Auth::user()->role_id;

        return $role;
    }

    public function authAsSuAdminOrAdmin($role){
        if($role == 1 || $role == 2){
            return true;
        }else{
            return false;
        }
    }
}
