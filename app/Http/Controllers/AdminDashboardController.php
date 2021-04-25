<?php

namespace App\Http\Controllers;

use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('sudata');

        $title = 'Dashboard';
        $username = Auth::user()->name;
        $role = Auth::user()->role_id;
        $jMember = MemberModel::count();
        $memberActive = MemberModel::where('status', 1)->count();
        $memberCuti = MemberModel::where('status', 3)->count();
        $totalSales = $this->asRupiah(MemberLogModel::sum('transaction'));

        return view('admin_dashboard', compact('title','username','role','jMember','memberActive','memberCuti','totalSales'));
    }

    function asRupiah($value) {
        if ($value<0) return "-".asRupiah(-$value);
        return 'Rp. ' . number_format($value, 0);
    }
}
