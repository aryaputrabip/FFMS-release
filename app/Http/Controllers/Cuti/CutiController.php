<?php

namespace App\Http\Controllers\Cuti;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\member\CutiMemberModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\membership\MembershipModel;
use App\Model\pt\PersonalTrainerModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CutiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $title = 'Data Cuti Member';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $jMember = MemberModel::from('memberdata')->count();
            $jMemberCuti = CutiMemberModel::count();
            $jMemberCutiLK = CutiMemberModel::from("cutidata as PK")
                ->join("memberdata as mData", "mData.member_id", "PK.member_id")
                ->where('mData.gender', '=', 'Laki-laki')->count();
            $jMemberCutiPR = CutiMemberModel::from("cutidata as PK")
                ->join("memberdata as mData", "mData.member_id", "PK.member_id")
                ->where('mData.gender', '=', 'Perempuan')->count();

            return view('cuti.index',
                compact('title','username','app_layout','role',
                        'jMemberCuti','jMemberCutiLK','jMemberCutiPR'));
        }
    }

    public function checkAuth(){
        $role = Auth::user()->role_id;

        return $role;
    }

    public function getCutiData(Request $request){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        if($request->ajax()){
            $data = CutiMemberModel::from("cutidata as PK")
                ->join("memberdata as mData", "mData.member_id", "=", "PK.member_id")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "mData.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->select(
                    'PK.id as ID',
                    'PK.member_id as member_id',
                    'mData.name as name',
                    'mShipData.name as membership',
                    'mShipType.type as type',
                    'PK.cuti_from',
                    'PK.cuti_expired'
                )
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('membership', function ($data) {
                    return '<div class="text-left">'.$data->membership.'</div>';
                })
                ->addColumn('membership_type', function ($data) {
                    return '<div class="text-left">'.$data->type.'</div>';
                })
                ->addColumn('start_cuti', function ($data) {
                    return '<div class="text-left">'.date("d M Y", strtotime($data->cuti_from)).'</div>';
                })
                ->addColumn('end_cuti', function ($data) {
                    return '<div class="text-left">'.date("d M Y", strtotime($data->cuti_expired)).'</div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="text-center">
                            <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#modal-cuti-manager" onclick="cutiManager(`'.$data->member_id.'`)">
                                <i class="fas fa-eye fa-sm"></i>
                            </button>
                            </div>';
                })
                ->rawColumns(['action', 'name', 'membership', 'membership_type', 'start_cuti', 'end_cuti'])
                ->make(true);
        }
    }

    public function preview(Request $request){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

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
                    'mMarketingData.name as marketing',
                    'mPTData.name as pt'
                )->where("member_id", $request->uid)->first();


            $data['url'] = "";
            return $data;

        }
    }

    public function checkCapability(Request $request){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        if($request->ajax()){
            $data['check'] = CutiMemberModel::where('member_id', $request->userID)->count();

            if($data['check'] == 0){
                $data['data'] = MemberModel::where('member_id', $request->userID)->first();

                //DURASI CUTI MAKSIMAL ADALAH SISA BULAN - 1
                //(sisakan 1 bulan terakhir agar tidak bisa mengajukan cuti)
                $restMonth = $date_now->diffInMonths($data['data']->m_enddate);
                $thedate = Carbon::parse($data['data']->m_enddate);
                $thedate->toDateTimeString();

                $data['pass'] = $restMonth - 1;
                $data['olddate'] = $thedate->format('d M Y');
                $data['newdate'] = $thedate->addMonths($request->duration)->format('d M Y');
                $data['currentdate'] = $date_now->format('d/m/Y');
                $data['endcuti'] = $date_now->addMonths($request->duration)->format('d/m/Y');
                $data['endcutiformat'] = $date_now->addMonth($request->duration);

                return $data;
            }else{
                $data['pass'] = null;

                return $data;
            }
        }
    }

    public function approve(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $check['check'] = CutiMemberModel::where('member_id', $r->activeMemberID)->count();

        if($check['check'] == 0){
            $data = CutiMemberModel::create([
                'member_id' => $r->activeMemberID,
                'cuti_from' => $date_now,
                'cuti_expired' => $r->endCutiDate,
                'return_old_data' => $r->oldEndDate,
                'created_by' => Auth::user()->role_id,
                'created_at' => $date_now
            ]);

            $data2 = MemberModel::where('member_id', $r->activeMemberID)->update([
                'status' => 3,
                'm_enddate' => $r->newMembershipEnd,
                'updated_at' => $date_now,
                'updated_by' =>Auth::user()->role_id
            ]);

            $log = MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Pengajuan Cuti Member Selama '.$r->activeStartDate.' Bulan',
                'category' => 4,
                'status' => 'Approved',
                'author' => $r->activeMemberID,
                'aksi' => 'cuti'
            ]);

            if(Auth::user()->role_id == 1){
                return redirect()->route('suadmin.cuti.index')->with(['success' => 'Member Berhasil Dicutikan!']);
            }elseif(Auth::user()->role_id == 2){
//              //
            }elseif(Auth::user()->role_id == 3){
                return redirect()->route('cs.cuti.index')->with(['success' => 'Member Berhasil Dicutikan!']);
            }
        }
    }

    public function abortCuti(Request $request){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        if($request->ajax()){
            $data['today'] = $date_now->format('d M Y');
            $data['data'] = CutiMemberModel::where('member_id', $request->uid)->first();
            $data['member'] = MemberModel::select('member_id', 'm_startdate', 'm_enddate', 'membership')
                                ->where('member_id', $request->uid)->first();

            $data['membership'] = MembershipModel::where('mship_id', $data['member']->membership)->first();

            $data['old_cuti_expired'] = Carbon::parse($data['data']->cuti_expired)->format('d M Y');
            $data['old_expired'] = Carbon::parse($data['member']->m_enddate)->format('d M Y');
//            $data['new_expired'] = Carbon::parse($data['member']->m_startdate)
//                                    ->addMonths($data['membership']->duration)->format('d M Y');

            $data['new_expired'] = $data['data']->return_old_data;

            return $data;
        }
    }

    public function remove(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        $exec = CutiMemberModel::destroy($r->formCuti);
        $data = MemberModel::where('member_id', $r->formMember)->update([
            'status' => 1,
            'm_enddate' => $r->formExpired,
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->role_id
        ]);

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.cuti.index')->with(['success' => 'Cuti Member Berhasil Dihapus!']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.cuti.index')->with(['success' => 'Cuti Member Berhasil Dihapus!']);
        }
    }
}
