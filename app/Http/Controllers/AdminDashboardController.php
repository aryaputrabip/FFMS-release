<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\ValidateRole;
use App\Http\Controllers\report\reportController;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $this->authorize('sudata');

        $title = 'Dashboard';
        $username = Auth::user()->name;
        $role = Auth::user()->role_id;
        $jMember = MemberModel::count();
        $memberActive = MemberModel::where('status', 1)->count();
        $memberCuti = MemberModel::where('status', 3)->count();
        $totalSales = $this->asRupiah(MemberLogModel::sum('transaction'));

        $chartData = new reportController();
        $memberChart = $chartData->getChart('member_total');

        $current_month = $datenow->month;
        $current_year = $datenow->year;

        $monthList = $this->getMonthList();
        $yearlist = $this->getYearList();

        return view('admin_dashboard', compact('title','username','role','jMember','memberActive','memberCuti','totalSales', 'memberChart', 'current_month', 'current_year','monthList','yearlist'));
    }

    public function getMemberData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();
        $data = array();

        if($r->ajax()){
            $generateLength = $this->defineGenerateLength($r->filterType, $r->filterYear, $r->filterMonth, $datenow);

            if($r->filterType == "day"){
                for($i=1; $i<=$generateLength; $i++){
                    $queryResult = $this->queryData("member_total", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult2 = $this->queryData("member_lk", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult3 = $this->queryData("member_pr", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    array_push($data, [$i, $i, $queryResult2, $queryResult3,  $queryResult]);
                };
            }else if($r->filterType == "month"){
                for($i=1; $i<=$generateLength; $i++){
                    $queryResult = $this->queryData("member_total", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult2 = $this->queryData("member_lk", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult3 = $this->queryData("member_pr", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    array_push($data, [$i, $this->getMonth($i), $queryResult2, $queryResult3, $queryResult]);
                }
            }else{
                $indexer = 1;
                for($i=$this->getYearList()[0]; $i<=$generateLength; $i++){
                    $queryResult = $this->queryData("member_total", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult2 = $this->queryData("member_lk", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    $queryResult3 = $this->queryData("member_pr", $r->filterType, $r->filterMonth, $r->filterYear, $i);
                    array_push($data, [$indexer, $this->getYearList()[0], $queryResult2, $queryResult3, $queryResult]);
                    $indexer++;
                }
            }

            return $data;
        }
    }

    function defineGenerateLength($filterType, $filterYear, $filterMonth, $currentDate){
        if($filterYear == $currentDate->year){
            if($filterType == "month"){
                return $currentDate->month;
            }else if($filterType == "day"){
                if($filterMonth == $currentDate->month){
                    return $currentDate->day;
                }else{
                    return $currentDate->daysInMonth;
                }
            }else{
                $year = $this->getYearList();
                return (end($year));
            }
        }else{
            if($filterType == "month"){
                return 12;
            }else if($filterType == "day"){
                if($filterMonth == $currentDate->month){
                    return $currentDate->day;
                }else{
                    return $currentDate->daysInMonth;
                }
            }else{
                $year = $this->getYearList();
                return (end($year));
            }
        }
    }

    function queryData($type, $filterBy, $filterMonth, $filterYear, $index){
        if($filterBy != "year"){
            $memberQuery = MemberModel::whereMonth('created_at', '=', $index);
        }else{
            $memberQuery = MemberModel::whereYear('created_at', '=', $index);
        }

        switch($type) {
            case "member_new":
                $query = $this->executeBaseQuery("member", $filterBy, $filterMonth, $filterYear, $memberQuery);

                return $query->count();
                break;
            case "member_lk":
                $query = $this->executeBaseQuery("member", $filterBy, $filterMonth, $filterYear, $memberQuery);

                return $query->where("gender","Laki-laki")->count();
                break;
            case "member_pr":
                $query = $this->executeBaseQuery("member", $filterBy, $filterMonth, $filterYear, $memberQuery);

                return $query->where("gender","Perempuan")->count();
                break;
            case "member_total":
                if($filterMonth < 10){
                    $filterMonth = "0".$filterMonth;
                }

                if($filterYear == ""){
                    $adminController = new AdminDashboardController();
                    $filterYear = $adminController->getYearList()[0];
                }

                $toDate = Carbon::createFromTimeString($filterYear."-".$filterMonth."-01 00:00:01")->daysInMonth;

                if($filterBy == "day"){
                    $from = $filterYear."-".$filterMonth."-01 00:00:01";
                    $to = $filterYear."-".$filterMonth."-".$toDate." 23:59:59";
                }else if($filterBy == "month"){
                    $from = $filterYear."-01-01 00:00:01";
                    $to = $filterYear."-".$filterMonth."-".$toDate." 23:59:59";
                }else{
                    $toDate2 = Carbon::createFromTimeString($filterYear."-12-01 00:00:01")->daysInMonth;
                    $from = $filterYear."-01-01 00:00:01";
                    $to = $filterYear."-12-".$toDate2." 23:59:59";
                }

                return MemberModel::whereBetween('created_at', [$from, $to])->count();
                break;
        }
    }

    function executeBaseQuery($type, $filterBy, $filterMonth, $filterYear, $query){
        switch($type){
            case "member":
                if($filterBy == "month"){
                    if(isset($filterYear)) {
                        return $query->whereYear('created_at', '=', $filterYear);
                    }else{
                        return $query;
                    }
                }else if($filterBy == "day"){
                    if(isset($filterYear)) {
                        if(isset($filterMonth)) {
                            return $query->whereMonth('created_at', '=', $filterMonth)
                                ->whereYear('created_at', '=', $filterYear);
                        } else {
                            return $query->whereYear('created_at', '=', $filterYear);
                        }
                    }else{
                        if(isset($filterMonth)) {
                            return $query->whereMonth('created_at', '=', $filterMonth);
                        } else {
                            return $query;
                        }
                    }
                }else{
                    return $query;
                }
                break;
        }
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }

    public function getMonth($index){
        switch ($index){
            case 1: return 'Januari'; break;
            case 2: return 'Februari'; break;
            case 3: return 'Maret'; break;
            case 4: return 'April'; break;
            case 5: return 'Mei'; break;
            case 6: return 'Juni'; break;
            case 7: return 'Juli'; break;
            case 8: return 'Agustus'; break;
            case 9: return 'September'; break;
            case 10: return 'Oktober'; break;
            case 11: return 'November'; break;
            case 12: return 'Desember'; break;
        }
    }

    public function getMonthList(){
        $data = array();
        for($i=1; $i<=12; $i++){
            array_push($data, [$this->getMonth($i), $i]);
        }

        return $data;
    }

    public function getYearList(){
        $data = array();

        $dt1 = MemberModel::select('created_at')->orderBy('created_at','ASC')->first();
        $dt2 = MemberModel::select('created_at')->orderBy('created_at','DESC')->first();

        $oldest = substr($dt1->created_at, 0, 4);
        $newest = substr($dt2->created_at, 0, 4);

        for($i=(int)$oldest; $i<=(int)$newest; $i++){
            array_push($data, $i);
        }

        return $data;
    }
}
