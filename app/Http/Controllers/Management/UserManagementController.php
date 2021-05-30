<?php

namespace App\Http\Controllers\Management;

use App\Model\users\UserModel;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\ValidateRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $title = 'User Management';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $totalAdmin = UserModel::where("role_id", 1)->count();
            $totalCS = UserModel::where("role_id", 3)->count();

            return view('administrator.management.user_management',
            compact('title','username','app_layout','role',
                    'totalAdmin','totalCS'));
        }
    }

    public function checkIsUserAvailable(Request $r){
        $data = UserModel::where("email", $r->email)->count();

        return $data;
    }

    public function userManagementData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        if($r->ajax()) {
            $data = UserModel::from("users as USER")
                    ->select(
                        'USER.id as id',
                        'USER.name as name',
                        'USER.email as email',
                        'USER.role_id as type',
                        'USER.status as status'
                    )->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($data) {
                    $initial = explode(" ", $data->name);
                    if(count($initial) > 1){
                        $render_initial = '<div data-initials="'.substr($initial[0], 0, 1).substr($initial[1], 0, 1).'"></div>';
                    }else{
                        $render_initial = '<div data-initials="'.substr($initial[0], 0, 1).'"></div>';
                    }

                    return '<div class="row">
                                <div class="col-auto">
                                    '.$render_initial.'
                                </div>
                                <div class="col">
                                    <b>'.$data->name.'</b>
                                    <p class="color-dark mb-0">'.$data->email.'</p>
                                </div>
                            </div>';
                })
                ->addColumn('type', function ($data) {
                    return '<div class="text-left">'.$this->RenderUserRole($data->type).'</div>';
                })
                ->addColumn('status', function ($data) {
                    if($data->status == "Active"){
                        return '<div class="align-middle text-left">
                                    <span class="logged-in mr-1">●</span>
                                    Active
                                </div>';
                    }else{
                        return '<div class="align-middle text-left">
                                    <span class="logged-out mr-1">●</span>
                                    Inactive
                                </div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    return '<div class="align-middle pl-0 pr-0">
                                <button class="btn edit-user-group btn-outline-warning" onclick="editUser('.$data->id.')">
                                    <i class="fas fa-pencil-alt fa-sm"></i>
                                </button>
                                <button class="btn delete-user-group ml-2 btn-danger" onclick="deleteUser('.$data->id.', `'.$data->email.'`)" style="display: none;">
                                    <i class="fas fa-trash fa-sm"></i>
                                </button>
                            </div>';
                })
                ->rawColumns(['user','type','status','action'])
                ->make(true);
        }
    }

    public function getUserData(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        if($r->ajax()){
            $data['data'] = UserModel::from("users as USER")
                ->select(
                    'USER.id as id',
                    'USER.name as name',
                    'USER.email as email',
                    'USER.role_id as type',
                    'USER.status as status',
                    'USER.created_at as date_created'
                )->where("id", $r->id)->first();

            $data['data']->date_created = date('d M Y', strtotime($data['data']->date_created));

            return $data;
        }
    }

    public function RenderUserRole($role){
        switch ($role){
            case 1:
                return 'Administrator';
                break;
            case 3:
                return 'Customer Service';
                break;
            default:
                return 'Unknown';
        }
    }

    public function addUser(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        $data = UserModel::create([
            'name' => $r->dataUserName,
            'email' => $r->dataUserEmail,
            'password' => Hash::make($r->dataUserPass),
            'role_id' => $r->dataUserRole,
            'status' => "Inactive",
            'created_at' => $date_now
        ]);

        return redirect()->route('suadmin.management.index')->with(['success' => 'User Berhasil Ditambahkan']);
    }

    public function editUser(Request $r){
        date_default_timezone_set("Asia/Jakarta");
        $date_now = Carbon::now();

        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        if(isset($dataUserPass)){
            $data = UserModel::where('id', $r->dataUserID)->update([
                'name' => $r->dataUserName,
                'password' => Hash::make($r->dataUserPass),
                'role_id' => $r->dataUserRole,
                'status' => $r->dataUserStatus,
                'updated_at' => $date_now
            ]);
        }else{
            $data = UserModel::where('id', $r->dataUserID)->update([
                'name' => $r->dataUserName,
                'role_id' => $r->dataUserRole,
                'status' => $r->dataUserStatus,
                'updated_at' => $date_now
            ]);
        }

        return redirect()->route('suadmin.management.index')->with(['success' => 'User Berhasil Diubah']);
    }

    public function deleteUser(Request $r){
        $validateRole = new ValidateRole;
        $validateRole->checkAuthADM();

        $exec = UserModel::destroy($r->dataUserID);

        if($exec){
            return redirect()->route('suadmin.management.index')->with(['success' => 'User Berhasil Dihapus']);
        }else{
            return redirect()->route('suadmin.management.index')->with(['error' => 'User Gagal Dihapus']);
        }
    }
}
