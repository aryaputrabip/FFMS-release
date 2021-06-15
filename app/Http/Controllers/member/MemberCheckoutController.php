<?php

namespace App\Http\Controllers\member;

use App\Model\member\MemberModel;
use App\Model\member\MemberStatusModel;
use App\Model\membership\MembershipModel;
use App\Model\membership\MembershipTypeModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MemberCheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Check-Out Manager';
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);
            $memberCheckin = MemberModel::from('memberdata')->where('checkin_status', true)->count();
            $membership = MembershipModel::select('name')->get();
            $memberStatus = MemberStatusModel::select('status')->get();
            $membershipType = MembershipTypeModel::select("type")->get();
            $memberLK = MemberModel::from('memberdata')->where('checkin_status', true)->where('gender', '=', 'Laki-laki')->count();
            $memberPR = MemberModel::from('memberdata')->where('checkin_status', true)->where('gender', '=', 'Perempuan')->count();

            return view('member.management.logout', compact('title','username','role','app_layout', 'memberCheckin','membership','memberStatus', 'membershipType', 'memberLK', 'memberPR'));
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

    public function getCheckinMemberData(Request $request){
        if($request->ajax()){
            $data = MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->join("member_status as mStatus", "mStatus.mstatus_id", "=", "PK.status")
                ->join("logcheckin as logCheckin", "logCheckin.author", "=", "PK.member_id")
                ->select(
                    'PK.id',
                    'PK.member_id',
                    'PK.name',
                    'PK.status as memberStatus',
                    'PK.m_startdate',
                    'PK.m_enddate',
                    'mStatus.mstatus_id as mStatusID',
                    'mStatus.status as status',
                    'mShipData.name as membership',
                    'mShipData.duration as duration',
                    'mShipType.type as type',
                    'logCheckin.date as checkinFrom'
                )
                ->where('checkin_status', '=', true)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('status', function ($data) {
                    if($data->status == "Non-Aktif"){
                        return '<div class="text-center text-danger">'.$data->status.'</div>';
                    }else{
                        return '<div class="text-center text-success">'.$data->status.'</div>';
                    }
                })
                ->addColumn('membership', function ($data) {
                    return '<div class="text-left">'.$data->membership.'</div>';
                })
                ->addColumn('membership_type', function ($data) {
                    return '<div class="text-left">'.$data->type.'</div>';
                })
                ->addColumn('checkin_date', function ($data) {
                    return '<div class="text-left">'.date("d M Y (H:m:s)", strtotime($data->checkinFrom)).'</div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="text-center">
                            <button class="btn btn-sm btn-danger" onclick="checkoutMember(\''.$data->member_id.'\');">
                                <i class="fas fa-calendar-minus fa-sm mr-1"></i> Checkout
                            </button>
                            </div>';
                })
                ->rawColumns(['action', 'name', 'status', 'membership', 'membership_type', 'checkin_date', 'date_expired'])
                ->make(true);
        }
    }

    public function getCheckoutMemberData (Request $r){
        $data['member'] = MemberModel::from('memberdata')->where('member_id', $r->member_id)->first();
        return $data;
    }

    public function checkoutMember(Request $r){
        $member = MemberModel::where('member_id', $r->checkoutMemberID)->update([
            'checkin_status' => false
        ]);

        $role = Auth::user()->role_id;

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.checkout')->with(['success' => 'Member Berhasil Checkout!']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.checkout')->with(['success' => 'Member Berhasil Checkout!']);
        }
    }
}
