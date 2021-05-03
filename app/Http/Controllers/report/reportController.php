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
        return view('report.index2', $data);
    }

    public function dataReg(Request $r)
    {
        $tglMulai = $r->tglMulai;
        $tglAkhir = $r->tglAkhir;
        //dd($tglMulai);
        //$data['alldata'] = [];
        if ($tglMulai) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '>', $tglMulai)
                ->where('aksi', 'register')
                ->get();
        } else if ($tglAkhir) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '<', $tglAkhir)
                ->where('aksi', 'register')
                ->get();
        } else if ($tglMulai && $tglAkhir) {
            $data['alldata'] = DB::table('logmember')
                ->where('date', '<', $tglAkhir)
                ->where('date', '>', $tglMulai)
                ->where('aksi', 'register')
                ->get();
        } else {
            $data['alldata'] = DB::table('logmember')
                ->where('aksi', 'register')
                ->get();
        }

        $month = [];
        $dataMonth = [];

        for ($i = 1; $i <= 12; $i++) {
            $dt =  date("F", mktime(0, 0, 0, $i, 10));
            $month[] = $dt;
            $dataMonth[] = MemberLogModel::where('aksi', 'register')
                ->whereRaw("extract(month from date) = " . $i)
                ->count();
            $dpm = cal_days_in_month(CAL_GREGORIAN, $i, Carbon::now()->year);
            //$dayPerMonth[] = cal_days_in_month(CAL_GREGORIAN, $i, Carbon::now()->year);
            for ($j = 1; $j <= $dpm; $j++) {
                $dataPerDay[$i][$j] = MemberLogModel::where('aksi', 'register')
                    ->whereRaw("extract(month from date) = " . $i)
                    ->whereRaw('extract(day from date) = ' . $j)
                    ->count();
            }
        }

        $data['month'] = $month;
        $data['dataMonth'] = $dataMonth;
        //$data['dayPerMonth'] = $dayPerMonth;
        $data['dataPerDay'] = $dataPerDay;
        //dd($data);
        return json_encode($data);
    }
}
