<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberLogModel;
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
        $data['profitPerMonth'] = $dataProfitMonth;
        
        //Data Pertahun
        
        $data['year'] = $year;
        $data['dataPerYear'] = $dataPerYear;
        $data['profitPerYear'] = $profitPerYear;
        //dd($data);
        return json_encode($data);
    }
}
