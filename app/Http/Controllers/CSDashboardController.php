<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidateRole;
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
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthCS();

        $title = 'Dashboard';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $tMember = $this->getTotalMember();
            $tMemberBaru = $this->getMemberToday();
            $tAktivitas = $this->getActivityToday();
            $tRevenue = $this->getRevenueToday();

            return view('cs_dashboard', compact('title','username','role','app_layout','tMember','tMemberBaru','tAktivitas','tRevenue'));
        }
    }

    public function getTotalMember(){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        return MemberModel::from('memberdata')->count();
    }

    public function getMemberToday(){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $data = MemberModel::whereDate('created_at', '=', Carbon::today()->toDateString())->count();

        return $data;
    }

    public function getActivityToday(){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $data = MemberLogModel::whereDate('date', '=', Carbon::today())->count();

        return $data;
    }

    public function getRevenueToday(){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $data = MemberLogModel::whereDate('date', '=', Carbon::today()->toDateString())->sum('transaction');

        return $this->asRupiah($data);
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
