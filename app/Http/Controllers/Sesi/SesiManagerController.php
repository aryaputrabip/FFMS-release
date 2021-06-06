<?php

namespace App\Http\Controllers\Sesi;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\gstatus\GlobalStatusModel;
use App\Model\session\SessionModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class SesiManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function manager()
    {
        $validateRole = new ValidateRole();
        $role = $validateRole->checkAuthADM();

        $title = 'Paket Personal Trainer';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $jSesi = SessionModel::count();
            $sesiAktif = SessionModel::where("status", 1)->count();
            $sesiStatus = GlobalStatusModel::get();

            $url = Route('sesi.createSesi');

            return view('sesi.index',
                compact('title','username','app_layout','role', 'jSesi', 'sesiAktif', 'sesiStatus', 'url'));
        }
    }

    public function getSesiData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        if($r->ajax()) {
            $data = SessionModel::from("sessiondata as SESI")
                ->join('global_status as STATUS', 'STATUS.gstatus_id', "=", "SESI.status")
                ->select(
                    'SESI.id as id',
                    'SESI.title as title',
                    'SESI.duration as duration',
                    'SESI.price as price',
                    'SESI.status as statusID',
                    'STATUS.status as status'
                )->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    if(isset($data->title)){
                        return '<div class="text-left">'.$data->title.'</div>';
                    }else{
                        return '<div class="text-left"> - </div>';
                    }
                })
                ->addColumn('duration', function ($data) {
                    return '<div class="text-left">'.$data->duration.' Sesi</div>';
                })
                ->addColumn('price', function ($data) {
                    return '<div class="text-left">'.$this->asRupiah($data->price).'</div>';
                })
                ->addColumn('status', function ($data) {
                    if($data->statusID == 1){
                        return '<div class="text-center font-weight-bold text-success">'.$data->status.'</div>';
                    }else{
                        return '<div class="text-center font-weight-bold text-danger">'.$data->status.'</div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<center>
                            <a href="#modal-sesi" class="btn btn-default btn-sm" title="Ubah" data-toggle="modal" onclick="editSesi('.$data->id.'); editDataOf('.$data->id.')">
                                <i class="fa fa-edit text-warning"></i>
                            </a>
                        </center>';
                })
                ->rawColumns(['title','duration','price','status', 'action'])
                ->make(true);
        }
    }

    function createSesi(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = SessionModel::create([
           'title' => $r->SesiTitle,
           'price' => $r->SesiPrice,
           'duration' =>$r->SesiDuration,
           'created_at' => $date_now,
           'status' => 2
        ]);

        return redirect()->route('suadmin.sesi.manager')->with(['success' => 'Paket PT Berhasil Ditambahkan']);
    }

    function edit(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        $data['data'] = SessionModel::where('id', $r->id)->first();

        $data['url'] = route('sesi.update');

        $data['delete_button'] = '
                    <button type="button" class="btn btn-danger ml-3" id="deleteSesi" style="position: absolute; left: 0;" onclick="deleteConfirmation()" title="Hapus"><i class="fas fa-trash fa-sm"></i></button>';
        return $data;
    }

    public function update(Request $r){
        date_default_timezone_set("Asia/Bangkok");
        $date_now = date('Y-m-d H:i:s');

        $data = SessionModel::where('id', $r->hiddenID)->update([
            'title' => $r->SesiTitle,
            'duration' => $r->SesiDuration,
            'price' => $r->SesiPrice,
            'status' => $r->SesiStatus,
            'updated_at' => $date_now
        ]);
        return redirect()->route('suadmin.sesi.manager')->with(['success' => 'Data Paket PT Berhasil Diubah']);
    }

    function deleteSesi(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        $exec = SessionModel::destroy($r->hiddenID);

        if($exec){
            return redirect()->route('suadmin.sesi.manager')->with(['success' => 'Paket PT Berhasil Dihapus']);
        }else{
            return redirect()->route('suadmin.sesi.manager')->with(['error' => 'Paket PT Gagal Dihapus']);
        }
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
