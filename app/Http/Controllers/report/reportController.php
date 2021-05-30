<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberLogModel;
use App\Model\memberData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class reportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['title'] = "Report";
        $data['board'] = "";
        $data['username'] = Auth::user()->name;
        $data['role'] = Auth::user()->role_id;

        for ($i = 1; $i <= 12; $i++) {
            $dt = date("F", mktime(0, 0, 0, $i, 10));
            $data['bulan'][] = $dt . ' ' . $i;
        }/* 
        foreach ($data['bulan'] as $b) {
            dd(explode(' ', $b));
        } */
        //dd($data);
        $data['tahun'] = MemberLogModel::select(DB::raw('EXTRACT(year FROM date) AS year'))->where('aksi', 'register')->distinct()->orderBy('year', 'ASC')->get();
        return view('report.index2', $data);
    }

    public function dataReg(Request $r)
    {
        $tglMulai = $r->tglMulai;
        $tglAkhir = $r->tglAkhir;
        $tahun = (isset($r->tahun)) ? $r->tahun : date('Y');
        //dd($tglMulai);
        //$data['alldata'] = [];
        if ($tglMulai) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '>', $tglMulai)
                ->where('aksi', 'register')
                ->whereRaw('extract(year from date) = ' . $tahun)
                ->get();
        } else if ($tglAkhir) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '<', $tglAkhir)
                ->where('aksi', 'register')
                ->whereRaw('extract(year from date) = ' . $tahun)
                ->get();
        } else if ($tglMulai && $tglAkhir) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '<', $tglAkhir)
                ->where('date', '>', $tglMulai)
                ->where('aksi', 'register')
                ->whereRaw('extract(year from date) = ' . $tahun)
                ->get();
        } else {
            $data['alldata'] = DB::table('logmember')
                ->where('aksi', 'register')
                ->whereRaw('extract(year from date) = ' . $tahun)
                ->get();
        }

        $month = [];
        $dataMonth = [];
        $dataProfitMonth = [];
        $dataProfitMemberPerMonth = [];
        $dataProfitSesiPerMonth = [];
        $dataCheckInPerMonth = [];
        $dataCheckOutPerMonth = [];
        $dataPembelianPerMonth = [];
        $dataMemberPerMonth = [];
        $dataLakiPerMonth = [];
        $dataPerempuanPerMonth = [];
        $dataPerformaMarketingPerMonth = [];
        $dataPerformaMarketingPerMonth_profit = [];
        $dataPerformaPTPerMonth = [];
        $dataPerformaPTPerMonth_profit = [];
        

        for ($i = 1; $i <= 12; $i++) {
            $dt =  date("F", mktime(0, 0, 0, $i, 10));
            $month[] = $dt;
            $dataMonth[] = MemberLogModel::where('aksi', 'register')
                ->whereRaw("extract(month from date) = " . $i)
                ->whereRaw('extract(year from date) = ' . $tahun)
                ->count();

            // $dataProfitMonth[] = MemberLogModel::whereRaw('EXTRACT(year from date) = ' . $tahun)->sum('transaction');
            $dataProfitMonth[] = MemberLogModel::whereRaw("extract(month from date) = " . $i)
                ->whereRaw('EXTRACT(year from date) = ' . $tahun)->sum('transaction');

            $dataProfitMemberPerMonth[] = MemberLogModel::where('aksi', 'membership')->whereRaw("extract(month from date) = " . $i)
                ->whereRaw('EXTRACT(year from date) = ' . $tahun)->sum('transaction');

            $dataProfitSesiPerMonth[] = MemberLogModel::where('aksi', 'sesi')->whereRaw("extract(month from date) = " . $i)
                ->whereRaw('EXTRACT(year from date) = ' . $tahun)->sum('transaction');

            $dataPembelianPerMonth[] =  MemberLogModel::whereRaw("extract(month from date) = " . $i)
            ->whereRaw('EXTRACT(year from date) = ' . $tahun)->count();

            $dataMemberPerMonth[] = DB::table('memberdata')->whereRaw("extract(month from m_startdate) = " . $i)
            ->whereRaw('EXTRACT(year from m_startdate) = ' . $tahun)->count();

            $dataLakiPerMonth[] = DB::table('memberdata')->where('gender', 'Laki-laki')->whereRaw("extract(month from m_startdate) = " . $i)
            ->whereRaw('EXTRACT(year from m_startdate) = ' . $tahun)->count();

            $dataPerempuanPerMonth[] = DB::table('memberdata')->where('gender', 'Perempuan')->whereRaw("extract(month from m_startdate) = " . $i)
            ->whereRaw('EXTRACT(year from m_startdate) = ' . $tahun)->count();

            $dpm = cal_days_in_month(CAL_GREGORIAN, $i, Carbon::now()->year);
            //$dayPerMonth[] = cal_days_in_month(CAL_GREGORIAN, $i, Carbon::now()->year);
            for ($j = 1; $j <= $dpm; $j++) {
                // $dataPerDay[$i][$j] = MemberLogModel::where('aksi', 'register')
                //     ->whereRaw("extract(month from date) = " . $i)
                //     ->whereRaw('extract(day from date) = ' . $j)
                //     ->whereRaw('extract(year from date) = ' . $tahun)
                //     ->count();
                $dataPerDay[$i][$j] = 0;
            }
        }

        // Data Top 10
        $TopMarketing = DB::select('select marketingdata.mark_id, marketingdata.name, COUNT(cache_read.session_price) AS total, SUM(cache_read.session_price) AS total_profit
                                                        FROM cache_read
                                                        JOIN marketingdata ON (marketingdata.mark_id = cache_read.id_marketing)
                                                        --WHERE EXTRACT(YEAR from marketingdata.join_from) = '.$tahun.'
                                                        GROUP BY marketingdata.mark_id, marketingdata.name
                                                        ORDER BY total DESC
                                                        LIMIT 10
                                                ');

        $TopPT = DB::select('select ptdata.pt_id, ptdata.name, COUNT(cache_read.session_price) AS total, SUM(cache_read.session_price) AS total_profit
                                                        FROM cache_read
                                                        JOIN ptdata ON (ptdata.pt_id = cache_read.id_marketing)
                                                        --WHERE EXTRACT(YEAR from ptdata.join_from) = '.$tahun.'
                                                        GROUP BY ptdata.pt_id, ptdata.name
                                                        ORDER BY total DESC
                                                        LIMIT 10
                                                ');

        
        $dataTopMarketing = [];
        $dataTopMarketingCount = [];
        $dataTopMarketingProfit = [];
        foreach($TopMarketing as $t) {
            $dataTopMarketing[] = $t->name;
            $dataTopMarketingCount[] = $t->total;
            $dataTopMarketingProfit[] = $t->total_profit;
        }

        $dataTopPT = [];
        $dataTopPTCount = [];
        $dataTopPTProfit = [];
        foreach($TopPT as $t) {
            $dataTopPT[] = $t->name;
            $dataTopPTCount[] = $t->total;
            $dataTopPTProfit[] = $t->total_profit;
        }
        

        // Data Per Tahun
        $tahun = MemberLogModel::select(DB::raw('EXTRACT(year FROM date) AS year'))->distinct()->orderBy('year', 'ASC')->get();
        $year = [];
        $dataPerYear = [];
        foreach($tahun as $t) {
            $year[] = $t->year;
            $dataPerYear[] = MemberLogModel::where('aksi', 'register')->whereRaw('EXTRACT(year from date) = ' . $t->year)->count();
            $profitPerYear[] = MemberLogModel::whereRaw('EXTRACT(year from date) = ' . $t->year)->sum('transaction');
        }

        // Data Perhari 
        $data['dataPerDay'] = $dataPerDay;

        // Data Perbulan
        $data['month'] = $month;
        $data['dataMonth'] = $dataMonth;

        // Data Profit Perbulan
        $data['profitPerMonth'] = $dataProfitMonth;
        $data['profitMemberPerMonth'] = $dataProfitMemberPerMonth;
        $data['profitSesiPerMonth'] = $dataProfitSesiPerMonth;

        // Data Aktivitas Member Perbulan
        $data['pembelianPerMonth'] = $dataPembelianPerMonth;

        // Data Performa Member Perbulan
        $data['memberPerMonth'] = $dataMemberPerMonth;
        $data['memberLakiPerMonth'] = $dataLakiPerMonth;
        $data['memberPerempuanPerMonth'] = $dataPerempuanPerMonth;

        // Data Top 10
        $data['topMarketing'] = $dataTopMarketing;
        $data['topMarketingCount'] = $dataTopMarketingCount;
        $data['topMarketingProfit'] = $dataTopMarketingProfit;
        $data['topPT'] = $dataTopPT;
        $data['topPTCount'] = $dataTopPTCount;
        $data['topPTProfit'] = $dataTopPTProfit;


        //Data Pertahun
        $data['year'] = $year;
        $data['dataPerYear'] = $dataPerYear;
        $data['profitPerYear'] = $profitPerYear;
        //dd($data);
        return json_encode($data);
    }
}
