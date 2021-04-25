<?php

namespace App\Http\Controllers\PT;

use App\Model\gstatus\GlobalStatusModel;
use App\Model\pt\PersonalTrainerModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PTDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $role = $this->checkAuth();
        if(isset($role)){
            $title = 'Data Personal Trainer';
            $jPT = PersonalTrainerModel::count();
            $PTActive = PersonalTrainerModel::where('status', 1 )->count();
            $PTLK = PersonalTrainerModel::where('gender', '=', 'Laki-laki')->count();
            $PTPR = PersonalTrainerModel::where('gender', '=', 'Perempuan')->count();
            $username = Auth::user()->name;
            $url = "pt.store";
            $status = GlobalStatusModel::get();
            $app_layout = $this->defineLayout($role);

            return view('pt.index',
                compact('title','username','role','app_layout','url','status','jPT','PTActive','PTLK','PTPR'));
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

    public function getPTData(Request $request){
        if($request->ajax()){
            $data = PersonalTrainerModel::from("ptdata as PK")
                ->join("global_status as gStatus", "gStatus.gstatus_id", "=", "PK.status")
                ->select(
                    'PK.pt_id',
                    'PK.name',
                    'PK.gender',
                    'PK.join_from',
                    'PK.status as status',
                    'gStatus.status as ptStatus'
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
                ->addColumn('ptStatus', function ($data) {
                    if($data->status == 1){
                        return '<div class="text-center font-weight-bold text-success">'.$data->ptStatus.'</div>';
                    }else{
                        return '<div class="text-center font-weight-bold text-danger">'.$data->ptStatus.'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<center>
                            <a href="#modal-pt" class="btn btn-default btn-sm" title="Ubah" data-toggle="modal" onclick="editDataOf('.$data->pt_id.'); PTEditMode();">
                                <i class="fa fa-edit text-warning"></i>
                            </a>
                        </center>';
                })
                ->rawColumns(['action', 'name', 'gender', 'join_from', 'ptStatus'])
                ->make(true);
        }
    }

    public function store(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = PersonalTrainerModel::create([
            'name' => $r->PTName,
            'gender' => $r->PTGender,
            'join_from' => $date_now,
            'status' => 2,
            'created_by' => Auth::user()->role_id,
            'created_at' => $date_now
        ]);

        return redirect()->route('suadmin.pt.index')->with(['success' => 'Data Personal Trainer Berhasil Ditambahkan']);
    }

    public function edit(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $data['data'] = PersonalTrainerModel::from("ptdata as PK")
            ->select(
                "PK.pt_id",
                "PK.name",
                "PK.gender",
                "PK.status"
            )->where('pt_id', $r->uid)->first();
        $data['url'] = route('pt.update');
        $data['delete_button'] = '
                    <button type="button" class="btn btn-danger ml-3" id="deletePT" style="position: absolute; left: 0;" onclick="destroy('.$r->uid.')" title="Hapus"><i class="fas fa-trash fa-sm"></i></button>';
        return $data;
    }

    public function update(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = PersonalTrainerModel::where('pt_id', $r->hiddenID)->update([
            'name' => $r->PTName,
            'gender' => $r->PTGender,
            'status' => $r->PTStatus,
            'updated_at' => $date_now,
            'updated_by' => Auth::user()->role_id
        ]);
        return redirect()->route('suadmin.pt.index')->with(['success' => 'Data Personal Trainer Berhasil Diubah']);
    }

    public function destroy(Request $r){
        $exec = PersonalTrainerModel::destroy($r->pt_id);

        if($exec){
            redirect()->route('suadmin.pt.index')->with(['success' => 'Data Pif(autersonal Trainer Berhasil Dihapus']);
        }else{
            redirect()->route('suadmin.pt.index')->with(['error' => 'Data Personal Trainer Gagal Dihapus']);
        }
    }
}
