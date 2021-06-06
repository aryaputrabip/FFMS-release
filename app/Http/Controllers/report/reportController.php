<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Sesi\SesiUseController;
use App\Model\member\MemberCheckinModel;
use App\Model\member\MemberModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberLogModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Fx3costa\LaravelChartJs;

class reportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();

        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();
        if(isset($role)){
            $data['title'] = "Report";
            $data['board'] = "";
            $data['username'] = Auth::user()->name;
            $data['role'] = Auth::user()->role_id;
            $data['app_layout'] = $this->defineLayout($role);

            $data['revenueChart'] = $this->generateChart("revenue", "month", $datenow->year);
            $data['activityChart'] = $this->generateChart("activity", "month", $datenow->year);
            $data['memberChart'] = $this->getChart("member_total");

            return view('report.index', $data);
        }
    }

    public function getChart($chart){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        switch($chart){
            case "member_total":
                return $this->generateChart("member", "month", $datenow->year, $datenow->month);
                break;
            case "cuti_total":
                return $this->generateChart("cuti", "month", $datenow->year, $datenow->month);
                break;
        }
    }

    public function checkAuth(){
        $role = Auth::user()->role_id;

        return $role;
    }

    public function defineLayout($role){
        if($role == 1){
            return 'layouts.app_admin';
        }else if($role == 2){
            return "";
        }else if($role == 3){
            return 'layouts.app_cs';
        }
    }

    public function updateMemberChartData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $data['memberData'] = $this->chartData("member", "total", $r->type, $r->year, $r->month);
        $data['memberLK'] = $this->chartData("member", "lk", $r->type, $r->year, $r->month);
        $data['memberPR'] = $this->chartData("member", "pr", $r->type, $r->year, $r->month);
        $data['memberBaru'] = $this->chartData("member", "baru", $r->type, $r->year, $r->month);

        return $data;
    }

    //CHANGE CHART DATA (WHEN FILTER APPLIED)
    public function updateChartData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        //$data['revenueInit'] = $this->initChartData("revenue", "month", $r->year);
        $data['revenueData'] = $this->chartData("revenue", "total", $r->type, $r->year, $r->month);
        $data['revenueDataMembership'] = $this->chartData("revenue", "membership", $r->type, $r->year, $r->month);
        $data['revenueDataSesi'] = $this->chartData("revenue", "sesi", $r->type, $r->year, $r->month);

        $data['activityCheckin'] = $this->chartData("activity", "total", $r->type, $r->year, $r->month);
        $data['activityPembelian'] = $this->chartData("activity", "pembelian", $r->type, $r->year, $r->month);

        $data['memberData'] = $this->chartData("member", "total", $r->type, $r->year, $r->month);
        $data['memberLK'] = $this->chartData("member", "lk", $r->type, $r->year, $r->month);
        $data['memberPR'] = $this->chartData("member", "pr", $r->type, $r->year, $r->month);
        $data['memberBaru'] = $this->chartData("member", "baru", $r->type, $r->year, $r->month);

        return $data;
    }

    //GETTING CHART DATASET & LABELS
    public function chartData($chart, $subchart, $filterBy, $filterYear, $filterMonth){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $adminController = new AdminDashboardController();

        $data['dataset'] = array();
        $data['labels'] = array();

        if($filterBy == "month"){
            if($filterYear == $datenow->year){
                for($i=1; $i<=$datenow->month; $i++){
                    array_push($data['labels'], $this->getMonth($i));
                }
            }else{
                for($i=1; $i<=12; $i++){
                    array_push($data['labels'], $this->getMonth($i));
                }
            }
        }else if($filterBy == "day"){
            if($filterYear == $datenow->year){
                if($filterMonth == $datenow->month){
                    for($i=1; $i<=$datenow->day; $i++){
                        array_push($data['labels'], $i);
                    }
                }else{
                    for($i=1; $i<=$datenow->daysInMonth; $i++){
                        array_push($data['labels'], $i);
                    }
                }
            }else{
                if($filterMonth == $datenow->month){
                    for($i=1; $i<=$datenow->day; $i++){
                        array_push($data['labels'], $i);
                    }
                }else{
                    for($i=1; $i<=$datenow->daysInMonth; $i++){
                        array_push($data['labels'], $i);
                    }
                }
            }
        }else{
            $year = $adminController->getYearList();
            $indexer = 0;

            for($i=$year[0]; $i<=end($year); $i++){
                array_push($data['labels'], $year[$indexer]);
                $indexer++;
            }
        }

        array_push($data['dataset'], $this->queryFilteredData($chart, $subchart, 0, $filterBy, $filterYear, $filterMonth));
        $data['dataset'] = $data['dataset'][0];

        return $data;
    }

    //QUERY CHART DATA BY FILTER
    public function queryFilteredData($chart, $subchart, $index, $filterType, $filterYear, $filterMonth){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $adminController = new AdminDashboardController();
        $generateLength = $adminController->defineGenerateLength($filterType, $filterYear, $filterMonth, $datenow);

        $adminController = new AdminDashboardController();
        $year = $adminController->getYearList();

        if($filterType != "year"){
            $indexer = 1;
        }else{
            $indexer = $year[0];
        }

        $data = array();

        switch ($chart){
            case "revenue":
                if($subchart == "total"){
                    //IF QUERING TOTAL REVENUE CHART LAYER
                    if($filterYear == "" || $filterYear == null){
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->sum('transaction');
                    }else{
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->whereYear('date', '=', $filterYear)
                            ->sum('transaction');
                    }

                }else if($subchart == "membership"){
                    //IF QUERING TOTAL REVENUE CHART (BY MEMBERSHIP TRANSACTION)
                    if($filterYear == "" || $filterYear == null){
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->where('aksi', '=', "membership")
                            ->sum('transaction');
                    }else{
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->whereYear('date', '=', $filterYear)
                            ->where('aksi', '=', "membership")
                            ->sum('transaction');
                    }

                }else if($subchart == "sesi"){
                    //IF QUERING TOTAL REVENUE CHART (BY SESI TRANSACTION)
                    if($filterYear == "" || $filterYear == null){
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->where('aksi', '=', "sesi")
                            ->sum('transaction');
                    }else{
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->whereYear('date', '=', $filterYear)
                            ->where('aksi', '=', "sesi")
                            ->sum('transaction');
                    }
                }
                break;
            case "activity":
                if($subchart == "total"){
                    //IF QUERING TOTAL CHECKIN CHART LAYER
                    if($filterYear == "" || $filterYear == null){
                        return MemberCheckinModel::whereMonth('date', '=', $index)
                            ->count();
                    }else{
                        return MemberCheckinModel::whereMonth('date', '=', $index)
                            ->whereYear('date', '=', $filterYear)
                            ->count();
                    }

                }else if($subchart == "pembelian"){
                    //IF QUERING TOTAL PEMBELIAN CHART (BY MEMBERSHIP TRANSACTION)
                    if($filterYear == "" || $filterYear == null){
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->where('aksi', '!=', "registrasi")
                            ->count();
                    }else{
                        return MemberLogModel::whereMonth('date', '=', $index)
                            ->whereYear('date', '=', $filterYear)
                            ->where('aksi', '!=', "registrasi")
                            ->count();
                    }

                }
                break;
            case "member":
                    if($subchart == "total"){
                        for($i=$indexer; $i<=$generateLength; $i++){
                            $queryResult = $adminController->queryData("member_new", $filterType, $filterMonth, $filterYear, $i);
                            array_push($data, $queryResult);
                        }
                    }else if($subchart == "lk"){
                        for($i=$indexer; $i<=$generateLength; $i++){
                            $queryResult = $adminController->queryData("member_lk", $filterType, $filterMonth, $filterYear, $i);
                            array_push($data, $queryResult);
                        }
                    }else if($subchart == "pr"){
                        for($i=$indexer; $i<=$generateLength; $i++){
                            $queryResult = $adminController->queryData("member_pr", $filterType, $filterMonth, $filterYear, $i);
                            array_push($data, $queryResult);
                        }
                    }else{
                        for($i=1; $i<=$filterMonth; $i++){
                            $queryResult = $adminController->queryData("member_total", $filterType, $i, $filterYear, $i);
                            array_push($data, $queryResult);
                        }
                    }

                    return $data;
                break;
            case "cuti":
                if($subchart == "total"){
                    for($i=$indexer; $i<=$generateLength; $i++){
                        $queryResult = $adminController->queryData("cuti_new", $filterType, $filterMonth, $filterYear, $i);
                        array_push($data, $queryResult);
                    }
                }else if($subchart == "lk"){
                    for($i=$indexer; $i<=$generateLength; $i++){
                        $queryResult = $adminController->queryData("cuti_lk", $filterType, $filterMonth, $filterYear, $i);
                        array_push($data, $queryResult);
                    }
                }else if($subchart == "pr"){
                    for($i=$indexer; $i<=$generateLength; $i++){
                        $queryResult = $adminController->queryData("cuti_pr", $filterType, $filterMonth, $filterYear, $i);
                        array_push($data, $queryResult);
                    }
                }else{
                    for($i=1; $i<=$filterMonth; $i++){
                        $queryResult = $adminController->queryData("cuti_total", $filterType, $i, $filterYear, $i);
                        array_push($data, $queryResult);
                    }
                }

                return $data;
                break;
        }
    }

    //KONFIGURASI GENERATE CHART UNTUK DI RENDER
    public function generateChart($chartName, $filterType, $filterYear, $filterMonth){
        $initData = $this->initChartData($chartName, $filterType, $filterYear, $filterMonth);

        $chart = app()->chartjs
            ->name($initData['name'])
            ->type($initData['type'])
            ->size($initData['size'])
            ->labels($initData['labels'])
            ->datasets($initData['dataset'])
            ->options($initData['options']);

        return $chart;
    }

    //KONFIGURASI ISI CHART
    public function initChartData($chart, $filterType, $filterYear, $filterMonth){
        switch ($chart){
            case "revenue":
                //GUNAKAN METODE revenueData UNTUK MENDAPATKAN DATASET & LABEL DARI CHART REVENUE
                $getData = $this->chartData($chart, "total", $filterType, $filterYear, $filterMonth);
                $getData2 = $this->chartData($chart, "membership", $filterType, $filterYear, $filterMonth);
                $getData3 = $this->chartData($chart, "sesi", $filterType, $filterYear, $filterMonth);

                $init['type'] = 'line'; //CHART TYPE
                $init['name'] = 'profitChart'; //CHART ID IN HTML
                $init['size'] = ['width' => 400, 'height' => 100]; //CHART SIZE (PASANG SEGINI)
                $init['labels'] = $getData['labels']; //CHART LABEL
                $init['dataset'] = [
                    $this->setTableData("line", 'Total Revenue', $getData['dataset'], 'rgba(6,95,173,0.1)', 'rgb(7,138,238)', 2, false),
                    $this->setTableData("line", 'Revenue (Membership)', $getData2['dataset'], 'rgba(173,64,6,0.1)', 'rgb(219,98,6)', 2, true),
                    $this->setTableData("line", 'Revenue (Sesi)', $getData3['dataset'], 'rgba(173,6,20,0.1)', 'rgb(219,6,6)', 2, true)
                ]; //CHART DATASET

                $init['options'] = [
                    $this->setTableOptions("top", "Total Revenue")
                ]; //CHART OPTIONS

                break;
            case "activity":
                $getData = $this->chartData($chart, "total", $filterType, $filterYear, $filterMonth); //TOTAL = CHECK-IN
                $getData2 = $this->chartData($chart, "pembelian", $filterType, $filterYear, $filterMonth);

                $init['type'] = 'line'; //CHART TYPE
                $init['name'] = 'activityChart'; //CHART ID IN HTML
                $init['size'] = ['width' => 400, 'height' => 100]; //CHART SIZE (PASANG SEGINI)
                $init['labels'] = $getData['labels']; //CHART LABEL
                $init['dataset'] = [
                    $this->setTableData("line", 'Check-In', $getData['dataset'], 'rgba(0,0,0,0)', 'rgb(37,147,220)', 2, false),
                    $this->setTableData("line", 'Pembelian', $getData2['dataset'], 'rgba(0,0,0,0)', 'rgb(9,187,89)', 2, true),
                ]; //CHART DATASET

                $init['options'] = [
                    $this->setTableOptions("top", "Check-In")
                ]; //CHART OPTIONS
                break;
            case "member":
                $getData = $this->chartData($chart, "total", $filterType, $filterYear, $filterMonth); //TOTAL
                $getData2 = $this->chartData($chart, "lk", $filterType, $filterYear, $filterMonth);
                $getData3 = $this->chartData($chart, "pr", $filterType, $filterYear, $filterMonth);
                $getData4 = $this->chartData($chart, "baru", $filterType, $filterYear, $filterMonth);

                $init['type'] = 'line'; //CHART TYPE
                $init['name'] = 'memberChart'; //CHART ID IN HTML
                $init['size'] = ['width' => 400, 'height' => 100]; //CHART SIZE (PASANG SEGINI)
                $init['labels'] = $getData['labels']; //CHART LABEL
                $init['dataset'] = [
                    $this->setTableData("bar", 'Total Member', $getData['dataset'], 'rgba(252,87,94,0.0)', 'rgb(6,173,41)', 2, false),
                    $this->setTableData("bar", 'Total Member (Laki-laki)', $getData2['dataset'], 'rgba(23,152,222,0)', 'rgb(6,115,173)', 2, true),
                    $this->setTableData("bar", 'Total Member (Perempuan)', $getData3['dataset'], 'rgba(224,13,97,0)', 'rgb(224,7,68)', 2, true),
                    $this->setTableData("line", 'Member Baru', $getData4['dataset'], 'rgba(37,147,220,0.2)', 'rgb(37,147,220)', 2, true),
                ]; //CHART DATASET

                $init['options'] = [
                    $this->setTableOptions("top", "Check-In")
                ]; //CHART OPTIONS
                break;
            case "member_top":

                break;
            case "marketing":

                break;
            case "marketing_top":

                break;
            case "pt":

                break;
            case "pt_top":

                break;
            case "cuti":
                $getData = $this->chartData($chart, "total", $filterType, $filterYear, $filterMonth); //TOTAL
                $getData2 = $this->chartData($chart, "lk", $filterType, $filterYear, $filterMonth);
                $getData3 = $this->chartData($chart, "pr", $filterType, $filterYear, $filterMonth);
                $getData4 = $this->chartData($chart, "baru", $filterType, $filterYear, $filterMonth);

                $init['type'] = 'line'; //CHART TYPE
                $init['name'] = 'memberChart'; //CHART ID IN HTML
                $init['size'] = ['width' => 400, 'height' => 100]; //CHART SIZE (PASANG SEGINI)
                $init['labels'] = $getData['labels']; //CHART LABEL
                $init['dataset'] = [
                    $this->setTableData("bar", 'Total Member', $getData['dataset'], 'rgba(252,87,94,0.0)', 'rgb(6,173,41)', 2, false),
                    $this->setTableData("bar", 'Total Member (Laki-laki)', $getData2['dataset'], 'rgba(23,152,222,0)', 'rgb(6,115,173)', 2, true),
                    $this->setTableData("bar", 'Total Member (Perempuan)', $getData3['dataset'], 'rgba(224,13,97,0)', 'rgb(224,7,68)', 2, true),
                    $this->setTableData("line", 'Member Baru', $getData4['dataset'], 'rgba(37,147,220,0.2)', 'rgb(37,147,220)', 2, true),
                ]; //CHART DATASET

                $init['options'] = [
                    $this->setTableOptions("top", "Check-In")
                ]; //CHART OPTIONS
                break;
        }

        return $init;
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

    //KONFIGURASI OPTIONS CHART
    public function setTableOptions($position, $title){
        $options = [
            'responsive' => true,
            'plugins' => [
                'legend' =>[
                    'position' => $position
                ],
                "title" => [
                    'display' => true,
                    "text" => $title
                ],
            ],
            'scales' => [
                'yAxes' => [
                    'ticks' => [
                        'beginAtZero' => true
                    ]
                ]
            ],
            'elements' => [
                'line' => [
                    'tension' => 0
                ]
            ]
        ];

        return $options;
    }

    public function setTableData($type, $label, $dataset, $background, $border, $width, $hidden){
        $data = [
            "type" => $type,
            "label" => $label,
            'data' => $dataset,
            'backgroundColor' => $background,
            'borderColor' => $border,
            'borderWidth' => $width,
            'hidden' => $hidden
        ];

        return $data;
    }
}
