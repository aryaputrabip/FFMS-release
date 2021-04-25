<?php

namespace App\Http\Controllers;

use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CSDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $role = Auth::user()->role_id;

        if(isset($role)){
            if($role == 1){
                return redirect()->route('suadmin.index');
            }else if($role == 2){
                return redirect()->route('suadmin.index');
            }else{
                $this->checkAuth();

                $title = 'Dashboard';
                $username = Auth::user()->name;
                $tMember = $this->getTotalMember();
                $tMemberBaru = $this->getMemberToday();
                $tAktivitas = $this->getActivityToday();

                return view('cs_dashboard', compact('title','username','role','tMember','tMemberBaru','tAktivitas'));
            }
        }
    }

    public function checkAuth(){
        $this->authorize('csdata');
    }

    public function getTotalMember(){
        return MemberModel::from('memberdata')->count();
    }

    public function getMemberToday(){
        $data = MemberModel::whereDate('created_at', '=', Carbon::today())->count();

        return 'IN_PROGRESS';
    }

    public function getActivityToday(){
        $data = MemberLogModel::whereDate('date', '=', Carbon::today())->count();

        return $data;
    }
}
