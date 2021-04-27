<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberLogModel;
use DB;
use Carbon;
class reportController extends Controller
{
    public function index(){
        $data['title'] = "Report";
        $data['board'] = "";

        return view('report.index',$data);
    }

    public function dataReg(Request $r){
        $tglMulai = $r->mulai;
        $tglAkhir = $r->akhir;

        $data = MemberLogModel::
                when($tglMulai, function($data, $tglMulai){
                  return $query->where('created_at','>',$tglMulai)->get();
                })
                ->when($tglAkhir, function($data, $tglAkhir){
                    return $query->where('created_at','<',$tglAkhir)->get();
                })
                ->where('aksi','register')
                ->get();

        //dd($data);
        return $data; 
    }
}
