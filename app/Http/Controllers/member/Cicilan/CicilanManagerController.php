<?php

namespace App\Http\Controllers\member\Cicilan;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\member\CicilanDataModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\payment\BankModel;
use App\Model\payment\PaymentModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CicilanManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $validateRole = new ValidateRole();
        $role = $validateRole->checkAuthADM();

        $title = 'Management Cicilan Member';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $tMemberCicilan = CicilanDataModel::count();
            $memberCicilanLK = CicilanDataModel::from("cicilandata as CICILAN")
                ->join("memberdata as MEMBER", "MEMBER.member_id", "=", "CICILAN.author")
                ->where("MEMBER.gender", "Laki-laki")->count();
            $memberCicilanPR = CicilanDataModel::from("cicilandata as CICILAN")
                ->join("memberdata as MEMBER", "MEMBER.member_id", "=", "CICILAN.author")
                ->where("MEMBER.gender", "Perempuan")->count();

            $payment = PaymentModel::latest()->orderBy('id')->get();
            $debitType = BankModel::latest()->where('model', 2)->get();
            $creditType = BankModel::latest()->where('model', 3)->get();

            return view('cicilan.cicilan_manager', compact('title','username','app_layout','role',
                    'tMemberCicilan','memberCicilanLK', 'memberCicilanPR', 'payment','debitType','creditType'));
        }
    }

    public function cicilanMemberData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        if($r->ajax()) {
            $data = CicilanDataModel::from("cicilandata as CICILAN")
                ->join("memberdata as MEMBER", "MEMBER.member_id", "=", "CICILAN.author")
                ->join("member_status as STATUS", "STATUS.mstatus_id", "=", "MEMBER.status")
                ->select(
                    'MEMBER.member_id as member_id',
                    'MEMBER.name as name',
                    'MEMBER.gender as gender',
                    'STATUS.status as status',
                    'CICILAN.rest_duration as tenor',
                    'CICILAN.rest_data as pembayaran'
                )->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('member_id', function ($data) {
                    return '<div class="text-left">'.$data->member_id.'</div>';
                })
                ->addColumn('name', function ($data) {
                    return '<div class="text-left">'.$data->name.'</div>';
                })
                ->addColumn('gender', function ($data) {
                    return '<div class="text-left">'.$data->gender.'</div>';
                })
                ->addColumn('status', function ($data) {
                    if($data->status == "Non-Aktif" || $data->status == "Expired"){
                        return '<div class="text-center text-danger">'.$data->status.'</div>';
                    }else{
                        return '<div class="text-center text-success">'.$data->status.'</div>';
                    }
                })
                ->addColumn('tenor', function ($data) {
                    return '<div class="text-left">'.$data->tenor.' Bulan</div>';
                })
                ->addColumn('pembayaran', function ($data) {
                    return '<div class="text-left">'.$this->asRupiah($data->pembayaran).'</div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="align-middle pl-0 pr-0">
                                <button class="btn edit-user-group btn-outline-success btn-sm" onclick="payDebt(`'.$data->member_id.'`)">
                                    <i class="fas fa-pencil-alt fa-sm"></i> Bayar
                                </button>
                            </div>';
                })
                ->rawColumns(['member_id','name','gender','status','tenor','pembayaran','action'])
                ->make(true);
        }
    }

    function getMemberCicilanData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        $data['data'] = MemberModel::from("memberdata as MEMBER")
                            ->join("cicilandata as CICILAN", "CICILAN.author", "=", "MEMBER.member_id")
                            ->select([
                                "MEMBER.member_id as member_id",
                                "MEMBER.name as name",
                                "MEMBER.gender as gender",
                                "CICILAN.rest_duration as tenor",
                                "CICILAN.rest_price as total_cicilan",
                                "CICILAN.rest_data as sisa_cicilan"
                            ])->where('MEMBER.member_id', $r->member_id)->first();

        return $data;
    }

    function bayarCicilan(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $data['data'] = CicilanDataModel::where('author', $r->hiddenID)->first();
        $data['logcount'] = MemberLogModel::where('category', 5)->count();


        if($r->hiddenDataPaymentType == "manual"){
            $data['price_source'] = $data['data']->rest_data - $r->hiddenDataPrice;

            $data['result'] = CicilanDataModel::where('author', $r->hiddenID)->update([
                'rest_data' => (int) $data['price_source'],
                'updated_at' => $date_now
            ]);

            if((int) $data['price_source'] <= 0){
                $data['result'] = CicilanDataModel::where('author', $r->hiddenID)->delete();
                $data['paymentStatus'] = "Lunas";
            }else{
                $data['paymentStatus'] = "Dalam Cicilan";
            }

            $data['log'] = MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Pembayaran Cicilan - ' . $data['data']->rest_membership,
                'category' => 5,
                'transaction' => $r->hiddenDataPrice,
                'status' => $data['paymentStatus'],
                'author' => $r->hiddenID,
                'additional' => $r->cachepaymentType,
                'reg_no' => ($data['logcount'] + 1),
                'aksi' => 'membership',
                't_membership' => $r->hiddenDataPrice
            ]);

        }else{
            $data['price_source'] = $data['data']->rest_data / $data['data']->rest_duration;

            //if(($data['data']->rest_duration - $r->hiddenDataDuration) < 1){
                //$data['paymentStatus'] = "Lunas";
            //}else{
                //$data['paymentStatus'] = "Dalam Cicilan";
            //}

            if((int) ($data['data']->rest_data - ($data['price_source'] * $r->hiddenDataDuration)) <= 0){


                $data['result'] = CicilanDataModel::where('author', $r->hiddenID)->delete();
                $data['paymentStatus'] = "Lunas";
            }else{
                $data['paymentStatus'] = "Dalam Cicilan";
            }

            if($data['data']->rest_duration - $r->hiddenDataDuration > 1){
                $data['result'] = CicilanDataModel::where('author', $r->hiddenID)->update([
                    'rest_duration' => ($data['data']->rest_duration - $r->hiddenDataDuration),
                    'rest_data' => (int) ($data['data']->rest_data - ($data['price_source'] * $r->hiddenDataDuration)),
                    'updated_at' => $date_now
                ]);
            }else{
                $data['result'] = CicilanDataModel::where('author', $r->hiddenID)->update([
                    'rest_data' => (int) ($data['data']->rest_data - ($data['price_source'] * $r->hiddenDataDuration)),
                    'updated_at' => $date_now
                ]);
            }

            $data['log'] = MemberLogModel::create([
                'date' => $date_now,
                'desc' => 'Pembayaran Cicilan - ' . $data['data']->rest_membership,
                'category' => 5,
                'transaction' => (int) ($data['price_source'] * $r->hiddenDataDuration),
                'status' => $data['paymentStatus'],
                'author' => $r->hiddenID,
                'additional' => $r->cachepaymentType,
                'reg_no' => ($data['logcount'] + 1),
                'aksi' => 'membership',
                't_membership' => (int) ($data['data']->rest_data - ($data['price_source'] * $r->hiddenDataDuration))
            ]);
        }





        return redirect()->route('suadmin.member.cicilan.index')->with(['payment_success' => $data['log']->log_id]);
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
