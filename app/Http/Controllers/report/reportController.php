<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberLogModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Fx3costa\LaravelChartJs;

class reportController extends Controller
{


    public function index()
    {
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $data['title'] = "Report";
        $data['board'] = "";
        $data['username'] = Auth::user()->name;
        $data['role'] = Auth::user()->role_id;

        $data['revenueChart'] = $this->generateChart("revenue", "month", $datenow->year);

        return view('report.index', $data);
    }

    //CHANGE CHART DATA (WHEN FILTER APPLIED)
    public function updateChartData(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $datenow = Carbon::now();

        $data['revenueInit'] = $this->initChartData("revenue", "month", $r->year);
        $data['revenueData'] = $this->chartData("revenue", "total", "month", $r->year);
        $data['revenueDataMembership'] = $this->chartData("revenue", "membership", "month", $r->year);
        $data['revenueDataSesi'] = $this->chartData("revenue", "sesi", "month", $r->year);

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
                    return MemberLogModel::whereMonth('date', '=', $index)
                        ->whereYear('date', '=', $filterYear)
                        ->sum('transaction');
                }else if($subchart == "membership"){
                    //IF QUERING TOTAL REVENUE CHART (BY MEMBERSHIP TRANSACTION)
                    return MemberLogModel::whereMonth('date', '=', $index)
                        ->whereYear('date', '=', $filterYear)
                        ->where('aksi', '=', "membership")
                        ->sum('transaction');
                }else if($subchart == "sesi"){
                    //IF QUERING TOTAL REVENUE CHART (BY SESI TRANSACTION)
                    return MemberLogModel::whereMonth('date', '=', $index)
                        ->whereYear('date', '=', $filterYear)
                        ->where('aksi', '=', "sesi")
                        ->sum('transaction');
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

                break;
            case "member":

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
