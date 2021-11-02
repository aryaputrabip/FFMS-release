<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\gstatus\GlobalStatusModel;
use App\Model\marketing\MarketingModel;
use App\Model\member\CicilanDataModel;
use App\Model\member\MemberCategoryModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\membership\MembershipCategoryModel;
use App\Model\membership\membershipListCacheModel;
use App\Model\membership\MembershipModel;
use App\Model\membership\MembershipStatusModel;
use App\Model\membership\MembershipTypeModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MembershipDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Data Membership';
            $jMembership = MembershipModel::from('membership')->count();
            $membershipActive = MembershipModel::from('membership')->where('status', 1)->count();
            $type = MembershipModel::from('membership_type')->get();
            $category = MemberCategoryModel::from('membership_category')->get();
            $status = GlobalStatusModel::from('membership_status')->get();
            $username = Auth::user()->name;
            $url = "membership.store";
            $app_layout = $this->defineLayout($role);

            $membershipType = MembershipTypeModel::select('type')->get();
            $membershipCategory = MembershipCategoryModel::select('category')->get();
            $membershipStatus = MembershipStatusModel::select('status')->get();

            return view('membership.index',
                compact('title','username','role','url','app_layout','type','status','jMembership','membershipActive','category','membershipType', 'membershipStatus', 'membershipCategory'));
        }
    }

    public function checkAuth(){
        $this->authorize('sudata');
        $role = Auth::user()->role_id;

        return $role;
    }

    public function defineLayout($role){
        return 'layouts.app_admin';
    }

    public function getMembershipData(Request $request){
        if($request->ajax()){
            $data = MembershipModel::from("membership as PK")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "PK.type")
                ->join("membership_status as mShipStatus", "mShipStatus.id", "=", "PK.status")
                ->join("membership_category as mShipCat", "mShipCat.id", "=", "PK.category")
                ->select(
                    'PK.mship_id',
                    'PK.name',
                    'mShipType.type as membershipType',
                    'mShipCat.category as membershipCategory',
                    'PK.duration',
                    'PK.price',
                    'PK.status as pkstatus',
                    'mShipStatus.status as membershipStatus'
                )
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('membership_type', function ($data) {
                    return '<div class="text-left">'.$data->membershipType.'</div>';
                })
                ->addColumn('membershipCategory', function ($data) {
                    return '<div class="text-left">'.$data->membershipCategory.'</div>';
                })
                ->addColumn('duration', function ($data) {
                    return '<div class="text-left">'.$data->duration.' Bulan</div>';
                })
                ->addColumn('price', function ($data) {
                    return '<div class="text-left">'.$this->asRupiah($data->price).'</div>';
                })
                ->addColumn('membershipStatus', function ($data) {
                    if($data->pkstatus == 1){
                        return '<div class="text-center font-weight-bold text-success">'.$data->membershipStatus.'</div>';
                    }else{
                        return '<div class="text-center font-weight-bold text-danger">'.$data->membershipStatus.'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<center>
                                <a href="#modal-membership" class="btn btn-default btn-sm" title="Ubah" data-toggle="modal" onclick="editDataOf('.$data->mship_id.'); membershipEditMode();">
                                    <i class="fa fa-edit text-warning"></i>
                                </a>
                            </center>';
                })
                ->rawColumns(['action', 'name', 'membershipType', 'membershipCategory', 'duration', 'price', 'membershipStatus'])
                ->make(true);
        }
    }

    public function store(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = MembershipModel::create([
            'name' => $r->membershipName,
            'desc' => $r->membershipDesc,
            'type' => $r->membershipType,
            'duration' => $r->membershipDuration,
            'price' => $r->membershipPrice,
            'created_by' => Auth::user()->id,
            'created_at' => $date_now,
            'status' => 2,
            'category' => $r->membershipCategory
        ]);

        return redirect()->route('suadmin.membership.index')->with(['success' => 'Paket Member Berhasil Ditambahkan']);
    }

    public function edit(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $data['data'] = MembershipModel::from("membership as PK")
            ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "PK.type")
            ->join("membership_status as mShipStatus", "mShipStatus.id", "=", "PK.type")
            ->join("membership_category as mShipCat", "mShipCat.id", "=", "PK.category")
            ->select(
                'PK.mship_id',
                'PK.name',
                'PK.desc',
                'PK.duration',
                'PK.price',
                'PK.status',
                'PK.type',
                'PK.category'
            )->where('mship_id', $r->mship_id)->first();

        $data['subscription'] = MemberModel::where('membership', $r->mship_id)->get()->count();

        $data['url'] = route('membership.update');

        $data['delete_button'] = '
            <button type="button" id="deleteMembership" class="p-2 ml-3 btn btn-danger" style="position: absolute; left:0;" onclick="destroy('.$r->mship_id.')">
                <i class="fas fa-trash fa-sm" title="Hapus"></i>
            </button>';
        return $data;
    }

    public function update(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = MembershipModel::where('mship_id', $r->hiddenID)->update([
            'name' => $r->membershipName,
            'desc' => $r->membershipDesc,
            'duration' => $r->hiddenDuration,
            'price' => $r->membershipPrice,
            'status' => $r->membershipStatus,
            'type' => $r->membershipType,
            'category' => $r->membershipCategory,
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->id,
        ]);
        return redirect()->route('suadmin.membership.index')->with(['success' => 'Paket Member Berhasil Diubah']);
    }

    public function destroy(Request $r){
        $exec = MembershipModel::destroy($r->mship_id);

        if($exec){
            redirect()->route('suadmin.membership.index')->with(['success' => 'Paket Member Berhasil Dihapus']);
        }else{
            redirect()->route('suadmin.membership.index')->with(['error' => 'Paket Member Gagal Dihapus']);
        }
    }

    function dataChecking(){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now()->toDateString();

        $data['data'] = membershipListCacheModel::whereDate('end_date', '<', $date_now)->delete();

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.index');
        }elseif(Auth::user()->role_id == 2){
//              //
        }elseif(Auth::user()->role_id == 3){
            return redirect()->route('cs.index');
        }
    }

    function showMembershipList(Request $r){

    }

    function changeMembership(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $member['memberdata'] = MemberModel::where('member_id', $r->uHiddenID)->first();

        $membership['membership'] = MembershipModel::where('mship_id', $r->membership)->first();
        $member['membershipcache'] = membershipListCacheModel::where('author', $r->uHiddenID)->orderBy('start_date', 'ASC');

        if($r->payment_method == "cicilan"){
            if($r->debt_first_pay != "null"){
                $jumlah_transaksi = $r->debt_first_pay;
            }else{
                $jumlah_transaksi = ($r->price - $r->discount) / $r->debt_length;
            }

            $sisa_durasi = ($r->debt_length - 1);

        }else if($r->payment_method == "tunda"){
            $jumlah_transaksi = 0;
            $sisa_durasi = $r->debt_length;
        }else{
            $jumlah_transaksi = ($r->price - $r->discount);
            $sisa_durasi = 0;
        }

        if($r->payment_method == "cicilan" || $r->payment_method =="tunda"){
            //JIKA PEMBAYARAN MENGGUNAKAN CICILAN ATAU TUNDA BAYAR = SET STATUS MENJADI DALAM CICILAN
            $status_transaksi = "Dalam Cicilan";
            $rest_membership = "Ubah Paket Member";

            CicilanDataModel::create([
                'author' => $r->uHiddenID,
                'rest_duration' => $sisa_durasi ,
                'rest_price' => $r->price,
                'rest_data' => ($r->price - $jumlah_transaksi),
                'rest_membership' => $rest_membership,
                'created_at' => $date_now
            ]);
        }else{
            // JIKA PEMBAYARAN LUNAS = SET STATUS MENJADI LUNAS
            $status_transaksi = "lunas";
        }

        if($member['memberdata']->status == 4) {
            //IF MEMBER EXPIRED, THEN CHANGE MEMBERSHIP END DATE TO TODAY + MEMBERSHIP DURATION
            $expired_date = Carbon::now()->addMonths($membership['membership']->duration)->toDateString();

            //UPDATE MEMBER NEW START AND EXPIRED DATE
            MemberModel::where('member_id', $r->uHiddenID)->update([
                'status' => 1, //ACTIVE
                'membership' => $r->membership,
                'm_startdate' => $date_now,
                'm_enddate' => $expired_date,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            membershipListCacheModel::create([
                'author' => $r->uHiddenID,
                'membership_id' => $r->membership,
                'start_date' => $date_now,
                'end_date' => $expired_date,
                'payment' => $jumlah_transaksi
            ]);

            MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Perpanjangan Paket Member - '.$membership['membership']->name,
                'category' => 5,
                'transaction' => $jumlah_transaksi,
                'status' => $status_transaksi,
                'author' => $r->uHiddenID,
                'additional' => $r->payment_addition,
                'reg_no' => ($r->regNo + 1),
                'aksi' => 'membership',
                't_membership' => $jumlah_transaksi,
                'notes' => $r->note
            ]);

            $successMessage = 'Ganti Paket Member Berhasil!';
            $redirectNotif = 'success';
        }else if($member['memberdata']->status == 1){
            // IF MEMBER ACTIVE, THEN ADD NEW MEMBERSHIP INTO QUEUE
            $start_date     = Carbon::parse($member['memberdata']->m_enddate)->addDays(1)->toDate();
            $expired_date   = Carbon::parse($member['memberdata']->m_enddate)->addMonths($membership['membership']->duration)->toDateString();

            //echo("rest_duration : ".$sisa_durasi." | rest_price : ".$r->price." | rest_data : ".($r->price - $jumlah_transaksi)." | ");
            //echo("expired : ".$member['memberdata']->m_enddate." | new expired : ".$expired_date);
           // dd("membership active");

            //UPDATE MEMBER NEW START AND EXPIRED DATE
            MemberModel::where('member_id', $r->uHiddenID)->update([
                'm_enddate' => $expired_date,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            membershipListCacheModel::create([
                'author' => $r->uHiddenID,
                'membership_id' => $r->membership,
                'start_date' => $start_date,
                'end_date' => $expired_date,
                'payment' => $jumlah_transaksi
            ]);

            MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Perpanjangan Paket Member - '.$membership['membership']->name,
                'category' => 5,
                'transaction' => $jumlah_transaksi,
                'status' => $status_transaksi,
                'author' => $r->uHiddenID,
                'additional' => $r->payment_addition,
                'reg_no' => ($r->regNo + 1),
                'aksi' => 'membership',
                't_membership' => $jumlah_transaksi,
                'notes' => $r->note
            ]);

            $successMessage = 'Ganti Paket Member Berhasil!';
            $redirectNotif = 'success';
        }else{
            // IF MEMBER CUTI OR NOT ACTIVATED, THE CHANGE MEMBERSHIP REQUEST REJECTED
            $successMessage = 'Terjadi Kesalahan Ketika Ganti Paket Member!';
            $redirectNotif = 'error';
        }

//        //IF MEMBER EXPIRED
//        if($member['membership']->status == 4){
//            //IF MEMBER EXPIRED, THEN CHANGE MEMBERSHIP END DATE TO TODAY + MEMBERSHIP DURATION
//
//
//        }else{
//            //IF MEMBER IS STILL ACTIVE / CUTI THEN CHANGE MEMBERSHIP END DATE TO LATEST MEMBERSHIP END DATE + MEMBERSHIP DURATION
//            $start_date     = Carbon::parse($member['member']->m_enddate)->addDays(1)->toDate();
//            $expired_date   = Carbon::parse($member['member']->m_enddate)->addMonths($membership['membership']->duration)->toDateString();
//
//            MemberModel::where('member_id', $r->uHiddenID)->update([
//                'membership' => $r->mHiddenID,
//                'm_enddate' => $start_date,
//                'updated_at' => $date_now,
//                'updated_by' => Auth::user()->id
//            ]);
//        }
//
//        membershipListCacheModel::create([
//            'author' => $r->uHiddenID,
//            'membership_id' => $r->mHiddenID,
//            'start_date' => $start_date,
//            'end_date' => $expired_date,
//            'payment' => $jumlah_transaksi
//        ]);

//        $successMessage = 'Pembelian Paket Member Berhasil!';

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.member.edit', $r->uHiddenID)->with(['success' => $successMessage]);
        }else if(Auth::user()->role_id == 2){
            //STILL EMPTY
        }else if(Auth::user()->role_id == 3){
            return redirect()->route('cs.member.edit', $r->uHiddenID)->with(['success' => $successMessage]);
        }
    }

    function extendMembership(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();


        $member['memberdata'] = MemberModel::where('member_id', $r->uHiddenID)->first();

        $membership['membership'] = MembershipModel::where('mship_id', $r->membership)->first();

        if($r->payment_method == "cicilan"){
            if($r->debt_first_pay != "null"){
                $jumlah_transaksi = $r->debt_first_pay;
            }else{
                $jumlah_transaksi = ($r->price - $r->discount) / $r->debt_length;
            }

            $sisa_durasi = ($r->debt_length - 1);

        }else if($r->payment_method == "tunda"){
            $jumlah_transaksi = 0;
            $sisa_durasi = $r->debt_length;
        }else{
            $jumlah_transaksi = ($r->price - $r->discount);
            $sisa_durasi = 0;
        }

        //echo("rest_duration : ".$sisa_durasi." | rest_price : ".$r->price." | rest_data : ".($r->price - $jumlah_transaksi));

        if($r->payment_method == "cicilan" || $r->payment_method =="tunda"){
            //JIKA PEMBAYARAN MENGGUNAKAN CICILAN ATAU TUNDA BAYAR = SET STATUS MENJADI DALAM CICILAN
            $status_transaksi = "Dalam Cicilan";
            $rest_membership = "Perpanjang Paket Member";

            CicilanDataModel::create([
                'author' => $r->uHiddenID,
                'rest_duration' => $sisa_durasi ,
                'rest_price' => $r->price,
                'rest_data' => ($r->price - $jumlah_transaksi),
                'rest_membership' => $rest_membership,
                'created_at' => $date_now
            ]);
        }else{
            // JIKA PEMBAYARAN LUNAS = SET STATUS MENJADI LUNAS
            $status_transaksi = "lunas";
        }

        //echo("membership : ".$r->membership." | m_startdate : ".$date_now." | m_enddate : ".$expired_date);

        if($member['memberdata']->status == 4) {
            //IF MEMBER EXPIRED, THEN RENEW MEMBERSHIP END DATE TO TODAY + MEMBERSHIP DURATION
            $start_date = $date_now;
            $expired_date = Carbon::now()->addMonths($membership['membership']->duration)->toDateString();

            //UPDATE MEMBER NEW START AND EXPIRED DATE
            MemberModel::where('member_id', $r->uHiddenID)->update([
                'status' => 1, //ACTIVE
                'membership' => $r->membership,
                'm_startdate' => $date_now,
                'm_enddate' => $expired_date,
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            membershipListCacheModel::create([
                'author' => $r->uHiddenID,
                'membership_id' => $r->membership,
                'start_date' => $date_now,
                'end_date' => $expired_date,
                'payment' => $jumlah_transaksi
            ]);

            MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Perpanjangan Paket Member - '.$membership['membership']->name,
                'category' => 5,
                'transaction' => $jumlah_transaksi,
                'status' => $status_transaksi,
                'author' => $r->uHiddenID,
                'additional' => $r->payment_addition,
                'reg_no' => ($r->regNo + 1),
                'aksi' => 'membership',
                't_membership' => $jumlah_transaksi,
                'notes' => $r->note
            ]);

            $successMessage = 'Perpanjangan Paket Member Berhasil!';
            $redirectNotif = 'success';
        }else {
            $successMessage = 'Terjadi Kesalahan ketika Perpanjang Paket!';
            $redirectNotif = 'error';
        }

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.member.edit', $r->uHiddenID)->with([$redirectNotif => $successMessage]);
        }else if(Auth::user()->role_id == 2){
            //STILL EMPTY
        }else if(Auth::user()->role_id == 3){
            return redirect()->route('cs.member.edit', $r->uHiddenID)->with([$redirectNotif => $successMessage]);
        }
    }

    function upgradeMembership(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $member['memberdata'] = MemberModel::where('member_id', $r->uHiddenID)->first();

        $membership['membership'] = MembershipModel::where('mship_id', $r->membership)->first();
        $member['membershipcache'] = membershipListCacheModel::where('author', $r->uHiddenID)->orderBy('start_date', 'ASC')->get();
        $member['membershipcache_first'] = membershipListCacheModel::where('author', $r->uHiddenID)->orderBy('start_date', 'ASC')->first();

        if($r->payment_method == "cicilan"){
            if($r->debt_first_pay != "null"){
                $jumlah_transaksi = $r->debt_first_pay;
            }else{
                $jumlah_transaksi = ($r->price - $r->discount) / $r->debt_length;
            }

            $sisa_durasi = ($r->debt_length - 1);

        }else if($r->payment_method == "tunda"){
            $jumlah_transaksi = 0;
            $sisa_durasi = $r->debt_length;
        }else{
            $jumlah_transaksi = ($r->price - $r->discount);
            $sisa_durasi = 0;
        }

        if($r->payment_method == "cicilan" || $r->payment_method =="tunda"){
            //JIKA PEMBAYARAN MENGGUNAKAN CICILAN ATAU TUNDA BAYAR = SET STATUS MENJADI DALAM CICILAN
            $status_transaksi = "Dalam Cicilan";
            $rest_membership = "Upgrade Paket Member";

            CicilanDataModel::create([
                'author' => $r->uHiddenID,
                'rest_duration' => $sisa_durasi ,
                'rest_price' => $r->price,
                'rest_data' => ($r->price - $jumlah_transaksi),
                'rest_membership' => $rest_membership,
                'created_at' => $date_now
            ]);
        }else{
            // JIKA PEMBAYARAN LUNAS = SET STATUS MENJADI LUNAS
            $status_transaksi = "lunas";
        }


        if($member['memberdata']->status != 4) {
            //IF MEMBER IS NOT EXPIRED
            $index = 0;
            foreach ($member['membershipcache'] as $user_membership) {
                //echo $user_membership->start_date." | ";
                $new_start = "";
                $new_end = "";

                if($index == 0){
                    $new_start = $user_membership->start_date;
                    $new_end = Carbon::parse($user_membership->end_date)->addMonths($membership['membership']->duration)->subMonths($r->upgrade_duration)->toDateString();
                    //echo "[".$index."] => OLD START : ".$user_membership->start_date.", NEW START : ".$user_membership->start_date;
                    //echo" | [".$index."] => OLD END : ".$user_membership->end_date.", NEW END : ".Carbon::parse($user_membership->end_date)->addMonths($membership['membership']->duration)->toDateString();

                    membershipListCacheModel::where('id', $user_membership->id)->update([
                        'membership_id' => $r->membership,
                        'start_date' => $new_start,
                        'end_date' => $new_end
                    ]);
                }else{
                    $new_start = Carbon::parse($user_membership->start_date)->addMonths($membership['membership']->duration)->subMonths($r->upgrade_duration)->toDateString();
                    $new_end = Carbon::parse($user_membership->end_date)->addMonths($membership['membership']->duration)->subMonths($r->upgrade_duration)->toDateString();

                    //echo " | [".$index."] => OLD START : ".$user_membership->start_date.", NEW START : ".Carbon::parse($user_membership->start_date)->addMonths($membership['membership']->duration)->toDateString();
                    //echo " | [".$index."] => OLD END : ".$user_membership->end_date.", NEW END : ".Carbon::parse($user_membership->end_date)->addMonths($membership['membership']->duration)->toDateString();

                    membershipListCacheModel::where('id', $user_membership->id)->update([
                        'start_date' => $new_start,
                        'end_date' => $new_end
                    ]);
                }

                $index++;
            }

            MemberModel::where('member_id', $r->uHiddenID)->update([
                'status' => 1, //ACTIVE
                'membership' => $r->membership,
                'm_enddate' => Carbon::parse($member['memberdata']->m_enddate)->addMonths($membership['membership']->duration)->subMonths($r->upgrade_duration)->toDateString(),
                'updated_at' => $date_now,
                'updated_by' => Auth::user()->id
            ]);

            MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Upgrade Paket Member - '.$membership['membership']->name,
                'category' => 5,
                'transaction' => $jumlah_transaksi,
                'status' => $status_transaksi,
                'author' => $r->uHiddenID,
                'additional' => $r->payment_addition,
                'reg_no' => ($r->regNo + 1),
                'aksi' => 'membership',
                't_membership' => $jumlah_transaksi,
                'notes' => $r->note
            ]);

            //dd($member['membershipcache'][0]);

            $successMessage = 'Upgrade Paket Member Berhasil!';
            $redirectNotif = 'success';
        }else {
            $successMessage = 'Terjadi Kesalahan ketika Upgrade Paket!';
            $redirectNotif = 'error';
        }

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.member.edit', $r->uHiddenID)->with([$redirectNotif => $successMessage]);
        }else if(Auth::user()->role_id == 2){
            //STILL EMPTY
        }else if(Auth::user()->role_id == 3){
            return redirect()->route('cs.member.edit', $r->uHiddenID)->with([$redirectNotif => $successMessage]);
        }
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
