<?php

namespace App\Http\Controllers\Marketing;

use App\Model\gstatus\GlobalStatusModel;
use App\Model\marketing\MarketingModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MarketingDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Data Marketing';
            $jMarketing = MarketingModel::count();
            $marketingActive = MarketingModel::count();
            $marketingLK = MarketingModel::where('gender', '=', 'Laki-laki')->count();
            $marketingPR = MarketingModel::where('gender', '=', 'Perempuan')->count();
            $username = Auth::user()->name;
            $url = "marketing.store";
            $status = GlobalStatusModel::from('global_status')->get();
            $app_layout = $this->defineLayout($role);

            return view('marketing.index',
                compact('title','username','role','app_layout','url','status','jMarketing','marketingActive','marketingLK','marketingPR'));
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

    public function getMarketingData(Request $request){
        if($request->ajax()){
            $data = MarketingModel::from("marketingdata as PK")
                ->join("global_status as gStatus", "gStatus.gstatus_id", "=", "PK.status")
                ->select(
                    'PK.mark_id',
                    'PK.name',
                    'PK.gender',
                    'PK.join_from',
                    'PK.status as status',
                    'gStatus.status as markStatus'
                )
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return '<span class="text-left">'.$data->name.'</span>';
                })
                ->addColumn('gender', function ($data) {
                    return '<div class="text-left">'.$data->gender.'</div>';
                })
                ->addColumn('join_from', function ($data) {
                    return '<div class="text-left">'.date('d/m/Y', strtotime($data->join_from)).'</div>';
                })
                ->addColumn('markStatus', function ($data) {
                    if($data->status == 1){
                        return '<div class="text-center font-weight-bold text-success">'.$data->markStatus.'</div>';
                    }else{
                        return '<div class="text-center font-weight-bold text-danger">'.$data->markStatus.'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<center>
                            <a href="#modal-marketing" class="btn btn-default btn-sm" title="Ubah" data-toggle="modal" onclick="editDataOf('.$data->mark_id.'); marketingEditMode();">
                                <i class="fa fa-edit text-warning"></i>
                            </a>
                        </center>';
                })
                ->rawColumns(['action', 'name', 'gender', 'join_from', 'markStatus'])
                ->make(true);
        }
    }

    public function store(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = MarketingModel::create([
            'name' => $r->marketingName,
            'gender' => $r->marketingGender,
            'join_from' => $date_now,
            'status' => 2,
            'created_by' => Auth::user()->role_id,
            'created_at' => $date_now
        ]);

        return redirect()->route('suadmin.marketing.index')->with(['success' => 'Marketing Berhasil Ditambahkan']);
    }

    public function edit(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $data['data'] = MarketingModel::from("marketingdata as pk")
            ->select(
                "pk.mark_id",
                "pk.name",
                "pk.gender",
                "pk.status"
            )->where('mark_id', $r->mark_id)->first();
        $data['url'] = route('marketing.update');
        $data['delete_button'] = '
                    <button type="button" class="btn btn-danger ml-3" id="deleteMarketing" style="position: absolute; left: 0;" onclick="destroy('.$r->mark_id.')" title="Hapus"><i class="fas fa-trash fa-sm"></i></button>';

        return $data;
    }

    public function update(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = MarketingModel::where('mark_id', $r->hiddenID)->update([
            'name' => $r->marketingName,
            'gender' => $r->marketingGender,
            'status' => $r->marketingStatus,
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->role_id
        ]);
        return redirect()->route('suadmin.marketing.index')->with(['success' => 'Marketing Berhasil Diubah']);
    }

    public function destroy(Request $r){
        $exec = MarketingModel::destroy($r->mark_id);

        if($exec){
            redirect()->route('suadmin.marketing.index')->with(['success' => 'Marketing Berhasil Dihapus']);
        }else{
            redirect()->route('suadmin.marketing.index')->with(['error' => 'Marketing Gagal Dihapus']);
        }
    }
}
