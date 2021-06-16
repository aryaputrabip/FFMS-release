<?php

namespace App\Http\Controllers\member\Registration;

use App\Model\marketing\MarketingModel;
use App\Model\member\CicilanDataModel;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberFamilyModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\membership\MembershipModel;
use App\Model\payment\BankModel;
use App\Model\payment\PaymentModel;
use App\Model\pt\PersonalTrainerModel;
use App\Model\session\SessionModel;
use App\Model\system\SysModel;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MemberRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Registrasi Member';
            $membership = MembershipModel::from("membership as PK")
                ->join("membership_category as mShipCategory", "mShipCategory.id", "=", "PK.category")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "PK.type")
                ->select(
                    'PK.mship_id',
                    'PK.name',
                    'PK.duration',
                    'PK.price',
                    'PK.status',
                    'mShipType.type as type',
                    'mShipCategory.member as tMember'
                )->where('status', 1)->orderBy('mship_id')->get();

            $pt = PersonalTrainerModel::latest()->where('status', 1)->orderBy('name')->get();
            $session = SessionModel::latest()->orderBy('duration')->where('status', 1)->get();
            $marketing = MarketingModel::latest()->where('status', 1)->orderBy('name')->get();
            $payment = PaymentModel::latest()->orderBy('id')->get();
            $debitType = BankModel::latest()->where('model', 2)->get();
            $creditType = BankModel::latest()->where('model', 3)->get();
            $username = Auth::user()->name;
            $app_layout = $this->defineLayout($role);

            return view('member.management.register',
                compact('title','username','role','app_layout','membership','pt','session','marketing','payment','debitType','creditType'));
        }
    }

    public function checkAuth(){
        $role = Auth::user()->role_id;

        if($role == 1){
            $this->authorize('sudata');
        }elseif($role == 2){
            $this->authorize('admindata');
        }elseif($role == 3){
            $this->authorize('csdata');
        }

        return $role;
    }

    public function defineLayout($role){
        if($role == 1 || $role == 2){
            return 'layouts.app_admin';
        }else{
            return 'layouts.app_cs';
        }
    }

    public function store(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        if(isset($r->cacheMemberSession)){
            $session = $r->dataUserPTSession;
        }else{ $session = null; }

        if(isset($r->cacheMemberSessionTitle)){
            $sessionTitle = $r->cacheMemberSessionTitle;
            $logDesc = "Registrasi Member Baru + Personal Trainer - ".$sessionTitle." (".$session." Sesi)";
        }else{
            $logDesc = "Registrasi Member Baru + Personal Trainer (".$session." Sesi)";
            $sessionTitle = null;
        }

        if(isset($r->cacheMemberPT)){
            if($r->dataUserPT == "nothing"){
                $namaPT = null; $PT = $r->dataUserPT;
            }else{ $namaPT = $r->cacheMemberPT; $PT = $r->dataUserPT; }
        }else{
            $logDesc = 'Registrasi Member Baru';
            $namaPT = null;
            $PT = null;
        }

        if(isset($r->cacheMemberSessionPrice)){
            if(isset($r->cachePTApproval)){
                $sessionPrice = $r->approvalSesiPrice;
            }else{
                $sessionPrice = $r->cacheMemberSessionPrice;
            }
        }else{ $sessionPrice = null; }

        if(isset($r->cacheMemberMarketing)){
            $namaMarketing = $r->cacheMemberMarketing;
            $marketing = $r->dataUserMarketing;
        }else{ $namaMarketing = null; $marketing = null; }

        if(isset($r->cacheMemberApproval)){
            $membershipPrice = $r->approvalPrice;
            $membership = $r->cacheMembership;
        }else{
            $membershipPrice = $r->cacheMembershipPrice;
            $membership = $r->cacheMembership;
        }

        $data['log'] = MemberLogModel::from("logmember")->latest('date')->first();

        $data['systemDay'] = SysModel::from("sysdata as PK")
            ->select('PK.sysdata','PK.value')->where("PK.sysid", 1)->first();

        $data['systemMonth'] = SysModel::from("sysdata as PK")
            ->select('PK.sysdata','PK.value')->where("PK.sysid", 2)->first();

        $data['systemYear'] = SysModel::from("sysdata as PK")
            ->select('PK.sysdata','PK.value')->where("PK.sysid", 3)->first();

        $nSystemNum = 1;

        if($data['log']->reg_no == null || $data['log']->reg_no == ""){
            $nSystemNum = 1;
        }else{
            $nSystemNum = $data['log']->reg_no;
        }

        $nSystemDay = $data['systemDay']->value;
        $nSystemMonth = $data['systemMonth']->value;
        $nSystemYear = $data['systemYear']->value;

        if(Carbon::now()->month == $nSystemMonth && Carbon::now()->year == $nSystemYear){
            $nSystemNum += 1;

            if(Carbon::now()->day != $nSystemDay){
                $sysUpdateDay = SysModel::where('sysid', 1)->update([
                    'value' => Carbon::now()->day
                ]);
            }
        }else{
            $nSystemNum += 1;

            if(Carbon::now()->month != $nSystemMonth){
                $sysUpdateMonth = SysModel::where('sysid', 2)->update([
                    'value' => Carbon::now()->month
                ]);
            }

            if(Carbon::now()->year != $nSystemYear){
                $sysUpdateYear = SysModel::where('sysid', 3)->update([
                    'value' => Carbon::now()->year
                ]);
            }
        }

        //RANDOM ID GENERATE
        $generate_by_date = date('dm');
        $generatedMemberID = $generate_by_date . substr($r->dataUserPhone, -4);

        while($this->memberIDChecking($generatedMemberID) != null){
            $randGenerate = rand(0, 9999);

            if($randGenerate < 10){
                $generatedMemberID = $generate_by_date . "000" . $randGenerate;
            }else if($randGenerate < 100){
                $generatedMemberID = $generate_by_date . "00" . $randGenerate;
            }else if($randGenerate < 1000){
                $generatedMemberID = $generate_by_date . "0" . $randGenerate;
            }else{
                $generatedMemberID = $generate_by_date . $randGenerate;
            }
        }

        //CREATE DATA
        $data = MemberModel::create([
            'member_id' =>  $generatedMemberID,
            'name' => $r->dataUserNama,
            'gender' => $r->dataUserGender,
            'email' => $r->dataUserEmail,
            'phone' => $r->dataUserPhone,
            'job' => $r->dataUserJob,
            'company' => $r->dataUserCompany,
            'membership' => $r->cacheMembershipID,
            'pt' => $namaPT,
            'marketing' => $namaMarketing,
            'session_reg' => $session,
            'session' => $session,
            'status' => 2,
//            'm_startdate' => $date_now, //Karena Presale, Maka Tidak  Langsung Aktif
            'created_by' => Auth::user()->id,
            'created_at' => $date_now,
            'visitlog' => 0,
            'photo' => $r->photoFile,
            'member_notes' => $r->dataNote
        ]);

        //CREATE DATA FOR FAMILY
        if($r->cacheMembershipCategory > 1){
            for($i = 0; $i < $r->cacheMembershipCategory; $i++){
                MemberFamilyModel::create([
                    'member_id' =>  $generatedMemberID,
                    'name' => $r->input("family_".$i."_name"),
                    'gender' => $r->input("family_".$i."_gender"),
                    'email' => $r->input("family_".$i."_email"),
                    'phone' => $r->input("family_".$i."_phone"),
                    'job' => $r->$r->input("family_".$i."_job"),
                    'company' => $r->$r->input("family_".$i."_company"),
                ]);
            }
        }

        if($r->paymentMethodGroup == "cicilan"){
            $statusBayar = "Dalam Cicilan";
            $chargePrice = $membershipPrice + $sessionPrice;
            $restData = $chargePrice - ($chargePrice / $r->paymentCicilanDuration);
            $restDataTransaction = $chargePrice / $r->paymentCicilanDuration;

            if($sessionPrice != null || $sessionPrice > 0){
                $restDataMembership = ($chargePrice / $r->paymentCicilanDuration)/2;
                $restDataSesi = (int) ($chargePrice / $r->paymentCicilanDuration)/2;
            }else{
                $restDataMembership = $chargePrice / $r->paymentCicilanDuration;
                $restDataSesi = null;
            }
        }else{
            $statusBayar = "Lunas";
            $chargePrice = $membershipPrice + $sessionPrice;
            $restData = $chargePrice;
            $restDataTransaction = $chargePrice;
            $restDataMembership = $membershipPrice;
            if($sessionPrice != null || $sessionPrice > 0){
                $restDataSesi = (int) $sessionPrice;
            }else{
                $restDataSesi = null;
            }
        }

        if(isset($r->cachepaymentType)){
            $startLog = MemberLogModel::create([
                'date' => $date_now,
                'desc' => $logDesc,
                'category' => 1,
                'status' => $statusBayar,
                'transaction' => (int) $restDataTransaction,
                'author' => $data->member_id,
                'additional' => $r->cachepaymentType,
                'reg_no' => $nSystemNum,
                't_membership' => (int) $restDataMembership,
                't_sesi' => $restDataSesi,
                'aksi' => 'register',
                'notes' => $r->dataNote
            ]);
        }else{
            $startLog = MemberLogModel::create([
                'date' => $date_now,
                'desc' => $logDesc,
                'category' => 1,
                'status' => $statusBayar,
                'transaction' => (int) $restDataTransaction,
                'author' => $data->member_id,
                'additional' => $r->cachePaymentModel,
                'reg_no' => $nSystemNum,
                't_membership' => (int) $restDataMembership,
                't_sesi' => $restDataSesi,
                'aksi' => 'register',
                'notes' => $r->dataNote
            ]);
        }

        if($r->paymentMethodGroup == "cicilan"){
            $cicilan = CicilanDataModel::create([
               'author' => $data->member_id,
               'rest_duration' => ($r->paymentCicilanDuration - 1),
               'rest_price' => $chargePrice,
               'rest_data' => (int) $restData,
               'rest_membership' => $logDesc,
               'created_at' => $date_now
            ]);
        }

        if(isset($r->cacheMemberApproval)){
            $cacheMember = MemberCacheModel::create([
                'author' => $data->member_id,
                'id_pt' => $PT,
                'id_marketing' => $marketing,
                'session_title' => $sessionTitle,
                'session_price' => $sessionPrice,
            ]);
        }else{
            $cacheMember = MemberCacheModel::create([
                'author' => $data->member_id,
                'id_pt' => $PT,
                'id_marketing' => $marketing,
                'session_title' => $sessionTitle,
                'session_price' => $sessionPrice,
                'approval_price' => $r->approvalPrice
            ]);
        }

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.member.registration.complete', ['mdata' => $data->member_id]);
        }elseif(Auth::user()->role_id == 2){
//            return redirect()->route('admin.registration.complete', ['mdata' => $data->member_id]);
        }elseif(Auth::user()->role_id == 3){
            return redirect()->route('cs.member.registration.complete', ['mdata' => $data->member_id]);
        }
    }

    public function complete(){
        $title = 'Registrasi Member';
        $role = $this->checkAuth();
        $username = Auth::user()->name;
        $app_layout = $this->defineLayout($role);

        return view('member.management.finish', compact('title','role','username','app_layout'));
    }

    function memberIDChecking($id){
        $data = MemberModel::from("memberdata")->where("member_id", $id)->first();
        if(isset($data)){
            return $data;
        }else{
            return null;
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
                'PK.notes',
                'PK.status as status',
                'PK.t_membership as t_membership',
                'PK.t_sesi as t_sesi'
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

        if($data['log']->status == "Dalam Cicilan"){
            $data['data']->membershipPrice = $data['log']->transaction;
            $data['log']->t_membership = $data['log']->transaction;
            if($data['session'] != null){
                $data['data']->membershipPrice = $data['log']->t_membership;
                $data['log']->t_membership = $data['log']->t_membership;
                $data['session']->sessionPrice = $data['log']->t_sesi;
            }
        }else{
            if($data['session']->session_price != null){
                $data['data']->membershipPrice = $data['log']->transaction - $data['session']->session_price;
            }else{
                $data['data']->membershipPrice = $data['log']->transaction;
            }
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

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
