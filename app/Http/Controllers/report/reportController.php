<?php

namespace App\Http\Controllers\report;

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
            $data['memberChart'] = $this->generateChart("member", "month", $datenow->year);

            return view('report.index', $data);
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

    //CHANGE CHART DATA (WHEN FILTER APPLIED)
    public function updateChartData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        //$data['revenueInit'] = $this->initChartData("revenue", "month", $r->year);
        $data['revenueData'] = $this->chartData("revenue", "total", "month", $r->year);
        $data['revenueDataMembership'] = $this->chartData("revenue", "membership", "month", $r->year);
        $data['revenueDataSesi'] = $this->chartData("revenue", "sesi", "month", $r->year);

        $data['activityCheckin'] = $this->chartData("activity", "total", "month", $r->year);
        $data['activityPembelian'] = $this->chartData("activity", "pembelian", "month", $r->year);

        $data['memberData'] = $this->chartData("member", "total", "month", $r->year);
        $data['memberLK'] = $this->chartData("member", "lk", "month", $r->year);
        $data['memberPR'] = $this->chartData("member", "pr", "month", $r->year);
        $data['memberBaru'] = $this->chartData("member", "baru", "month", $r->year);

        return $data;
    }

    //GETTING CHART DATASET & LABELS
    public function chartData($chart, $subchart, $filterBy, $filterYear){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $data['dataset'] = array();
        $data['labels'] = array();

        if($filterBy == "month"){
            //IF FILTERED BY MONTH
            //& CURRENT FILTERED YEAR == CURRENT YEAR
            if($filterYear == $datenow->year){
                //TOTAL MONTH = CURRENT MONTH (NOT 12 MONTHS)
                for($i=1; $i<=$datenow->month; $i++){
                    array_push($data['labels'], $this->getMonth($i));
                    array_push(
                        $data['dataset'],
                        $this->queryFilteredData($chart, $subchart, $i, $filterBy, $filterYear)
                    );

                }
            }else{
                //& CURRENT FILTERED YEAR != CURRENT YEAR
                //TOTAL MONTH = 12 MONTHS
                for($i=1; $i<=12; $i++){
                    array_push($data['labels'], $this->getMonth($i));
                    array_push(
                        $data['dataset'],
                        $this->queryFilteredData($chart, $subchart, $i, $filterBy, $filterYear)
                    );
                }
            }
            //==========================
        }else if($filterBy == "year"){
            //IF FILTERED BY YEAR

        }

        return $data;
    }

    //QUERY CHART DATA BY FILTER
    public function queryFilteredData($chart, $subchart, $index, $filterType, $filterYear){
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
                    //IF QUERING TOTAL MEMBER CHART LAYER
                    $enddate = Carbon::create($filterYear, $index);

                    if($filterYear == ""){
                        $cdata1 = MemberModel::select('created_at')->orderBy('created_at','ASC')->first();
                        $cdata2 = MemberModel::select('created_at')->orderBy('created_at','DESC')->first();

                        $filterYearFrom = Carbon::parse($cdata1->created_at)->year;
                        $filterYearTo = Carbon::parse($cdata2->created_at)->year;
                    }else{
                        $filterYearFrom = $filterYear;
                        $filterYearTo = $filterYear;
                    }

                    $from = date('Y-m-d', strtotime($filterYearFrom.'-01'.'01'));

                    if($index < 10){
                        $to = date('Y-m-d', strtotime($filterYearTo.'-0'.$index."-".(string) $enddate->daysInMonth));
                    }else{
                        $to = date('Y-m-d', strtotime($filterYearTo.'-'.$index."-".(string) $enddate->daysInMonth));
                    }

                    if($filterYear == "" || $filterYear == null){
                        return MemberModel::whereBetween('created_at', [$from, $to])
                            ->count();
                    }else{
                        return MemberModel::whereBetween('created_at', [$from, $to])
                            ->whereYear('created_at', '=', $filterYear)
                            ->count();
                    }

                }else if($subchart == "lk"){
                    //IF QUERING TOTAL MEMBER LAKI-LAKI CHART
                    if($filterYear == "" || $filterYear == null){
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->where('gender', '=', "Laki-laki")
                            ->count();
                    }else{
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->whereYear('created_at', '=', $filterYear)
                            ->where('gender', '=', "Laki-laki")
                            ->count();
                    }
                }else if($subchart == "pr"){
                    //IF QUERING TOTAL MEMBER PREMPUAN CHART
                    if($filterYear == "" || $filterYear == null){
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->where('gender', '=', "Perempuan")
                            ->count();
                    }else{
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->whereYear('created_at', '=', $filterYear)
                            ->where('gender', '=', "Perempuan")
                            ->count();
                    }
                }else if($subchart == "baru"){
                    if($filterYear == "" || $filterYear == null){
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->count();
                    }else{
                        return MemberModel::whereMonth('created_at', '=', $index)
                            ->whereYear('created_at', '=', $filterYear)
                            ->count();
                    }
                }
                break;
        }
    }

    //KONFIGURASI GENERATE CHART UNTUK DI RENDER
    public function generateChart($chartName, $filterType, $filterYear){
        $initData = $this->initChartData($chartName, $filterType, $filterYear);

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
    public function initChartData($chart, $filterType, $filterYear){
        switch ($chart){
            case "revenue":
                //GUNAKAN METODE revenueData UNTUK MENDAPATKAN DATASET & LABEL DARI CHART REVENUE
                $getData = $this->chartData($chart, "total", $filterType, $filterYear);
                $getData2 = $this->chartData($chart, "membership", $filterType, $filterYear);
                $getData3 = $this->chartData($chart, "sesi", $filterType, $filterYear);

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
                $getData = $this->chartData($chart, "total", $filterType, $filterYear); //TOTAL = CHECK-IN
                $getData2 = $this->chartData($chart, "pembelian", $filterType, $filterYear);

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
                $getData = $this->chartData($chart, "total", $filterType, $filterYear); //TOTAL
                $getData2 = $this->chartData($chart, "lk", $filterType, $filterYear);
                $getData3 = $this->chartData($chart, "pr", $filterType, $filterYear);
                $getData4 = $this->chartData($chart, "baru", $filterType, $filterYear);

                $init['type'] = 'line'; //CHART TYPE
                $init['name'] = 'memberChart'; //CHART ID IN HTML
                $init['size'] = ['width' => 400, 'height' => 100]; //CHART SIZE (PASANG SEGINI)
                $init['labels'] = $getData['labels']; //CHART LABEL
                $init['dataset'] = [
                    $this->setTableData("line", 'Total Member', $getData['dataset'], 'rgba(252,87,94,0.0)', 'rgb(6,173,41)', 2, false),
                    $this->setTableData("line", 'Total Member (Laki-laki)', $getData2['dataset'], 'rgba(23,152,222,0)', 'rgb(6,115,173)', 2, true),
                    $this->setTableData("line", 'Total Member (Perempuan)', $getData3['dataset'], 'rgba(224,13,97,0)', 'rgb(224,7,68)', 2, true),
                    $this->setTableData("bar", 'Member Baru', $getData4['dataset'], 'rgba(37,147,220,0.2)', 'rgb(37,147,220)', 2, true),
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
