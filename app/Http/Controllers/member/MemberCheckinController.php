<?php

namespace App\Http\Controllers\member;

use App\Model\member\MemberModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MemberCheckinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Check-In';
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            return view('member.management.checkin', compact('title','username','role','app_layout'));
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

    public function preview(Request $request){
        if($request->ajax()){
            $data['data'] = MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("cache_read as cacheData", "cacheData.author", "=", "PK.member_id")
                ->leftjoin("ptdata as mPTData", "mPTData.pt_id", "=", "cacheData.id_pt")
                ->leftjoin("marketingdata as mMarketingData", "mMarketingData.mark_id", "=", "cacheData.id_marketing")
                ->join("member_status as mStatus", "mStatus.mstatus_id", "=", "PK.status")
                ->select(
                    'PK.id',
                    'PK.member_id',
                    'PK.photo',
                    'PK.name',
                    'PK.gender',
                    'PK.job',
                    'PK.company',
                    'PK.phone',
                    'PK.email',
                    'PK.status',
                    'PK.visitlog',
                        'PK.checkin_status',
                    'mMarketingData.name as marketing',
                    'mPTData.name as pt',
                )->where("member_id", $request->uid)->first();

            $data['url'] = "";
            return $data;

        }
    }

    function checkin(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = MemberModel::where('member_id', $r->dataIDMember)->update([
            'checkin_status' => true,
            'visitlog' => $r->visitLog + 1
        ]);

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.checkin')->with(['success' => 'Check-in Berhasil!']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.checkin')->with(['success' => 'Check-in Berhasil']);
        }
    }
}
