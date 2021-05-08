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
                $tRevenue = $this->getRevenueToday();

                return view('cs_dashboard', compact('title','username','role','tMember','tMemberBaru','tAktivitas','tRevenue'));
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
        date_default_timezone_set("Asia/Jakarta");
        $data = MemberModel::whereDate('created_at', '=', Carbon::today()->toDateString())->count();

        return $data;
    }

    public function getActivityToday(){
        date_default_timezone_set("Asia/Jakarta");
        $data = MemberLogModel::whereDate('date', '=', Carbon::today())->count();

        return $data;
    }

    public function getRevenueToday(){
        date_default_timezone_set("Asia/Jakarta");
        $data = MemberLogModel::whereDate('date', '=', Carbon::today()->toDateString())->sum('transaction');


        return $this->asRupiah($data);
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
