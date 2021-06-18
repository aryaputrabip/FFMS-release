<?php

namespace App\Http\Controllers\Membership;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\gstatus\GlobalStatusModel;
use App\Model\marketing\MarketingModel;
use App\Model\member\MemberCategoryModel;
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
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
