<?php

namespace App\Http\Controllers\Sesi;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberModel;
use App\Model\pt\LogPTModel;
use App\Model\pt\PersonalTrainerModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use function Matrix\add;

class SesiUseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        $title = 'Gunakan Sesi';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $ptdata = PersonalTrainerModel::where('status', 1)->get();

            return view('member.management.sesi_use',
                compact('title','username','app_layout','role','ptdata'));
        }
    }

    public function getMemberData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthALL();

        if($r->ajax()) {
            $data = MemberModel::from("memberdata as MEMBER")
                ->join('cache_read as CACHE', 'CACHE.author', "=", "MEMBER.member_id")
                ->join('ptdata as PT', 'PT.pt_id', "=", "CACHE.id_pt")
                ->select(
                    'MEMBER.member_id as member_id',
                    'MEMBER.name as name',
                    'MEMBER.session as session_left',
                    'CACHE.id_pt as id_pt',
                    'PT.name as pt'
                )
                ->where('MEMBER.session','>=',1)
                ->where('CACHE.id_pt','!=',null)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('member_id', function ($data) {
                    return '<div class="text-left">'.$data->member_id.'</div>';
                })
                ->addColumn('name', function ($data) {
                    return '<div class="text-left">'.$data->name.'</div>';
                })
                ->addColumn('pt', function ($data) {
                    return '<div class="text-left">'.$data->pt.'</div>';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="align-middle">
                                <button class="btn edit-user-group btn-outline-secondary" data-member="'.$data->member_id.'" onclick="selectMember(this)">
                                    <i class="fas fa-chevron-right fa-sm"></i>
                                </button>
                            </div>';
                })
                ->addColumn('action_reverse', function ($data) {
                    return '<div class="align-middle">
                                <button class="btn edit-user-group btn-outline-secondary" data-member="'.$data->member_id.'" onclick="selectMember(this)">
                                    <i class="fas fa-chevron-left fa-sm"></i>
                                </button>
                            </div>';
                })->addColumn('session_use', function ($data) {
                    return '<div class="align-middle">
                                <input type="number" class="sesi_source_input form-group mb-0" data-member="'.$data->member_id.'"
                                min="1" max="'.$data->session_left.'" value="1" style="max-width: 50px;" onchange="addSesi(this)" onkeyup="addSesi(this)">
                            </div>';
                })->addColumn('session', function ($data) {
                    return '<div class="text-left">'.$data->session_left.' Sesi</div>';;
                })
                ->rawColumns(['member_id','name','pt','action', 'action_reverse','session_use','session'])
                ->make(true);
        }
    }

    public function useSesi(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $data = explode(",", $r->listdata);
        $listUser = array();
        $listSesi = array();
        $listSesiLoop = array();
        $log = array();

        for($i=0; $i<count($data); $i+=2){
            array_push($listUser, $data[$i]);
        }

        for($i=1; $i<count($data); $i+=2){
            array_push($listSesi, $data[$i]);
            array_push($listSesiLoop, $data[$i]);
        }
        $memberSession = MemberModel::select('session')->whereIn('member_id', $listUser)->get();

        for($i = 0; $i<count($memberSession); $i++){
            if($memberSession[$i]->session - $listSesi[$i] < 0){
                $listSesi[$i] = 0;
            }else{
                $listSesi[$i] = $memberSession[$i]->session - $listSesi[$i];
            }
        }

        for($i = 0; $i<count($listUser); $i++){
            $member = MemberModel::from("memberdata as MEMBER")
                ->select(
                    'MEMBER.member_id as member_id',
                    'MEMBER.session as session',
                    'MEMBER.updated_at as updated_at'
                )
                ->where('MEMBER.member_id', $listUser[$i])
                ->update([
                    'MEMBER.session' => $listSesi[$i],
                    'MEMBER.updated_at' => $date_now
                ]);

            $pt = MemberCacheModel::select('id_pt')->where('author', $listUser[$i])->first();

            for($j=0;$j<$listSesiLoop[$i];$j++){
                array_push($log, ['pt_author'=>$pt->id_pt, 'date'=> $date_now, 'member'=> $listUser[$i]]);
            }
        }

        $logPT = LogPTModel::insert($log);

        if(Auth::user()->role_id == 1){
            return redirect()->route('suadmin.sesi.index')->with(['success' => 'Sesi Berhasil Diubah']);
        }else{
            return redirect()->route('cs.sesi.index')->with(['success' => 'Sesi Berhasil Diubah']);
        }
    }
}
