<?php

namespace App\Http\Controllers\report;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\member\MemberLogModel;
use DB;
use Carbon;
use Illuminate\Support\Facades\Auth;

class reportController extends Controller
{   
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $data['title'] = "Report";
        $data['board'] = "";
        $data['username']= Auth::user()->name;
        $data['role']=Auth::user()->role_id;
        return view('report.index',$data);
    }

    public function dataReg(Request $r){
        $tglMulai = $r->mulai;
        $tglAkhir = $r->akhir;

        $data = MemberLogModel::
                when($tglMulai, function($data, $tglMulai){
                  return $data->where('created_at','>',$tglMulai)->get();
                })
                ->when($tglAkhir, function($data, $tglAkhir){
                    return $data->where('created_at','<',$tglAkhir)->get();
                })
                ->where('aksi','register')
                ->get();
 
        //dd($data);
        return $data; 
    }
}
