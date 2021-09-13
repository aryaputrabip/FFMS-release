<?php

namespace App\Http\Controllers\member;

use App\Exports\MemberCheckinExport;
use App\Exports\MemberExport;
use App\Http\Controllers\Auth\ValidateRole;
use App\Model\marketing\MarketingModel;
use App\Model\member\CicilanDataModel;
use App\Model\member\CutiMemberModel;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberCheckinModel;
use App\Model\member\MemberLogCategoryModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\member\MemberStatusModel;
use App\Model\memberData;
use App\Model\membership\MembershipCategoryModel;
use App\Model\membership\membershipListCacheModel;
use App\Model\membership\MembershipModel;
use App\Model\membership\MembershipTypeModel;
use App\Model\payment\BankModel;
use App\Model\payment\PaymentModel;
use App\Model\pt\PersonalTrainerModel;
use App\Model\session\SessionModel;
use App\Model\system\SysModel;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class MemberDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $title = 'Data Member';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $jMember = MemberModel::from('memberdata')->count();
            $memberActive = MemberModel::from('memberdata')->where('status', 1)->count();
            $memberLK = MemberModel::from('memberdata')->where('gender', '=', 'Laki-laki')->count();
            $memberPR = MemberModel::from('memberdata')->where('gender', '=', 'Perempuan')->count();
            $membership = MembershipModel::select('name')->get();
            $membershipType = MembershipTypeModel::select('type')->get();
            $memberStatus = MemberStatusModel::select('status')->get();

            return view('member.index', compact('title','username','role','jMember','memberActive','memberLK','memberPR','membership','membershipType','memberStatus','app_layout'));
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

    public function authAsSuAdminOrAdmin($role){
        if($role == 1 || $role == 2){
            return true;
        }else{
            return false;
        }
    }

    public function getMemberData(Request $request){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if($request->ajax()){
            $data = MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->join("member_status as mStatus", "mStatus.mstatus_id", "=", "PK.status")
                ->leftJoin("membership_memberlist as mShipList", "mShipList.author", "=", "PK.member_id")
                ->select(
                    'PK.id',
                    'PK.member_id',
                    'PK.name',
                    'PK.status as memberStatus',
                    'PK.m_startdate',
                    'PK.m_enddate',
                    'mStatus.mstatus_id as mStatusID',
                    'mStatus.status as status',
                    //'mShipData.name as membership',
                    'PK.membership as membership_member',
                    'mShipList.membership_id as membership',
                    'mShipData.duration as duration',
                    'mShipType.type as type'
                )
                ->orderBy('mShipList.start_date', 'ASC')
                ->get();

            $totalQuery = count($data);
            $arrayValidate = [];

            for($i=0; $i<$totalQuery; $i++){
                if (in_array($data[$i]->member_id, $arrayValidate)) {
                    $data->forget($i);
                }else{
                    array_push($arrayValidate, $data[$i]->member_id);
                    if(isset($data[$i]->membership)){
                        $getMembershipName = MembershipModel::where('mship_id', $data[$i]->membership)->first();
                        $data[$i]->membership = $getMembershipName->name;
                    }else{
                        if(isset($data[$i]->membership_member)){
                            $getMembershipName = MembershipModel::where('mship_id', $data[$i]->membership_member)->first();

                            if(isset($getMembershipName)){
                                $data[$i]->membership = $getMembershipName->name;
                            }else{
                                $data[$i]->membership = "-";
                            }
                        }else{
                            $data[$i]->membership = "-";
                        }
                    }
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('status', function ($data) {
                    if($data->status == "Non-Aktif" || $data->status == "Expired"){
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
                ->addColumn('date_from', function ($data) {
                    if($data->status == "Non-Aktif"){
                        return '<div class="text-left"> - </div>';
                    }else{
                        return '<div class="text-left">'.date("d M Y", strtotime($data->m_startdate)).'</div>';
                    }
                })
                ->addColumn('date_expired', function ($data) {
                    if($data->status == "Non-Aktif"){
                        return '<div class="text-left"> - </div>';
                    }else{
                        return '<div class="text-left">'.date("d M Y", strtotime($data->m_enddate)).'</div>';
//                        return '<div class="text-left">'. date("d M Y   ", strtotime("+".$data->duration." month", strtotime($data->m_startdate))).'</div>';
                    }
                })
                ->addColumn('action', function ($data) {

//                    if($data->memberStatus != 1){
//                        $activationMenu = '<a href="#MemberActivationModal" id="activateMember_'.$data->member_id.'" class="btn btn-default btn-sm" title="Aktivasi Member" data-toggle="modal" data-member="'.$data->member_id.'" onclick="activateMember(this.id)">
//                                <i class="fas fa-check text-success"></i>
//                            </a>';
//                    }else{
//                        $activationMenu = "";
//                    }

                    return '<div class="text-center">
                            <button class="btn btn-sm btn-dark" data-toggle="modal" data-target="#modal-action" onclick="modalMember(\''.$data->member_id.'\'); requestAction(\''.$data->member_id.'\', '.$data->duration.')">
                                <i class="fas fa-eye fa-sm"></i>
                            </button>
                            </div>';
                })
                ->rawColumns(['action', 'name', 'status', 'membership', 'membership_type', 'date_from', 'date_expired'])
                ->make(true);
        }
    }

    public function requestModalAction(Request $r){
        if($r->ajax()){
            $data['data'] = MemberModel::where("member_id", $r->id)->first();
            $editLink = "";
            $viewLink = "";

            if(Auth::user()->role_id == 1){
                $editLink = route('suadmin.member.edit', $r->id);
                $viewLink = route('suadmin.member.view', $r->id);
            }elseif(Auth::user()->role_id == 2){
                $editLink = "";
                $viewLink = "";
            }elseif(Auth::user()->role_id == 3){
                $editLink = route('cs.member.edit', $r->id);
                $viewLink = route('cs.member.view', $r->id);
            }

            if(isset($data['data'])){
                if($data['data']->status == 1){
                    if($this->checkAuth() == 1){
                        $routeCheckin = route("suadmin.member.checkin");
                        $routeCuti = route("suadmin.cuti.index");
                    }else if($this->checkAuth() == 2){

                    }else if($this->checkAuth() == 3){
                        $routeCheckin = route("cs.member.checkin");
                        $routeCuti = route("cs.cuti.index");
                    }

                    return '<button type="button" class="btn btn-success w-100 mb-2 font-weight-bold" onclick="checkinMember(`'.$r->id.'`)">
                                <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                            </button>
                            <button class="btn btn-outline-dark w-100" onclick="cutikanMember(`'.$r->id.'`)">
                                <i class="fas fa-calendar-minus fa-sm mr-1"></i> Cutikan Member
                            </button>
                            <hr>
                            <a href="'.$viewLink.'" class="btn btn-dark mb-2 w-100">
                                <i class="fas fa-eye fa-sm mr-1"></i> Lihat Data Member
                            </a>
                            <a href="'.$editLink.'" class="btn btn-warning w-100">
                                <i class="fas fa-pencil-alt fa-sm mr-1"></i> Edit Data Member
                            </a>';

                }else if($data['data']->status == 2){
                    return '<button href="#" class="btn btn-success w-100 mb-2 font-weight-bold" onclick="activateMember(`'.$r->id.'`, '.$r->duration.')">
                                <i class="fas fa-calendar-check fa-sm mr-1"></i> Aktivasi Member
                            </button>
                            <hr>
                            <a href="'.$viewLink.'" class="btn btn-dark mb-2 w-100">
                                <i class="fas fa-eye fa-sm mr-1"></i> Lihat Data Member
                            </a>
                            <a href="'.$editLink.'" class="btn btn-warning w-100">
                                <i class="fas fa-pencil-alt fa-sm mr-1"></i> Edit Data Member
                            </a>';
                }else if($data['data']->status == 3){
                    return '<a href="'.$viewLink.'" class="btn btn-dark mb-2 w-100">
                                <i class="fas fa-eye fa-sm mr-1"></i> Lihat Data Member
                            </a>
                            <a href="'.$editLink.'" class="btn btn-warning w-100">
                                <i class="fas fa-pencil-alt fa-sm mr-1"></i> Edit Data Member
                            </a>';
                }else if($data['data']->status == 4){
                    return '<button href="#" class="btn btn-success w-100 mb-2 font-weight-bold" onclick="extendMembership(`'.$r->id.'`, '.$r->duration.')">
                                <i class="fas fa-calendar-plus fa-sm mr-1"></i> Perpanjang Paket Member
                            </button>
                            <hr>
                            <a href="'.$viewLink.'" class="btn btn-dark mb-2 w-100">
                                <i class="fas fa-eye fa-sm mr-1"></i> Lihat Data Member
                            </a>
                            <a href="'.$editLink.'" class="btn btn-warning w-100">
                                <i class="fas fa-pencil-alt fa-sm mr-1"></i> Edit Data Member
                            </a>';
                }
            }
        }
    }

    public function getMemberMembership(Request $request, $id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if($request->ajax() && $id != ""){
            $data = membershipListCacheModel::from("membership_memberlist as mShipCache")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "mShipCache.membership_id")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->join("memberdata as PK", "PK.member_id", "=", "mShipCache.author")
                ->select(
                    'mShipData.name as name',
                    'mShipData.duration as duration',
                    'mShipCache.start_date as start_date',
                    'mShipCache.end_date as end_date',
                    'mShipType.type as type',
                    'PK.visitlog as visit'
                )
                ->where('mShipCache.author', '=', $id)
                ->get();

            if(count($data) <= 0){
                $data = MemberModel::from("memberdata as PK")
                    ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                    ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                    ->select(
                        'mShipData.name as name',
                        'mShipData.duration as duration',
                        'PK.m_startdate as start_date',
                        'PK.m_enddate as end_date',
                        'mShipType.type as type',
                        'PK.visitlog as visit'
                    )->where('PK.member_id', '=', $id)
                    ->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('type', function ($data) {
                    return '<div class="text-left">'.$data->type.'</div>';
                })
                ->addColumn('duration', function ($data) {
                    return '<div class="text-left">'.$data->duration.' Bulan</div>';
                })
                ->addColumn('start_date', function ($data) {
                    if(isset($data->start_date)){
                        if($data->memberStatus != 2){
                            return '<div class="text-left">'.date("d M Y",strtotime($data->start_date)).'</div>';
                        }else{
                            return '<div class="text-left"> - </div>';
                        }
                    }else{
                        return '<div class="text-left"> - </div>';
                    }
                })
                ->addColumn('expired_date', function ($data) {
                    if(isset($data->end_date)){
                        if($data->memberStatus != 2){
                            $nextMonth = date("d/m/Y",strtotime($data->start_date."+".$data->duration ." month"));
//                      $endDate = date('d/m/Y',strtotime($data->start_date."+".$data->duration ." month"));

                            $endDate = date("d M Y", strtotime($data->end_date));
                        }else{
                            $endDate = " - ";
                        }

                        return '<div class="text-left">'.$endDate.


//                        if($data->start_date==$nextMonth-1){
//                            echo $endDate = date('Y-m-d',strtotime($data->start_date."+".$data->duration ." month"));
//                        }else{
//                            echo $endDate = date('Y-m-d', strtotime("last day of next month",strtotime($data->start_date)));
//                        }

                            '</div>';
                    }else{
                        return '<div class="text-left"> - </div>';
                    }

                })
                ->addColumn('visit', function ($data) {
                    return '<div class="text-left">'.$data->visit.'</div>';
                })
                ->addColumn('action', function ($data) {
                    return '<button class="btn btn-default w-auto" title="Perpanjang Membership">
                                <span class="fas fa-plus fa-xs mr-1"></span> Perpanjang
                            </button>';
                })
                ->rawColumns(['name', 'type', 'duration', 'start_date', 'expired_date', 'visit', 'action'])
                ->make(true);
        }
    }

    public function getMemberPT(Request $request, $id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if($request->ajax() && $id != ""){
            $data = MemberModel::from("memberdata as PK")
                ->join("cache_read as cData", "cData.author", "=", "PK.member_id")
                ->leftjoin("ptdata as PTData", "PTData.pt_id", "=", "cData.id_pt")
                ->select(
                    'PK.member_id',
                    'PTData.name as name',
                    'PTData.gender as gender',
                    'PK.session_reg as jsession',
                    'PK.session as session_left'
                )
                ->where('PK.member_id', $id)
                ->where('PK.session_reg', "!=", null)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    if($data->name == ""){
                        return '<span class="text-left"> - </span>';
                    }else{
                        return '<span class="text-left">'.$data->name.'</span>';
                    }
                })
                ->addColumn('gender', function ($data) {
                    if($data->name == ""){
                        return '<div class="text-left"> - </div>';
                    }else{
                        return '<div class="text-left">'.$data->gender.'</div>';
                    }
                })
                ->addColumn('jsession', function ($data) {
                    return '<div class="text-left">'.$data->jsession.' Sesi</div>';
                })
                ->addColumn('session_left', function ($data) {
                    return '<div class="text-left">'.$data->session_left.' Sesi</div>';
                })
                ->rawColumns(['name', 'gender', 'jsession', 'session_left'])
                ->make(true);
        }
    }

    public function getMemberLog(Request $request, $id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");

        if($request->ajax()){
            $data = MemberLogModel::from("logmember as PK")
                ->join("log_category as logCat", "logCat.id", "=", "PK.category")
                ->select(
                    'PK.log_id as log_id',
                    'PK.date as date',
                    'PK.desc as desc',
                    'logCat.category as category',
                    'PK.transaction as transaction',
                    'PK.author as member_id',
                    'PK.status as status',
                    'PK.aksi'
                )
                ->orderBy('PK.date', 'DESC')
                ->where('PK.author', '=', $id)->get();

            $data2 = MemberCheckinModel::where('author', '=', $id)->get();

            $list = collect([]);

            foreach($data as $d1) {
                $list = $list->push($d1);
            }
            foreach($data2 as $d2) {
                $list = $list->push($d2);
            }

            return DataTables::of($list)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return '<span class="text-left">'.date('d M Y - H:i', strtotime($data->date)).'</span>';
                })
                ->addColumn('desc', function ($data) {
                    if($data->desc == null){
                        return '<div class="text-left">Member Check-in</div>';
                    }else{
                        return '<div class="text-left">'.$data->desc.'</div>';
                    }
                })
                ->addColumn('category', function ($data) {
                    if($data->category == null){
                        return '<div class="text-left">Aktivitas</div>';
                    }else{
                        return '<div class="text-left">'.$data->category.'</div>';
                    }
                })
                ->addColumn('status', function ($data) {
                    return '<div class="text-left">'.$data->status.'</div>';
                })
                ->addColumn('transaction', function ($data) {
                    if($data->transaction == null){
                        return null;
                    }else{
                        return '<div class="text-left">'.$this->asRupiah($data->transaction).'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    if($data->aksi == "register"){
                        return '<center>
                                    <a href="'.route('member.printRegister', $data->member_id).'" class="btn btn-default btn-sm" title="Cetak Invoice Registrasi">
                                        <i class="fas fa-print text-info"></i>
                                    </a>
                                </center>';
                    }else if($data->aksi == "sesi"){
                        return '<center>
                                    <a href="'.route('member.printPembelianSesi', $data->log_id).'" class="btn btn-default btn-sm" title="Cetak Invoice Pembelian Sesi">
                                        <i class="fas fa-print text-info"></i>
                                    </a>
                                </center>';
                    }else if($data->aksi == "membership"){
                        return '<center>
                                    <a href="'.route('member.printPembelianSesi', $data->log_id).'" class="btn btn-default btn-sm" title="Cetak Invoice Pembelian Paket Member">
                                        <i class="fas fa-print text-info"></i>
                                    </a>
                                </center>';
                    }
                })
                ->rawColumns(['date', 'desc', 'category', 'status', 'transaction', 'action'])
                ->make(true);
        }
    }

    public function view($id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if(isset($role)){
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            $data['data'] = MemberModel::where('member_id', $id)->first();
            $data['cache'] = MemberCacheModel::where('author', $id)->first();
            $data['membership'] = MembershipModel::where('mship_id', $data['data']->membership)->first();

            $data['data']->dob = Carbon::parse($data['data']->dob)->format("d M Y");

            $data['membership_cache'] = membershipListCacheModel::where('author', $id)->orderBy('start_date', 'ASC')->first();

            if(isset($data['membership_cache']->start_date)){
                $data['membership_startdate'] = date("d M y",strtotime($data['membership_cache']->start_date));
            }

            if(isset($data['membership_cache']->end_date)){
                $data['membership_enddate'] = date("d M y",strtotime($data['membership_cache']->end_date));
            }


            $data['cicilan_member'] = CicilanDataModel::where('author', $id)->get();

            if(isset($data['cache']->id_pt)){
                $data['pt'] = PersonalTrainerModel::where('pt_id', $data['cache']->id_pt)->first();
            }

            $data['marketing'] = MarketingModel::where('mark_id', $data['cache']->id_marketing)->first();

            $data['marketingList'] = MarketingModel::get();
            $data['ptList'] = PersonalTrainerModel::get();

            $data['title'] = 'Lihat Data Member';
            $data['role'] = $this->checkAuth();
            $data['username'] = Auth::user()->name;
            $data['app_layout'] = $this->defineLayout($role);

            $data['logCategory'] = MemberLogCategoryModel::select('category')->get();

            $data['last_edited'] = DB::table("public.memberdata as MEMBER")
                ->leftJoin("secure.users as CS", "CS.id", "=", "MEMBER.updated_by")
                ->select(
                    'CS.name as name'
                )
                ->where('member_id', "=", $id)
                ->first();

            if($data['last_edited'] == null){
                $data['last_edited'] = DB::table("public.memberdata as MEMBER")
                    ->leftJoin("secure.users as CS", "CS.id", "=", "MEMBER.created_by")
                    ->select(
                        'CS.name as name'
                    )
                    ->where('member_id', "=", $id)
                    ->first();
            }

            $data['filter_year_available'] = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->get();

            $totalQuery = count($data['filter_year_available']);
            $arrayValidate = [];

            for($i=0; $i<$totalQuery; $i++) {
                if (in_array($data['filter_year_available'][$i]->date, $arrayValidate)) {
                    $data['filter_year_available']->forget($i);
                } else {
                    array_push($arrayValidate, $data['filter_year_available'][$i]->date);
                }
            }

            if($data['data'] != null){
                return view('member.management.view', $data);
            }else{
                dd('data tidak ditemukan');
            }
        }
    }

    public function edit($id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if(isset($role)){
            $title = 'Edit Data Member';
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            $data['data'] = MemberModel::where('member_id', $id)->first();
            $data['cache'] = MemberCacheModel::where('author', $id)->first();
            $data['logCategory'] = MemberLogCategoryModel::select('category')->get();

            if($data['data']){
                $data['membership'] = MembershipModel::where('mship_id', $data['data']->membership)->first();
            }else{
                abort(404);
            }

            $data['membership_cache'] = membershipListCacheModel::from("membership_memberlist as PK")
                ->join("membership as mShip", "mShip.mship_id", "=", "PK.membership_id")
                ->select(
                    "PK.id as id",
                    "PK.membership_id as membership_id",
                    "PK.start_date as start_date",
                    "PK.end_date as end_date",
                    "PK.author as author",
                    "mShip.name as membership")
                ->where('author', $id)->orderBy('start_date', 'ASC')->first();

            if(isset($data['membership_cache']->start_date)){
                $data['membership_startdate'] = date("d M y",strtotime($data['membership_cache']->start_date));
            }

            if(isset($data['membership_cache']->end_date)){
                $data['membership_enddate'] = date("d M y",strtotime($data['membership_cache']->end_date));
            }

            $data['cicilan_member'] = CicilanDataModel::where('author', $id)->get();

            if(isset($data['cache']->id_pt)){
                $data['pt'] = PersonalTrainerModel::where('pt_id', $data['cache']->id_pt)->first();
            }

            $data['marketing'] = MarketingModel::where('mark_id', $data['cache']->id_marketing)->first();


            $data['marketingList'] = MarketingModel::where('status', 1)->get();
            $data['ptList'] = PersonalTrainerModel::where('status', 1)->get();

            $data['title'] = 'Ubah Data Member';
            $data['role'] = $this->checkAuth();
            $data['username'] = Auth::user()->name;
            $data['app_layout'] = $this->defineLayout($role);

            $data['membership_action'] =
                $this->isMembershipActive(
                    $data['data']->status, $data['data']->m_enddate, $id, $data['membership']->duration
                );

            $data['pt_action'] = $this->isSessionAvailable($data['data']->session);
            $data['session'] = SessionModel::where('status', 1)->get();
            $data['payment'] = PaymentModel::latest()->orderBy('id')->get();
            $data['membership_data'] = MembershipModel::from('membership as PK')
                ->leftjoin('membership_type as mType', 'mType.mtype_id','=','PK.type')->select(
                'PK.mship_id as mship_id',
                'PK.name as name',
                'PK.duration as duration',
                'PK.price as price',
                'PK.status as status',
                'PK.category as category',
                'mType.type as type'
            )->where('status', 1)->get();

            $data['debitType'] = BankModel::latest()->where('model', 2)->get();
            $data['creditType'] = BankModel::latest()->where('model', 3)->get();
            $data['reg_no'] = MemberLogModel::where('category', 5)->count();

            $data['duration_left'] = Carbon::parse($data['data']->m_enddate)->diffInDays(Carbon::today());

            $data['filter_year_available'] = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->get();

            $totalQuery = count($data['filter_year_available']);
            $arrayValidate = [];

            for($i=0; $i<$totalQuery; $i++) {
                if (in_array($data['filter_year_available'][$i]->date, $arrayValidate)) {
                    $data['filter_year_available']->forget($i);
                } else {
                    array_push($arrayValidate, $data['filter_year_available'][$i]->date);
                }
            }

            if($data['data'] != null){
                return view('member.management.edit', $data);
            }else{
                abort(404);
            }
        }
    }

    public function update(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        if(Auth::user()->role_id == 1){
            if($r->dataMarketing == "nothing"){
                $marketingName = null;
                $marketingID = null;
            }else{
                $marketingName = $r->cacheMarketing;
                $marketingID = $r->dataMarketing;
            }
        }

        if($r->dataPT == "nothing"){
            $ptName = null;
            $ptID = null;
        }else{
            $ptName = $r->cachePT;
            $ptID = $r->dataPT;
        }

        if(Auth::user()->role_id == 1){
            $data = MemberModel::where('member_id', $r->hiddenID)->update([
                'name' => $r->dataNama,
                'gender' => $r->dataGender,
                'job' => $r->dataJob,
                'company' => $r->dataCompany,
                'phone' => $r->dataPhone,
                'email' => $r->dataEmail,
                'dob' => $r->dataDOB,
                'photo' => $r->photoFile,
                'marketing' => $marketingName,
                'pt' => $ptName,
                'member_notes' => $r->dataUserNote,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            $cache = MemberCacheModel::where('author', $r->hiddenID)->update([
                'id_pt' => $ptID,
                'id_marketing' => $marketingID
            ]);
        }else{
            $data = MemberModel::where('member_id', $r->hiddenID)->update([
                'photo' => $r->photoFile,
                'member_notes' => $r->dataUserNote,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);
        }

        if(Auth::user()->role_id== 1){
            return redirect()->route('suadmin.member.index')->with(['success' => 'Data Member Berhasil Diubah']);
        }else if(Auth::user()->role_id == 2){
            //STILL EMPTY
        }else if(Auth::user()->role_id == 3){
            return redirect()->route('cs.member.index')->with(['success' => 'Data Member Berhasil Diubah']);
        }
    }

    function isMembershipActive($status, $last_longer, $id, $duration){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        //$date_now->diffInDays(Carbon::parse($last_longer)) <= 31
        switch ($status){
            case 1:
                return
                    '<button type="button" class="btn btn-dark mt-0 mb-2 ml-1 w-100" data-dismiss="modal" onclick="ubahPaket();">
                        <i class="fas fa-plus mr-1 fa-sm"></i> Ganti Paket
                    </button>
                    <button type="button" class="btn btn-dark mt-0 ml-1 w-100" data-dismiss="modal" onclick="upgradePaket();">
                        <i class="fas fa-upload mr-1 fa-sm"></i> Upgrade
                    </button>';
                break;
            case 2:
                return
                    '<button type="button" class="btn btn-dark mt-0 mb-2 ml-1 w-100" onclick="activatePaket(`'.$id.'`,'.$duration.');">
                        <i class="fas fa-check mr-1 fa-sm"></i>  Aktivasi Member
                    </button>';
                break;
            case 3:
                return null;
                break;
            case 4:
                return
                    '<button type="button" class="btn btn-dark mt-0 mb-2 ml-1 w-100" data-dismiss="modal" onclick="extendPaket();">
                        <i class="fas fa-sync mr-1 fa-sm"></i>  Renewal Paket
                     </button>
                     <button type="button" class="btn btn-outline-dark mt-0 ml-1 w-100" data-dismiss="modal" onclick="ubahPaket();">
                        <i class="fas fa-edit mr-1 fa-sm"></i> Ganti / Upgrade Paket
                     </button>';
                break;
        }
    }

    function isSessionAvailable($session){
        if($session > 0){
            return '<button type="button" class="btn btn-dark mb-2 w-100" onclick="tambahSesi();">
                        <i class="fas fa-plus mr-1 fa-sm"></i> Tambah Sesi
                    </button>
                    <button type="button" class="btn btn-outline-dark mt-0 w-100" onclick="ubahPT();">
                        <i class="fas fa-edit mr-1 fa-sm"></i> Ubah PT
                    </button>';
        }else{
            return '<button type="button" class="btn btn-dark w-100" onclick="registerPT();">
                        <i class="fas fa-plus mr-1 fa-sm"></i> Daftar Paket PT
                    </button>';
        }
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }

    public function aktivasi(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();
        $date_end = Carbon::now()->addMonths($r->duration)->toDateString();

        $dataGET = MemberModel::select('membership')->where('member_id', $r->id)->first();
        $payment = MemberLogModel::select('transaction')->where('author', $r->id)->first();

        $data = MemberModel::where('member_id', $r->id)->update([
            'status' => 1,
            'm_startdate' => $date_now,
            'm_enddate' => $date_end,
        ]);

        $membershiplist = membershipListCacheModel::create([
            'membership_id' => $dataGET->membership,
            'start_date' => $date_now,
            'end_date' => $date_end,
            'author' => $r->id,
            'payment' => $payment->transaction
        ]);

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.index')->with(['success' => 'Member '.$r->id.' Berhasil Di Aktivasi.']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.index')->with(['success' => 'Member '.$r->id.' Berhasil Di Aktivasi.']);
        }
    }

    public function print($id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('d M Y');

        $data['title'] = 'Invoice Registrasi';
        $data['memberID'] = $id;
        $data['time'] = $date_now;
        $data['desc_title'] = 'Registrasi dan Pembelian Paket ';
        $data['pt_title'] = 'Personal Trainer ';
        $data['data'] = MemberModel::from("memberdata as PK")
            ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
            ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
            ->select(
                'PK.member_id',
                'PK.name as memberName',
                'PK.session_reg',
                'PK.pt as PKPT',
                'mShipData.name as membership',
                'mShipData.price as membershipPrice',
                'mShipType.type as type')
            ->where("PK.member_id", $id)
            ->first();

        $data['log'] = MemberLogModel::from("logmember as PK")
            ->select(
                'PK.author',
                'PK.date as LOG_DATE',
                'PK.reg_no as PO_ID',
                'PK.additional',
                'PK.notes'
            )
            ->where("PK.author", $id)
            ->first();

        $data['systemDay'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 1)->first();

        $data['systemMonth'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 2)->first();

        $data['systemYear'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 3)->first();

        $bulanToRomawi = $this->getRomawi((date("m",strtotime($data['log']->LOG_DATE))));
        $idInvoice = $this->getNomorSurat($data['log']->PO_ID);


        $data['PINO'] = $idInvoice."/PI/FF/".$bulanToRomawi."/".(date("Y",strtotime($data['log']->LOG_DATE)));

        $data['session'] = MemberCacheModel::from("cache_read")
            ->where('author', $id)->first();

//        $data['session'] = SessionModel::from("sessiondata as PK")
//            ->select(
//                'PK.price as sessionPrice'
//            )->where('PK.duration', $data['data']->session_reg)
//            ->first();

        $data['ppn'] = 10;
        $data['namaCS'] = 0;
        $data['jumlahSesi'] = 0;
        $data['metodeBayar'] = "Cash";
        $data['namaBank'] = "";

        if($data['session']->session_price != null){
            $data['data']->membershipPrice = $data['log']->transaction - $data['session']->session_price;
        }else{
            $data['data']->membershipPrice = $data['log']->transaction;
        }

        if($data['log']->additional == "Cash"){
            $data['metodeBayar'] = "Cash";
            $data['namaBank'] = " - ";
        }else if($data['log']->additional == "Visa" || $data['log']->additional == "Master Card"){
            $data['metodeBayar'] = "Credit Card (".$data['log']->additional.")" ;
            $data['namaBank'] = " - ";
        }else{
            $data['metodeBayar'] = "Debit Card";
            $data['namaBank'] = $data['log']->additional;
        }

        if($data['session']->session_title != null){
            $data['pt_title'] = "Personal Trainer - ".$data['session']->session_title." (".$data['data']->session_reg." Sessions)";
        }else{
            $data['pt_title'] = "Personal Trainer (".$data['data']->session_reg." Sessions)" ;
        }

//        if($data['data']->session_reg == 99) {
//            $data['pt_title'] = "Personal Trainer - Small Group (10 Sessions)";
//        }else if($data['data']->session_title != null){
//            $data['pt_title'] = "Personal Trainer (".$data['data']->session_title." - ".$data['data']->session_reg." Sessions)";
//        }else{
//            $data['pt_title'] = "Personal Trainer (".$data['data']->session_reg." Sessions)" ;
//        }

        $pdf = PDF::loadView('member.print.register_invoice', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        $DOC_NAME = "INVOICE_REG_".$id;

        return $pdf->stream($DOC_NAME.".pdf");
    }

    function printRegister($id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('d M Y');

        $data['title'] = 'Invoice Registrasi';
        $data['memberID'] = $id;
        $data['time'] = $date_now;
        $data['desc_title'] = 'Registrasi dan Pembelian Paket ';
        $data['pt_title'] = 'Personal Trainer ';
        $data['data'] = MemberModel::from("memberdata as PK")
            ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
            ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
            ->select(
                'PK.id',
                'PK.member_id',
                'PK.name as memberName',
                'PK.session_reg',
                'PK.pt as PKPT',
                'mShipData.name as membership',
                'mShipData.price as membershipPrice',
                'mShipType.type as type')
            ->where("PK.member_id", $id)
            ->first();

        $data['log'] = MemberLogModel::from("logmember as PK")
            ->select(
                'PK.author',
                'PK.date as LOG_DATE',
                'PK.reg_no as PO_ID',
                'PK.transaction',
                'PK.additional',
                'PK.t_membership',
                'PK.t_sesi',
                'PK.notes'
            )
            ->where("PK.author", $id)
            ->first();

        $data['systemDay'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 1)->first();

        $data['systemMonth'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 2)->first();

        $data['systemYear'] = SysModel::from("sysdata as PK")
            ->select(
                'PK.sysdata',
                'PK.value'
            )->where("PK.sysid", 3)->first();

        $bulanToRomawi = $this->getRomawi((date("m",strtotime($data['log']->LOG_DATE))));
        $idInvoice = $this->getNomorSurat($data['data']->id);

        $data['PINO'] = $idInvoice."/PI/".$bulanToRomawi."/".(date("Y",strtotime($data['log']->LOG_DATE)));

        $data['session'] = MemberCacheModel::from("cache_read")
            ->where('author', $id)->first();
        $data['ppn'] = 10;
        $data['namaCS'] = 0;
        $data['jumlahSesi'] = 0;
        $data['metodeBayar'] = "Cash";
        $data['namaBank'] = "";

        if($data['session']->session_price != null){
            $data['data']->membershipPrice = $data['log']->transaction - $data['session']->session_price;
        }else{
            $data['data']->membershipPrice = $data['log']->transaction;
        }

        if($data['log']->additional == "Cash"){
            $data['metodeBayar'] = "Cash";
            $data['namaBank'] = " - ";
        }else if($data['log']->additional == "Visa" || $data['log']->additional == "Master Card"){
            $data['metodeBayar'] = "Credit Card (".$data['log']->additional.")" ;
            $data['namaBank'] = " - ";
        }else{
            $data['metodeBayar'] = "Debit Card";
            $data['namaBank'] = $data['log']->additional;
        }

        if($data['session']->session_title != null){
            $data['pt_title'] = "Personal Trainer - ".$data['session']->session_title." (".$data['data']->session_reg." Sessions)";
        }else{
            $data['pt_title'] = "Personal Trainer (".$data['data']->session_reg." Sessions)" ;
        }

        $pdf = PDF::loadView('member.print.register_invoice', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        $DOC_NAME = "INVOICE_REG_".$id;

        return $pdf->stream($DOC_NAME.".pdf");
    }

    function getRomawi($bln){
        switch ($bln){
            case 1:
                return "I";
                break;
            case 2:
                return "II";
                break;
            case 3:
                return "III";
                break;
            case 4:
                return "IV";
                break;
            case 5:
                return "V";
                break;
            case 6:
                return "VI";
                break;
            case 7:
                return "VII";
                break;
            case 8:
                return "VIII";
                break;
            case 9:
                return "IX";
                break;
            case 10:
                return "X";
                break;
            case 11:
                return "XI";
                break;
            case 12:
                return "XII";
                break;
        }
    }

    function getNomorSurat($no){
        if($no < 1000 && $no > 100){
            return "0".$no;
        }else if($no < 100 && $no > 10){
            return "00".$no;
        }else if($no < 10){
            return "000".$no;
        }else{
            return $no;
        }
    }

    function dataChecking(){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now()->toDateString();

        $data['data'] = MemberModel::select('member_id','m_enddate','status')->where('status', '=', 1)->whereDate('m_enddate', '<', $date_now)->update(array('status' => 4));
        $member = CutiMemberModel::from("cutidata as PK")
                    ->join('memberdata as mData','mData.member_id','=','PK.member_id')
                    ->join('membership as mShipData','mShipData.mship_id','=','mData.membership')
                    ->select(
                        'mData.member_id as mid',
                        'mData.m_startdate as start',
                        'mShipData.duration as duration',
                        'PK.id as id',
                        'PK.cuti_expired as cuti_expired'
                    )->whereDate('cuti_expired', '<', $date_now)->get();

        if(count($member) > 0){
            $member_list = [];
            $new_date_list = [];
            foreach ($member as $m) {
                $m->start = Carbon::parse($m->start)->addMonths($m->duration)->format('Y-m-d');
                array_push($member_list, $m->mid);
                array_push($new_date_list, $m->start);
            }

            foreach($new_date_list as $date){
                $exec_date = MemberModel::select('member_id','m_enddate')
                    ->whereIn('member_id', $member_list)
                    ->update(array('m_enddate' => $date));
            }

            $exec_member = MemberModel::select('member_id','status')
                ->whereIn('member_id', $member_list)
                ->update(array('status' => 1));

            $exec_cuti = CutiMemberModel::select('member_id')
                ->whereIn('member_id', $member_list)
                ->delete();
        }

        if(Auth::user()->role_id == 1){
                return redirect()->route('suadmin.index');
            }elseif(Auth::user()->role_id == 2){
//              //
        }elseif(Auth::user()->role_id == 3){
                return redirect()->route('cs.index');
            }
    }

    function addTransaction(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        $successMessage = "";

        if($r->sTransaction == "extend-session") {
            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                'session_reg' => ($r->lOld + $r->nSession),
                'session' => ($r->sOld + $r->nSession),
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            if ($r->nTitle == "") {
                $fullSessionName = $r->nSession . " Session";
            } else {
                $fullSessionName = $r->nTitle . " - " . $r->nSession . " Session";
            }

            if($r->paymentMethodGroup == "cicilan") {
                $status_transaksi = "Dalam cicilan";

                if ($r->mShipApproval == "") {
                    $jumlah_transaksi = $r->nPrice;
                } else {
                    $jumlah_transaksi = $r->mShipApproval;
                }

                if($r->firstPaySet == "manual"){
                    $rest_data = $r->firstPayData;
                }else{
                    $rest_data = ($jumlah_transaksi / $r->durasiCicilan);
                }

                $rest_pt = 'Pembelian Sesi PT - ' . $fullSessionName;

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => ($r->durasiCicilan - 1),
                    'rest_price' => $jumlah_transaksi,
                    'rest_data' => ($jumlah_transaksi - $rest_data),
                    'rest_membership' => $rest_pt,
                    'created_at' => $date_now
                ]);
            }else if($r->paymentMethodGroup == "tunda"){
                $status_transaksi = "Dalam cicilan";

                if ($r->mShipApproval == "") {
                    $jumlah_transaksi = $r->nPrice;
                } else {
                    $jumlah_transaksi = $r->mShipApproval;
                }

                $rest_data = $jumlah_transaksi;
                $rest_pt = 'Pembelian Sesi PT - ' . $fullSessionName;

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => 1,
                    'rest_price' => $jumlah_transaksi,
                    'rest_data' => $rest_data,
                    'rest_membership' => $rest_pt,
                    'created_at' => $date_now
                ]);
            }else{
                $status_transaksi = "lunas";
                $rest_pt = 'Pembelian Sesi PT - ' . $fullSessionName;

                if($r->mShipApproval == ""){
                    $jumlah_transaksi = $r->nPrice;
                }else{
                    $jumlah_transaksi = $r->mShipApproval;
                }
            }

            if($r->paymentMethodGroup == "cicilan") {
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => $rest_data,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => $rest_data,
                    'notes' => $r->nNotes
                ]);
            }else if($r->paymentMethodGroup == "tunda"){
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => 0,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => 0,
                    'notes' => $r->nNotes
                ]);
            }else{
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => $jumlah_transaksi,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => $jumlah_transaksi,
                    'notes' => $r->nNotes
                ]);
            }

            $successMessage = 'Pembelian Sesi Berhasil!';

        }else if($r->sTransaction == "register-session"){
            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                'session_reg' => $r->nSession,
                'session' => $r->nSession,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            if($r->nPT == "nothing" || $r->nPT == ""){
                $namaRegPT = null;
            }else{
                $namaRegPT = $r->nPT;
            }

            $data2 = MemberCacheModel::where('author', $r->sHiddenID)->update([
                'id_pt' => $namaRegPT,
                'session_price' => $r->nPrice
            ]);

            if ($r->nTitle == "") {
                $fullSessionName = $r->nSession . " Session";
            } else {
                $fullSessionName = $r->nTitle . " - " . $r->nSession . " Session";
            }

            if($r->paymentMethodGroup == "cicilan") {
                $status_transaksi = "Dalam cicilan";

                if ($r->mShipApproval == "") {
                    $jumlah_transaksi = $r->nPrice;
                } else {
                    $jumlah_transaksi = $r->mShipApproval;
                }

                if($r->firstPaySet == "manual"){
                    $rest_data = $r->firstPayData;
                }else{
                    $rest_data = ($jumlah_transaksi / $r->durasiCicilan);
                }

                $rest_pt = 'Pembelian Paket Personal Trainer - ' . $fullSessionName;

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => ($r->durasiCicilan - 1),
                    'rest_price' => $jumlah_transaksi,
                    'rest_data' => ($jumlah_transaksi - $rest_data),
                    'rest_membership' => $rest_pt,
                    'created_at' => $date_now
                ]);
            }else if($r->paymentMethodGroup == "tunda"){
                $status_transaksi = "Dalam cicilan";

                if ($r->mShipApproval == "") {
                    $jumlah_transaksi = $r->nPrice;
                } else {
                    $jumlah_transaksi = $r->mShipApproval;
                }

                $rest_data = $jumlah_transaksi;
                $rest_pt = 'Pembelian Paket Personal Trainer - ' . $fullSessionName;

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => 1,
                    'rest_price' => $jumlah_transaksi,
                    'rest_data' => $jumlah_transaksi,
                    'rest_membership' => $rest_pt,
                    'created_at' => $date_now
                ]);
            }else{
                $status_transaksi = "lunas";
                $rest_pt = 'Pembelian Paket Personal Trainer - ' . $fullSessionName;

                if($r->mShipApproval == ""){
                    $jumlah_transaksi = $r->nPrice;
                }else{
                    $jumlah_transaksi = $r->mShipApproval;
                }
            }

            if($r->paymentMethodGroup == "cicilan") {
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => $rest_data,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => $rest_data,
                    'notes' => $r->nNotes
                ]);
            }else if($r->paymentMethodGroup == "tunda"){
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => 0,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => 0,
                    'notes' => $r->nNotes
                ]);
            }else{
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $rest_pt,
                    'category' => 5,
                    'transaction' => $jumlah_transaksi,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'sesi',
                    't_sesi' => $jumlah_transaksi,
                    'notes' => $r->nNotes
                ]);
            }

            $successMessage = "Pembelian Paket Personal Trainer Berhasil!";

        }else if($r->sTransaction == "change-membership" || $r->sTransaction == "extend-membership"){
            $member['member'] = MemberModel::where('member_id', $r->sHiddenID)->first(); 

            if($r->paymentMethodGroup == "cicilan") {
                $status_transaksi = "Dalam Cicilan";
                $jumlah_transaksi = (int)$r->jumlahCicilan;

                if($r->firstPaySet == "manual"){
                    $rest_data = ($r->jumlahCicilan * $r->durasiCicilan) - $r->firstPayData;
                }else{
                    $rest_data = ($r->jumlahCicilan * $r->durasiCicilan) - $r->jumlahCicilan;
                }


                $rest_price = $r->jumlahCicilan * $r->durasiCicilan;
                $rest_membership = "Pembelian Paket Member";

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => $r->durasiCicilan,
                    'rest_price' => $rest_price,
                    'rest_data' => $rest_data,
                    'rest_membership' => $rest_membership,
                    'created_at' => $date_now
                ]);
            }else if($r->paymentMethodGroup == "tunda"){
                $status_transaksi = "Dalam Cicilan";
                $jumlah_transaksi = (int)$r->jumlahCicilan;

                $rest_price = $r->jumlahCicilan * $r->durasiCicilan;
                $rest_data = $r->jumlahCicilan * $r->durasiCicilan;
                $rest_membership = "Pembelian Paket Member";

                $cicilanData = CicilanDataModel::create([
                    'author' => $r->sHiddenID,
                    'rest_duration' => $r->durasiCicilan,
                    'rest_price' => $rest_price,
                    'rest_data' => $rest_data,
                    'rest_membership' => $rest_membership,
                    'created_at' => $date_now
                ]);
            }else{
                $status_transaksi = "lunas";
                if($r->mShipApproval == ""){
                    $jumlah_transaksi = (int)$r->mShipPrice;
                }else{
                    $jumlah_transaksi = (int)$r->mShipApproval;
                }
            }

            if($r->upgradeRecord == ""){
                $log_desc = 'Pembelian Paket Member - '.$r->mShipName;
                $successMessage = 'Pembelian Paket Member Berhasil!';
            }else{
                $log_desc = 'Upgrade Paket Member - '.$r->mShipName;
                $successMessage = 'Upgrade Paket Member Berhasil!';
            }

            if($r->mShipApproval == null || $r->mShipApproval == ""){
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $log_desc,
                    'category' => 5,
                    'transaction' => $jumlah_transaksi,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'membership',
                    't_membership' => $jumlah_transaksi,
                    'notes' => $r->nNotes
                ]);
            }else{
                $log = MemberLogModel::create([
                    'date' => $date_now,
                    'desc' => $log_desc,
                    'category' => 5,
                    'transaction' => $jumlah_transaksi,
                    'status' => $status_transaksi,
                    'author' => $r->sHiddenID,
                    'additional' => $r->nPayment,
                    'reg_no' => ($r->nRegNo + 1),
                    'aksi' => 'membership',
                    't_membership' => $jumlah_transaksi,
                    'notes' => $r->nNotes
                ]);
            }

            if($r->sTransaction == "change-membership"){

                if($member['member']->status == 4){
                    //IF MEMBER EXPIRED, THEN CHANGE MEMBERSHIP END DATE CHANGE TO...
                    $new_enddate = Carbon::now()->addMonths($r->mShipDuration)->toDateString();

                    $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                        'status' => 1,
                        'membership' => $r->mShipID,
                        'm_startdate' => $date_now,
                        'm_enddate' => $new_enddate,
                        'updated_at' => $date_now,
                        'updated_by' => Auth::user()->id
                    ]);

                    $memberhipListCache = membershipListCacheModel::create([
                        'author' => $r->sHiddenID,
                        'membership_id' => $r->mShipID,
                        'start_date' => $date_now,
                        'end_date' => $new_enddate,
                        'payment' => $log->transaction
                    ]);
                }else{
                    if($r->upgradeRecord == ""){
                        $new_enddate = Carbon::parse($member['member']->m_enddate)->addMonths($r->mShipDuration)->toDateString();
                        $cacheStartDate = Carbon::parse($member['member']->m_enddate)->addDays(1)->toDate();

                        $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                            'membership' => $r->mShipID,
                            'm_enddate' => $new_enddate,
                            'updated_at' => $date_now,
                            'updated_by' => Auth::user()->id
                        ]);

                        $memberhipListCache = membershipListCacheModel::create([
                            'author' => $r->sHiddenID,
                            'membership_id' => $r->mShipID,
                            'start_date' => $cacheStartDate,
                            'end_date' => $new_enddate,
                            'payment' => $log->transaction
                        ]);
                    }else{
                        $member['membershipCacheList'] = membershipListCacheModel::where('author', $r->sHiddenID)->orderBy('start_date', 'ASC')->first();
                        $member['membershipData'] = MembershipModel::where('mship_id', $member['membershipCacheList']->membership_id)->first();

                        $upgrade_start = Carbon::parse($member['membershipCacheList']->start_date);
                        $upgrade_end = Carbon::parse($member['membershipCacheList']->end_date);

                        $upgrade_duration_new_month = Carbon::parse($upgrade_start)->diffInMonths($upgrade_end);

                        $upgrade_price_divider = $member['membershipData']->price / $member['membershipData']->duration;
                        $upgrade_price_final = $upgrade_price_divider * $upgrade_duration_new_month;

//                        echo "Start Date : ".$upgrade_start. " | ";
//                        echo "End Date : ".$upgrade_end. " | ";
//                        echo "Sisa Durasi : ".$upgrade_duration_new_month." Bulan | ";
//                        echo "New End Date : ".$upgrade_end->subMonths($upgrade_duration_new_month)." | ";
//                        echo "Real Price : ".$this->asRupiah($member['membershipData']->price)." | ";
//                        echo "New Price : ".$this->asRupiah($upgrade_price_final)." | ";
//                        echo "Apakah Paket Lebih Kecil atau sama? : ".$hf." | ";
//                        echo "Paket Baru Member : ".$r->mShipName." | ";
//                        echo "Durasi Paket : ".$r->mShipDuration." | ";
//                        echo "Durasi Paket Dimasukkan : ".($r->mShipDuration - $upgrade_duration_new_month)." | ";
//                        echo "Transaksi : ".($r->mShipPrice);

                        if($r->mShipDuration <= $member['membershipData']->duration){
                            $new_enddate = Carbon::parse($member['member']->m_enddate)->addMonths($r->mShipDuration)->toDateString();
                            $cacheStartDate = Carbon::parse($member['member']->m_enddate)->addDays(1)->toDate();

                            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                                'membership' => $r->mShipID,
                                'm_enddate' => $new_enddate,
                                'updated_at' => $date_now,
                                'updated_by' => Auth::user()->id
                            ]);

                            $memberhipListCache = membershipListCacheModel::create([
                                'author' => $r->sHiddenID,
                                'membership_id' => $r->mShipID,
                                'start_date' => $cacheStartDate,
                                'end_date' => $new_enddate,
                                'payment' => $log->transaction
                            ]);

                        }else{
                            $new_enddate = Carbon::parse($member['member']->m_enddate)->addMonths($r->mShipDuration)->toDateString();

                            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                                'membership' => $r->mShipID,
                                'm_enddate' => $new_enddate,
                                'updated_at' => $date_now,
                                'updated_by' => Auth::user()->id
                            ]);

                            $membershipListCache = membershipListCacheModel::where('author', $r->sHiddenID)->orderBy('start_date', 'ASC')->get();

                            for($i=0; $i < count($membershipListCache); $i++){
                                if($i <= 0){
                                    $MLIST_UPDATE = membershipListCacheModel::where('id', $membershipListCache[$i]->id)->update([
                                        'end_date' => Carbon::parse($membershipListCache[$i]->end_date)->addMonths(($r->mShipDuration - $upgrade_duration_new_month)),
                                        'membership_id' => $r->mShipID,
                                        'payment' => (int)$r->mShipPrice
                                        ]);
                                }else{
                                    $MLIST_UPDATE = membershipListCacheModel::where('id', $membershipListCache[$i]->id)->update([
                                        'start_date' => Carbon::parse($membershipListCache[$i]->start_date)->addMonths(($r->mShipDuration - $upgrade_duration_new_month)),
                                        'end_date' => Carbon::parse($membershipListCache[$i]->end_date)->addMonths(($r->mShipDuration - $upgrade_duration_new_month)),
                                        'membership_id' => $r->mShipID,
                                        'payment' => (int)$r->mShipPrice
                                    ]);
                                }
                            }
                        }
                    }
                }
            }else if($r->sTransaction == "extend-membership"){
                $new_enddate = Carbon::now()->addMonths($r->mShipDuration)->toDateString();

                $log_desc = 'Perpanjangan Paket Member - '.$r->mShipName;
                $successMessage = 'Perpanjangan Paket Member Berhasil!';

                $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                    'status' => 1,
                    'membership' => $r->mShipID,
                    'm_startdate' => $date_now,
                    'm_enddate' => $new_enddate,
                    'updated_at' => $date_now,
                    'updated_by' => Auth::user()->id
                ]);

                $memberhipListCache = membershipListCacheModel::create([
                    'author' => $r->sHiddenID,
                    'membership_id' => $r->mShipID,
                    'start_date' => $date_now,
                    'end_date' => $new_enddate,
                    'payment' => $log->transaction
                ]);
            }
        }

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.edit', $r->sHiddenID)->with(['success' => $successMessage]);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.edit', $r->sHiddenID)->with(['success' => $successMessage]);
        }
    }



    function printPembelianSesi($log_id){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('d M Y');

        $data['data'] = MemberLogModel::from("logmember as PK")
                        ->join("memberdata as mData", "mData.member_id", "=", "PK.author")
                        ->where("PK.log_id", $log_id)
                        ->first();

        if($data['data']->aksi == "sesi"){
            //$data['title'] = 'RETENTION SESSION INVOICE';
            $DOC_NAME = "INVOICE_RI_S_".$data['data']->member_id;
        }else if($data['data']->aksi == "membership"){
            //$data['title'] = 'RETENTION MEMBERSHIP INVOICE';
            $DOC_NAME = "INVOICE_RI_M_".$data['data']->member_id;
        }

        $data['title'] = 'PROFORMA INVOICE';

        $data['memberID'] = $data['data']->member_id;
        $data['memberName'] = $data['data']->name;

        $data['desc'] = $data['data']->desc;

        $idInvoice = $this->getNomorSurat($data['data']->reg_no);
        $subject = "RI";

        $data['price'] = $data['data']->transaction;
        $data['disc'] = 0;

        if($data['data']->additional == "Cash"){
            $data['metodeBayar'] = "Cash";
        }else if($data['data']->additional == "Master Card" || $data['data']->additional == "Visa"){
            $data['metodeBayar'] = "Credit Card";
        }else{
            $data['metodeBayar'] = "Debit Card";
        }


        $data['namaBank'] = $data['data']->additional;
        $activeMonth = $this->getRomawi((date("m",strtotime($data['data']->date))));
        $data['time'] = date("d M Y",strtotime($data['data']->date));

        $data['PINO'] = $idInvoice."/".$subject."/".$activeMonth."/".(date("Y",strtotime($date_now)));

        //return $data;

        $pdf = PDF::loadView('member.print.transaction_invoice', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

        return $pdf->download($DOC_NAME.".pdf");
    }

    function changePT(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now()->toDateString();

        if($r->cachePT == "nothing"){
            $activePT = null;
        }else{
            $activePT = $r->cachePT;
        }

        $PT = MemberCacheModel::where('author', $r->ptEditHiddenID)->update([
            'id_pt' => $activePT,
        ]);

        $Member = MemberModel::where('member_id', $r->ptEditHiddenID)->update([
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->id
        ]);

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.edit', $r->ptEditHiddenID)->with(['success' => 'Nama Personal Trainer Berhasil Diubah!']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.edit', $r->ptEditHiddenID)->with(['success' => 'Nama Personal Trainer Berhasil Diubah!']);
        }
    }

    function exportExcelData(){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $namaFile = "Members";

        return Excel::download(new MemberExport(), $namaFile.'.xlsx');
    }

    function exportCheckinExcelData(){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();
        $datenow = Carbon::now()->format('d-m-Y');

        $namaFile = "MemberCheckin-".$datenow;

        return Excel::download(new MemberCheckinExport(), $namaFile.'.xlsx');
    }

    function deleteMember(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $exec = MemberModel::where('member_id', $r->hiddenID)->delete();

        if($this->checkAuth() == 1){
            $enroute = 'suadmin.member.index';
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            $enroute = 'cs.member.index';
        }

        if($exec){
            return redirect()->route($enroute)->with(['success' => 'Member Berhasil Dihapus']);
        }else{
            return redirect()->route($enroute)->with(['error' => 'Member Gagal Dihapus']);
        }
    }

    function forceChangeStatus(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now()->toDateString();

        if($r->dataStatusMember == 2){
            $Member2 = membershipListCacheModel::where('author', $r->memberStatusHiddenID)->orderBy('start_date', 'ASC')->first();
            $M3 = membershipListCacheModel::destroy($Member2->id);

            $Member = MemberModel::where('member_id', $r->memberStatusHiddenID)->update([
                'm_enddate' => $date_now,
                'status' => 4,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);
        }

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.edit', $r->memberStatusHiddenID)->with(['success' => 'Status Member Berhasil Diubah!']);
        }else{
            return redirect()->route('cs.member.edit', $r->memberStatusHiddenID)->with(['success' => 'Status Member Berhasil Diubah!']);
        }
    }

    function HistoryMemberChart(Request $r)
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $query_membership = MemberLogModel::where('author', $r->member_id)
            ->where('aksi', "membership")
            ->orWhere('aksi', 'registrasi');

        $query_pt = MemberLogModel::where('author', $r->member_id)
            ->where('aksi', "membership")
            ->orWhere('aksi', 'registrasi');

        if($r->filterType == "monthly"){
            $transaction_membership = $query_membership->whereMonth('date', '=', $r->filterMonth)->sum('transaction');
            $transaction_pt = $query_pt->whereMonth('date', '=', $r->filterMonth)->sum('transaction');
        }else if($r->filterType == "daily"){

        }else if($r->filterType == "yearly"){

        }

        $data['dataset_membership'] = [];
        $data['dataset_pt'] = [];

        for($i=0; $i< count($transaction_membership); $i++){
            array_push($data['dataset_membership'], $transaction_membership[$i]->transaction);
        }

        for($i=0; $i< count($transaction_pt); $i++){
            array_push($data['dataset_pt'], $transaction_pt[$i]->transaction);
        }

        return $data;
    }

    public function forceChangeStartEndDate(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = date('Y-m-d H:i:s');

        $member = MemberModel::where('member_id', $r->dateHiddenID)->select('m_startdate','m_enddate','status')->first();

        if($member->status == 3){
            return redirect()->route('suadmin.member.edit', $r->dateHiddenID)->with(['failed' => 'Member ini sedang cuti! Tidak dapat mengubah data']);
        }else{
            if(isset($r->memberStartDateHidden)){
                $start_new = Carbon::parse($r->memberStartDateHidden);

                $membership = membershipListCacheModel::whereDate('end_date', '<', $start_new->format("Y-m-d"))->delete();
                $membership = membershipListCacheModel::where('author', $r->dateHiddenID)->orderBy("start_date", "asc")->first()->update([
                    'start_date' => $start_new->format("Y-m-d")
                ]);
            }

            if(isset($r->memberEndDateHidden)){
                $end_new = Carbon::parse($r->memberEndDateHidden);

                $membership = membershipListCacheModel::whereDate('start_date', '>', $end_new->format("Y-m-d"))->delete();
                $membership = membershipListCacheModel::where('author', $r->dateHiddenID)->orderBy("end_date", "desc")->first()->update([
                    'end_date' => $end_new->format("Y-m-d")
                ]);
            }

            if(isset($r->memberStartDateHidden) && isset($r->memberEndDateHidden)){
                $data = MemberModel::where('member_id', $r->dateHiddenID)->update([
                    'm_startdate' => $r->memberStartDateHidden,
                    'm_enddate' => $r->memberEndDateHidden,
                    'updated_at' => $date_now,
                    'updated_by' => Auth::user()->id
                ]);
            }else if(isset($r->memberStartDateHidden)){
                $data = MemberModel::where('member_id', $r->dateHiddenID)->update([
                    'm_startdate' => $r->memberStartDateHidden,
                    'updated_at' => $date_now,
                    'updated_by' => Auth::user()->id
                ]);
            }else if(isset($r->memberEndDateHidden)){
                $data = MemberModel::where('member_id', $r->dateHiddenID)->update([
                    'm_enddate' => $r->memberEndDateHidden,
                    'updated_at' => $date_now,
                    'updated_by' => Auth::user()->id
                ]);
            }

            return redirect()->route('suadmin.member.edit', $r->dateHiddenID)->with(['success' => 'Tanggal Mulai / Berakhir Member Berhasil Diubah!']);
        }
    }
}
