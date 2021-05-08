<?php

namespace App\Http\Controllers\member;

use App\Model\marketing\MarketingModel;
use App\Model\member\CutiMemberModel;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\membership\MembershipModel;
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
use Yajra\DataTables\DataTables;

class MemberDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Data Member';
            $jMember = MemberModel::from('memberdata')->count();
            $memberActive = MemberModel::from('memberdata')->where('status', 1)->count();
            $memberLK = MemberModel::from('memberdata')->where('gender', '=', 'Laki-laki')->count();
            $memberPR = MemberModel::from('memberdata')->where('gender', '=', 'Perempuan')->count();
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            return view('member.index', compact('title','username','role','jMember','memberActive','memberLK','memberPR','app_layout'));
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
        if($request->ajax()){
            $data = MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->join("member_status as mStatus", "mStatus.mstatus_id", "=", "PK.status")
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
                    'mShipType.type as type'
                )
                ->get();

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

                    return '<a href="'.$routeCheckin.'" class="btn btn-danger w-100 mb-2 font-weight-bold">
                                <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                            </a>
                            <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                                <i class="fas fa-address-card fa-sm mr-1"></i> Perpanjang Paket Member
                            </button>
                            <button class="btn btn-outline-secondary w-100" disabled>
                                <i class="fas fa-dumbbell fa-sm mr-1"></i> Perpanjang Sesi Latihan Member
                            </button>
                            <hr>
                            <a href="'.$routeCuti.'" class="btn btn-secondary w-100">
                                <i class="fas fa-calendar-minus fa-sm mr-1"></i> Cutikan Member
                            </a>
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
        if($request->ajax() && $id != ""){
            $data = MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->select(
                    'PK.member_id',
                    'mShipData.name as name',
                    'mShipData.duration as duration',
                    'PK.status as memberStatus',
                    'PK.m_startdate as start_date',
                    'PK.m_enddate as end_date',
                    'mShipType.type as type',
                    'PK.visitlog as visit'
                )
                ->where('PK.member_id', '=', $id)
                ->get();

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
                    if($data->memberStatus != 2){
                        return '<div class="text-left">'.date("d M Y",strtotime($data->start_date)).'</div>';
                    }else{
                        return '<div class="text-left"> - </div>';
                    }
                })
                ->addColumn('expired_date', function ($data) {

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
                ->orderBy('PK.date')
                ->where('PK.author', '=', $id)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return '<span class="text-left">'.date('d/m/Y', strtotime($data->date)).'</span>';
                })
                ->addColumn('desc', function ($data) {
                    return '<div class="text-left">'.$data->desc.'</div>';
                })
                ->addColumn('category', function ($data) {
                    return '<div class="text-left">'.$data->category.'</div>';
                })
                ->addColumn('status', function ($data) {
                    return '<div class="text-left">'.$data->status.'</div>';
                })
                ->addColumn('transaction', function ($data) {
                    return '<div class="text-left">'.$this->asRupiah($data->transaction).'</div>';
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
        $role = $this->checkAuth();

        if(isset($role)){
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            $data['data'] = MemberModel::where('member_id', $id)->first();
            $data['cache'] = MemberCacheModel::where('author', $id)->first();
            $data['membership'] = MembershipModel::where('mship_id', $data['data']->membership)->first();

            $data['pt'] = PersonalTrainerModel::where('pt_id', $data['cache']->id_pt)->first();
            $data['marketing'] = MarketingModel::where('mark_id', $data['cache']->id_marketing)->first();

            $data['marketingList'] = MarketingModel::get();
            $data['ptList'] = PersonalTrainerModel::get();

            $data['title'] = 'Lihat Data Member';
            $data['role'] = $this->checkAuth();
            $data['username'] = Auth::user()->name;
            $data['app_layout'] = $this->defineLayout($role);

            if($data['data'] != null){
                return view('member.management.view', $data);
            }else{
                dd('data tidak ditemukan');
            }
        }
    }

    public function edit($id){
        $role = $this->checkAuth();

        if(isset($role)){
            $title = 'Edit Data Member';
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            $data['data'] = MemberModel::where('member_id', $id)->first();
            $data['cache'] = MemberCacheModel::where('author', $id)->first();

            if($data['data']){
                $data['membership'] = MembershipModel::where('mship_id', $data['data']->membership)->first();
            }else{
                abort(404);
            }

            $data['pt'] = PersonalTrainerModel::where('pt_id', $data['cache']->id_pt)->first();
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
            $data['session'] = SessionModel::get();
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

            if($data['data'] != null){
                return view('member.management.edit', $data);
            }else{
                abort(404);
            }
        }
    }

    public function update(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        if($r->dataMarketing == "nothing"){
            $marketingName = null;
            $marketingID = null;
        }else{
            $marketingName = $r->cacheMarketing;
            $marketingID = $r->dataMarketing;
        }

        if($r->dataPT == "nothing"){
            $ptName = null;
            $ptID = null;
        }else{
            $ptName = $r->cachePT;
            $ptID = $r->dataPT;
        }

        $data = MemberModel::where('member_id', $r->hiddenID)->update([
            'name' => $r->dataNama,
            'gender' => $r->dataGender,
            'job' => $r->dataJob,
            'company' => $r->dataCompany,
            'phone' => $r->dataPhone,
            'email' => $r->dataEmail,
            'photo' => $r->photoFile,
            'marketing' => $marketingName,
            'pt' => $ptName,
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->role_id
        ]);

        $cache = MemberCacheModel::where('author', $r->hiddenID)->update([
            'id_pt' => $ptID,
            'id_marketing' => $marketingID
        ]);

        $role = Auth::user()->role_id;

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.index')->with(['success' => 'Data Member Berhasil Diubah']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
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
                    '<button type="button" class="btn btn-dark mt-0 ml-1 w-100" data-dismiss="modal" onclick="ubahPaket();">
                        <i class="fas fa-plus mr-1 fa-sm"></i> Ganti Paket
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
                        <i class="fas fa-plus mr-1 fa-sm"></i>  Perpanjang
                     </button>
                     <button type="button" class="btn btn-outline-dark mt-0 ml-1 w-100" data-dismiss="modal" onclick="ubahPaket();">
                        <i class="fas fa-edit mr-1 fa-sm"></i> Ganti Paket
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
            return '<button type="button" class="btn btn-dark w-100">
                        <i class="fas fa-plus mr-1 fa-sm"></i> Daftar Paket PT
                    </button>';
        }
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }

    public function aktivasi(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();
        $date_end = Carbon::now()->addMonths($r->duration)->toDateString();

        $data = MemberModel::where('member_id', $r->id)->update([
            'status' => 1,
            'm_startdate' => $date_now,
            'm_enddate' => $date_end,
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
                'PK.additional')
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
                'PK.t_sesi')
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
        date_default_timezone_set("Asia/Bangkok");
        $date_now = Carbon::now();

        $successMessage = "";

        if($r->sTransaction == "extend-session"){
            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                'session_reg' => ($r->sOld + $r->nSession),
                'session' => ($r->lOld + $r->nSession),
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->role_id
            ]);

            if($r->nTitle == ""){
                $fullSessionName = $r->nSession." Session";
            }else{
                $fullSessionName = $r->nTitle." - ".$r->nSession." Session";
            }

            $log = MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Pembelian Sesi PT - '.$fullSessionName,
                'category' => 5,
                'transaction' => $r->nPrice,
                'status' => 'Lunas',
                'author' => $r->sHiddenID,
                'additional' => $r->nPayment,
                'reg_no' => ($r->nRegNo + 1),
                'aksi' => 'sesi',
                't_sesi' => $r->nPrice
            ]);

            $successMessage = 'Pembelian Sesi Berhasil!';

        }else if($r->sTransaction == "change-membership"){
            $member['member'] = MemberModel::where('member_id', $r->sHiddenID)->first();
            $new_enddate = Carbon::parse($member['member']->m_enddate)->addMonths($r->mShipDuration)->toDateString();

            $data = MemberModel::where('member_id', $r->sHiddenID)->update([
                'membership' => $r->mShipID,
                'm_enddate' => $new_enddate,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->role_id
            ]);

            $log = MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Pembelian Paket Member - '.$r->mShipName,
                'category' => 5,
                'transaction' => $r->mShipPrice,
                'status' => 'Lunas',
                'author' => $r->sHiddenID,
                'additional' => $r->nPayment,
                'reg_no' => ($r->nRegNo + 1),
                'aksi' => 'membership',
                't_membership' => $r->mShipPrice
            ]);

            $successMessage = 'Pembelian Paket Member Berhasil!';
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
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('d M Y');

        $data['data'] = MemberLogModel::from("logmember as PK")
                        ->join("memberdata as mData", "mData.member_id", "=", "PK.author")
                        ->where("PK.log_id", $log_id)
                        ->first();

        if($data['data']->aksi == "sesi"){
            $data['title'] = 'RETENTION SESSION INVOICE';
            $DOC_NAME = "INVOICE_RI_S_".$data['data']->member_id;
        }else if($data['data']->aksi == "membership"){
            $data['title'] = 'RETENTION MEMBERSHIP INVOICE';
            $DOC_NAME = "INVOICE_RI_M_".$data['data']->member_id;
        }

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
            'updated_by' => Auth::user()->role_id
        ]);

        if($this->checkAuth() == 1){
            return redirect()->route('suadmin.member.edit', $r->ptEditHiddenID)->with(['success' => 'Nama Personal Trainer Berhasil Diubah!']);
        }else if($this->checkAuth() == 2){
            //STILL EMPTY
        }else if($this->checkAuth() == 3){
            return redirect()->route('cs.member.edit', $r->ptEditHiddenID)->with(['success' => 'Nama Personal Trainer Berhasil Diubah!']);
        }
    }
}
