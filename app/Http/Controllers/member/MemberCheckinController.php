<?php

namespace App\Http\Controllers\member;

use App\Model\member\MemberCheckinModel;
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
                    'mPTData.name as pt'
                )->where("member_id", $request->uid)->first();

            $data['url'] = "";
            return $data;

        }
    }

    function checkin(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');
        $redirectMessage = ['success' => 'Check-in Berhasil!'];

        if($r->dataCheckinSource == 'checkin'){
            $preview = MemberModel::select("visitlog", "checkin_status")->where("member_id", $r->dataIDMember)->first();
        }elseif($r->dataCheckinSource  == 'member' || $r->dataCheckinSource  == 'view'){
            $preview = MemberModel::select("visitlog", "checkin_status")->where("member_id", $r->dataIDMemberCheckin)->first();
        }else{
            $preview = MemberModel::select("visitlog", "checkin_status")->where("member_id", $r->dataIDMember)->first();
        }

        if($preview->checkin_status){
            $redirectMessage = ['failed' => 'Member ini belum Checkout!'];
        }else{
            if($r->dataCheckinSource == 'checkin'){
                $data = MemberModel::where('member_id', $r->dataIDMember)->update([
                    'checkin_status' => true,
                    'visitlog' => ($preview->visitlog + 1)
                ]);

                $data2 = MemberCheckinModel::create([
                    'date' => $date_now,
                    'author' => $r->dataIDMember
                ]);
            }elseif($r->dataCheckinSource  == 'member' || $r->dataCheckinSource  == 'view'){
                $data = MemberModel::where('member_id', $r->dataIDMemberCheckin)->update([
                    'checkin_status' => true,
                    'visitlog' => ($preview->visitlog + 1)
                ]);

                $data2 = MemberCheckinModel::create([
                    'date' => $date_now,
                    'author' => $r->dataIDMemberCheckin
                ]);
            }
        }

        $redirectTo = $this->checkinSourceRedirect($r->dataCheckinSource);

        if($this->checkAuth() == 1){
            if($r->dataCheckinSource == "view"){
                return redirect()->route('suadmin.member'.$redirectTo, $r->dataIDMemberCheckin)->with($redirectMessage);
            }else{
                return redirect()->route('suadmin.member'.$redirectTo)->with($redirectMessage);
            }
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            if($r->dataCheckinSource == "view"){
                return redirect()->route('cs.member'.$redirectTo, $r->dataIDMemberCheckin)->with($redirectMessage);
            }else{
                return redirect()->route('cs.member'.$redirectTo)->with($redirectMessage);
            }
        }
    }

    public function checkinSourceRedirect($source){
        if($source == 'checkin'){
            return ".checkin";
        }else if($source == 'member'){
            return '.index';
        }else if($source  == 'view'){
            return '.view';
        }
    }
}
