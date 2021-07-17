<?php

namespace App\Http\Controllers\report;

use App\Http\Controllers\Auth\ValidateRole;
use App\Model\marketing\MarketingModel;
use App\Model\member\CutiMemberModel;
use App\Model\member\MemberCacheModel;
use App\Model\member\MemberCheckinModel;
use App\Model\member\MemberLogModel;
use App\Model\member\MemberModel;
use App\Model\membership\membershipListCacheModel;
use App\Model\pt\LogPTModel;
use App\Model\pt\PersonalTrainerModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReportGraphController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $title = 'Laporan';
        $username = Auth::user()->name;
        $app_layout = $validateRole->defineLayout($role);

        if(isset($role)){
            $filter_year_available = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->get();
            $filter_marketing = MarketingModel::select('mark_id','name')->get();
            $filter_pt = PersonalTrainerModel::select('pt_id','name')->get();

            $totalQuery = count($filter_year_available);
            $arrayValidate = [];

            for($i=0; $i<$totalQuery; $i++) {
                if (in_array($filter_year_available[$i]->date, $arrayValidate)) {
                    $filter_year_available->forget($i);
                } else {
                    array_push($arrayValidate, $filter_year_available[$i]->date);
                }
            }

            return view('report.index', compact('title','username','role','app_layout','filter_year_available','filter_marketing','filter_pt'));
        }
    }

    public function performaMember(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberModel::whereDay('created_at', $i)->count();
                            $result_daily_lk = MemberModel::where('gender', "Laki-laki")->whereDay('created_at', $i)->count();
                            $result_daily_pr = MemberModel::where('gender', "Perempuan")->whereDay('created_at', $i)->count();
                        }else{
                            $result_daily = MemberModel::whereDay('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                            $result_daily_lk = MemberModel::where('gender', "Laki-laki")->whereDay('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                            $result_daily_pr = MemberModel::where('gender', "Perempuan")->whereDay('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberModel::whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->count();
                            $result_daily_lk = MemberModel::where('gender', "Laki-laki")->whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->count();
                            $result_daily_pr = MemberModel::where('gender', "Perempuan")->whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->count();
                        }else{
                            $result_daily = MemberModel::whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                            $result_daily_lk = MemberModel::where('gender', "Laki-laki")->whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                            $result_daily_pr = MemberModel::where('gender', "Perempuan")->whereDay('created_at', $i)->whereMonth('created_at', (int)$r->FILTER_MONTH)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_daily);
                    array_push($data['chart_dataset_1'], $result_daily_lk);
                    array_push($data['chart_dataset_2'], $result_daily_pr);
                }

                return $data;
                break;
            case "monthly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        $result_monthly = MemberModel::whereMonth('created_at', $i)->count();
                        $result_monthly_lk = MemberModel::where('gender', "Laki-laki")->whereMonth('created_at', $i)->count();
                        $result_monthly_pr = MemberModel::where('gender', "Perempuan")->whereMonth('created_at', $i)->count();
                    }else{
                        $result_monthly = MemberModel::whereMonth('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                        $result_monthly_lk = MemberModel::where('gender', "Laki-laki")->whereMonth('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                        $result_monthly_pr = MemberModel::where('gender', "Perempuan")->whereMonth('created_at', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->count();
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $result_monthly);
                    array_push($data['chart_dataset_1'], $result_monthly_lk);
                    array_push($data['chart_dataset_2'], $result_monthly_pr);
                }


                return $data;
                break;
            case "yearly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    $result_yearly = MemberModel::whereYear('created_at', $i)->count();
                    $result_yearly_lk = MemberModel::where('gender', "Laki-laki")->whereYear('created_at', $i)->count();
                    $result_yearly_pr = MemberModel::where('gender', "Perempuan")->whereYear('created_at', $i)->count();

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_yearly);
                    array_push($data['chart_dataset_1'], $result_yearly_lk);
                    array_push($data['chart_dataset_2'], $result_yearly_pr);
                }
                break;
        }

        return $data;
    }

    public function performaCuti(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberLogModel::whereDay('date', $i)->where('aksi', 'cuti')->count();
                        }else{
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->where('aksi', 'cuti')->count();
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->where('aksi', 'cuti')->count();
                        }else{
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->where('aksi', 'cuti')->count();
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_daily);
                }

                return $data;
                break;
            case "monthly":
                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        $result_monthly = MemberLogModel::whereMonth('date', $i)->where('aksi', 'cuti')->count();
                    }else{
                        $result_monthly = MemberLogModel::whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->where('aksi', 'cuti')->count();
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $result_monthly);
                }


                return $data;
                break;
            case "yearly":
                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    $result_yearly = MemberLogModel::whereYear('date', $i)->where('aksi', 'cuti')->count();

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_yearly);
                }
                break;
        }

        return $data;
    }



    public function performaRevenue(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberLogModel::whereDay('date', $i)->sum('transaction');
                            $result_daily_mship = MemberLogModel::where('t_membership', "!=", null)->whereDay('date', $i)->sum('t_membership');
                            $result_daily_pt = MemberLogModel::where('t_sesi', "!=", null)->whereDay('date', $i)->sum('transaction');
                        }else{
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereYear('created_at', (int)$r->FILTER_YEAR)->sum('transaction');
                            $result_daily_mship = MemberLogModel::where('t_membership', "!=", null)->whereDay('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_membership');
                            $result_daily_pt = MemberLogModel::where('t_sesi', "!=", null)->whereDay('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_sesi');
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->sum('transaction');
                            $result_daily_mship = MemberLogModel::where('t_membership', "!=", null)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->sum('t_membership');
                            $result_daily_pt = MemberLogModel::where('t_sesi', "!=", null)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->sum('t_sesi');
                        }else{
                            $result_daily = MemberLogModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->sum('transaction');
                            $result_daily_mship = MemberLogModel::where('t_membership', "!=", null)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_membership');
                            $result_daily_pt = MemberLogModel::where('t_sesi', "!=", null)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_sesi');
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_daily);
                    array_push($data['chart_dataset_1'], $result_daily_mship);
                    array_push($data['chart_dataset_2'], $result_daily_pt);
                }

                return $data;
                break;
            case "monthly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        $result_monthly = MemberLogModel::whereMonth('date', $i)->sum('transaction');
                        $result_monthly_mship = MemberLogModel::where('t_membership', "!=", null)->whereMonth('date', $i)->sum('t_membership');
                        $result_monthly_pt = MemberLogModel::where('t_sesi', "!=", null)->whereMonth('date', $i)->sum('t_sesi');
                    }else{
                        $result_monthly = MemberLogModel::whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->sum('transaction');
                        $result_monthly_mship = MemberLogModel::where('t_membership', "!=", null)->whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_membership');
                        $result_monthly_pt = MemberLogModel::where('t_sesi', "!=", null)->whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->sum('t_sesi');
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $result_monthly);
                    array_push($data['chart_dataset_1'], $result_monthly_mship);
                    array_push($data['chart_dataset_2'], $result_monthly_pt);
                }


                return $data;
                break;
            case "yearly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    $result_yearly = MemberLogModel::whereYear('date', $i)->sum('transaction');
                    $result_yearly_mship = MemberLogModel::where('t_membership', "!=", null)->whereYear('date', $i)->sum('t_membership');
                    $result_yearly_pt = MemberLogModel::where('t_sesi', "!=", null)->whereYear('date', $i)->sum('t_sesi');

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_yearly);
                    array_push($data['chart_dataset_1'], $result_yearly_mship);
                    array_push($data['chart_dataset_2'], $result_yearly_pt);
                }
                break;
        }

        return $data;
    }

    public function performaMarketing(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            if(isset($r->FILTER_MARKETING)){
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                    ->whereDay('LOG.date', $i)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }else{
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "!=", null)
                                    ->whereDay('LOG.date', $i)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }
                        }else{
                            if(isset($r->FILTER_MARKETING)){
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                    ->whereDay('LOG.date', $i)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }else{
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "!=", null)
                                    ->whereDay('LOG.date', $i)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            if(isset($r->FILTER_MARKETING)){
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }else{
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "!=", null)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }
                        }else{
                            if(isset($r->FILTER_MARKETING)){
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }else{
                                $result_daily = MemberLogModel::from("logmember as LOG")
                                    ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                    ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                    ->where('cache.id_marketing', "!=", null)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->orderBy('LOG.date','asc')
                                    ->get();
                            }
                        }
                    }

                    $totalQuery = count($result_daily);
                    $arrayValidate = [];
                    $transaction = 0;

                    for($k=0; $k<$totalQuery; $k++){
                        if (in_array($result_daily[$k]->author, $arrayValidate)) {
                            $result_daily->forget($k);
                        }else{
                            array_push($arrayValidate, $result_daily[$k]->author);
                            $transaction += $result_daily[$k]->transaction;
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $transaction);
                }

                return $data;
                break;
            case "monthly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        if(isset($r->FILTER_MARKETING)){
                            $result_monthly = MemberLogModel::from("logmember as LOG")
                                ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                ->whereMonth('LOG.date', $i)
                                ->orderBy('LOG.date','asc')
                                ->get();
                        }else{
                            $result_monthly = MemberLogModel::from("logmember as LOG")
                                ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                ->where('cache.id_marketing', "!=", null)
                                ->whereMonth('LOG.date', $i)
                                ->orderBy('LOG.date','asc')
                                ->get();
                        }
                    }else{
                        if(isset($r->FILTER_MARKETING)){
                            $result_monthly = MemberLogModel::from("logmember as LOG")
                                ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                                ->whereMonth('LOG.date', $i)
                                ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                ->orderBy('LOG.date','asc')
                                ->get();
                        }else{
                            $result_monthly = MemberLogModel::from("logmember as LOG")
                                ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                                ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                                ->where('cache.id_marketing', "!=", null)
                                ->whereMonth('LOG.date', $i)
                                ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                ->orderBy('LOG.date','asc')
                                ->get();
                        }
                    }

                    $totalQuery = count($result_monthly);
                    $arrayValidate = [];
                    $transaction = 0;

                    for($k=0; $k<$totalQuery; $k++){
                        if (in_array($result_monthly[$k]->author, $arrayValidate)) {
                            $result_monthly->forget($k);
                        }else{
                            array_push($arrayValidate, $result_monthly[$k]->author);
                            $transaction += $result_monthly[$k]->transaction;
                        }
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $transaction);
                }


                return $data;
                break;
            case "yearly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    if(isset($r->FILTER_MARKETING)){
                        $result_yearly = MemberLogModel::from("logmember as LOG")
                            ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                            ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                            ->where('cache.id_marketing', "=", $r->FILTER_MARKETING)
                            ->whereYear('LOG.date', $i)
                            ->orderBy('LOG.date','asc')
                            ->get();
                    }else{
                        $result_yearly = MemberLogModel::from("logmember as LOG")
                            ->leftJoin("cache_read as cache", "cache.author", "=", "LOG.author")
                            ->select('LOG.transaction as transaction','LOG.date as date','LOG.author as author','cache.id_marketing as id_marketing')
                            ->where('cache.id_marketing', "!=", null)
                            ->whereYear('LOG.date', $i)
                            ->orderBy('LOG.date','asc')
                            ->get();
                    }

                    $totalQuery = count($result_yearly);
                    $arrayValidate = [];
                    $transaction = 0;

                    for($k=0; $k<$totalQuery; $k++){
                        if (in_array($result_yearly[$k]->author, $arrayValidate)) {
                            $result_yearly->forget($k);
                        }else{
                            array_push($arrayValidate, $result_yearly[$k]->author);
                            $transaction += $result_yearly[$k]->transaction;
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $transaction);
                }
                break;
        }

        return $data;
    }

    public function performaPT(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            if(isset($r->FILTER_PT)){
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                    ->whereDay('LOG.date', $i)
                                    ->count();
                            }else{
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->whereDay('LOG.date', $i)
                                    ->count();
                            }
                        }else{
                            if(isset($r->FILTER_PT)){
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                    ->whereDay('LOG.date', $i)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->count();
                            }else{
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->whereDay('LOG.date', $i)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->count();
                            }
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            if(isset($r->FILTER_PT)){
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->count();
                            }else{
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->count();
                            }
                        }else{
                            if(isset($r->FILTER_PT)){
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->count();
                            }else{
                                $result_daily = LogPTModel::from("log_pt as LOG")
                                    ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                    ->whereDay('LOG.date', $i)
                                    ->whereMonth('LOG.date', (int)$r->FILTER_MONTH)
                                    ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                    ->count();
                            }
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_daily);
                }

                return $data;
                break;
            case "monthly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        if(isset($r->FILTER_PT)){
                            $result_monthly = LogPTModel::from("log_pt as LOG")
                                ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                ->whereMonth('LOG.date', $i)
                                ->count();
                        }else{
                            $result_monthly = LogPTModel::from("log_pt as LOG")
                                ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                ->whereMonth('LOG.date', $i)
                                ->count();
                        }
                    }else{
                        if(isset($r->FILTER_PT)){
                            $result_monthly = LogPTModel::from("log_pt as LOG")
                                ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                ->where('LOG.pt_author', "=", $r->FILTER_PT)
                                ->whereMonth('LOG.date', $i)
                                ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                ->count();
                        }else{
                            $result_monthly = LogPTModel::from("log_pt as LOG")
                                ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                                ->whereMonth('LOG.date', $i)
                                ->whereYear('LOG.date', (int)$r->FILTER_YEAR)
                                ->count();
                        }
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $result_monthly);
                }


                return $data;
                break;
            case "yearly":
                $data['chart_dataset_1'] = [];
                $data['chart_dataset_2'] = [];

                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    if(isset($r->FILTER_PT)){
                        $result_yearly = LogPTModel::from("log_pt as LOG")
                            ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                            ->where('LOG.pt_author', "=", $r->FILTER_PT)
                            ->whereYear('LOG.date', $i)
                            ->count();
                    }else{
                        $result_yearly = LogPTModel::from("log_pt as LOG")
                            ->leftJoin("ptdata as PT", "PT.pt_id", "=", "LOG.pt_author")
                            ->whereYear('LOG.date', $i)
                            ->count();
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_yearly);
                }
                break;
        }

        return $data;
    }

    public function performaCheckin(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthADM();

        $data['chart_label'] = [];
        $data['chart_dataset'] = [];
        $total_loop = 0;

        switch($r->FILTER_TYPE){
            case "daily":
                $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){
                    if($r->FILTER_MONTH == "all"){
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberCheckinModel::whereDay('date', $i)->count();
                        }else{
                            $result_daily = MemberCheckinModel::whereDay('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->count();
                        }
                    }else{
                        if($r->FILTER_YEAR == "all"){
                            $result_daily = MemberCheckinModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->count();
                        }else{
                            $result_daily = MemberCheckinModel::whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->count();
                        }
                    }

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_daily);
                }

                return $data;
                break;
            case "monthly":
                $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                for($i=1; $i <= $total_loop; $i++){

                    if($r->FILTER_YEAR == "all"){
                        $result_monthly = MemberCheckinModel::whereMonth('date', $i)->count();
                    }else{
                        $result_monthly = MemberCheckinModel::whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->count();
                    }

                    array_push($data['chart_label'], $this->getMonthByIndex($i));
                    array_push($data['chart_dataset'], $result_monthly);
                }


                return $data;
                break;
            case "yearly":
                $total_loop = $r->FILTER_YEAR_DURATION;

                if($r->FILTER_YEAR == "all"){
                    $yQuery = MemberLogModel::selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                    $year_start = (int) $yQuery->date;
                }else{
                    $year_start = (int) $r->FILTER_YEAR;
                }

                for($i=$year_start; $i < $year_start + $total_loop; $i++){
                    $result_yearly = MemberCheckinModel::whereYear('date', $i)->count();

                    array_push($data['chart_label'], $i);
                    array_push($data['chart_dataset'], $result_yearly);
                }
                break;
        }

        return $data;
    }

    public function getSpecifyMemberSpending(Request $r){
        $validateRole = new ValidateRole;
        $role = $validateRole->checkAuthALL();

        if(isset($r->member_id)){
            $data['chart_label'] = [];
            $data['chart_dataset'] = [];
            $total_loop = 0;

            switch($r->FILTER_TYPE){
                case "daily":
                    $data['chart_dataset_membership'] = [];
                    $data['chart_dataset_pt'] = [];

                    $total_loop = $this->defineTotalLoop("daily", $r->FILTER_MONTH, $r->FILTER_YEAR);

                    for($i=1; $i <= $total_loop; $i++){
                        if($r->FILTER_MONTH == "all"){
                            if($r->FILTER_YEAR == "all"){
                                $result_daily = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->sum('transaction');
                                $result_daily_membership = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->where('t_membership', "!=", null)->sum('transaction');
                                $result_daily_pt = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->where('t_sesi', "!=", null)->sum('transaction');
                            }else{
                                $result_daily = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereYear('date', (int) $r->FILTER_YEAR)->sum('transaction');
                                $result_daily_membership = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereYear('date', (int) $r->FILTER_YEAR)->where('t_membership', "!=", null)->sum('transaction');
                                $result_daily_pt = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->where('t_sesi', "!=", null)->sum('transaction');
                            }
                        }else{
                            if($r->FILTER_YEAR == "all"){
                                $result_daily = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->sum('transaction');
                                $result_daily_membership = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->where('t_membership', "!=", null)->sum('transaction');
                                $result_daily_pt = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->where('t_sesi', "!=", null)->sum('transaction');
                            }else{
                                $result_daily = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int) $r->FILTER_YEAR)->sum('transaction');
                                $result_daily_membership = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int) $r->FILTER_YEAR)->where('t_membership', "!=", null)->sum('transaction');
                                $result_daily_pt = MemberLogModel::where('author', $r->member_id)->whereDay('date', $i)->whereMonth('date', (int)$r->FILTER_MONTH)->whereYear('date', (int)$r->FILTER_YEAR)->where('t_sesi', "!=", null)->sum('transaction');
                            }
                        }

                        array_push($data['chart_label'], $i);
                        array_push($data['chart_dataset'], $result_daily);
                        array_push($data['chart_dataset_membership'], $result_daily_membership);
                        array_push($data['chart_dataset_pt'], $result_daily_pt);
                    }

                    return $data;
                    break;
                case "monthly":
                    $data['chart_dataset_membership'] = [];
                    $data['chart_dataset_pt'] = [];

                    $total_loop = $this->defineTotalLoop("monthly", null, $r->FILTER_YEAR);

                    for($i=1; $i <= $total_loop; $i++){

                        if($r->FILTER_YEAR == "all"){
                            $result_monthly = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->sum('transaction');
                            $result_monthly_membership = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->where('t_membership', "!=", null)->sum('transaction');
                            $result_monthly_pt = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->where('t_sesi', "!=", null)->sum('transaction');
                        }else{
                            $result_monthly = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->whereYear('date', (int) $r->FILTER_YEAR)->sum('transaction');
                            $result_monthly_membership = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->whereYear('date', (int) $r->FILTER_YEAR)->where('t_membership', "!=", null)->sum('transaction');
                            $result_monthly_pt = MemberLogModel::where('author', $r->member_id)->whereMonth('date', $i)->whereYear('date', (int)$r->FILTER_YEAR)->where('t_sesi', "!=", null)->sum('transaction');
                        }

                        array_push($data['chart_label'], $this->getMonthByIndex($i));
                        array_push($data['chart_dataset'], $result_monthly);
                        array_push($data['chart_dataset_membership'], $result_monthly_membership);
                        array_push($data['chart_dataset_pt'], $result_monthly_pt);
                    }

                    return $data;
                    break;
                case "yearly":
                    $data['chart_dataset_membership'] = [];
                    $data['chart_dataset_pt'] = [];

                    $total_loop = $r->FILTER_YEAR_DURATION;

                    if($r->FILTER_YEAR == "all"){
                        $yQuery = MemberLogModel::where('author',$r->member_id)->selectRaw('to_char(date, \'yyyy\') as date')->orderBy('date', 'asc')->first();
                        $year_start = (int) $yQuery->date;
                    }else{
                        $year_start = (int) $r->FILTER_YEAR;
                    }

                    for($i=$year_start; $i < $year_start + $total_loop; $i++){
                        $result_yearly = MemberLogModel::where('author', $r->member_id)->whereYear('date', $i)->sum('transaction');
                        $result_yearly_membership = MemberLogModel::where('author', $r->member_id)->whereYear('date', $i)->where('t_membership', "!=", null)->sum('transaction');
                        $result_yearly_pt = MemberLogModel::where('author', $r->member_id)->whereYear('date', $i)->where('t_sesi', "!=", null)->sum('transaction');

                        array_push($data['chart_label'], $i);
                        array_push($data['chart_dataset'], $result_yearly);
                        array_push($data['chart_dataset_membership'], $result_yearly_membership);
                        array_push($data['chart_dataset_pt'], $result_yearly_pt);
                    }
                    break;
            }

        }else{
            return null;
        }

        return $data;
    }

    public function defineTotalLoop($filter, $index_month, $index_year){
        switch($filter){
            case "daily":
                if($index_month == Carbon::now()->month && $index_year == Carbon::now()->year){
                    return Carbon::now()->day;
                }else{
                    return Carbon::now()->daysInMonth;
                }
                break;
            case "monthly":
                if($index_year == Carbon::now()->year){
                    return Carbon::now()->month;
                }else{
                    return 12;
                }
                break;
            case "yearly":

                break;
        }
    }

    public function getMonthByIndex($index){
        switch ($index){
            case 1:
                return "Jan";
                break;
            case 2:
                return "Feb";
                break;
            case 3:
                return "Mar";
                break;
            case 4:
                return "Apr";
                break;
            case 5:
                return "May";
                break;
            case 6:
                return "Jun";
                break;
            case 7:
                return "Jul";
                break;
            case 8:
                return "Aug";
                break;
            case 9:
                return "Sep";
                break;
            case 10:
                return "Oct";
                break;
            case 11:
                return "Nov";
                break;
            case 12:
                return "Dec";
                break;
        }
    }
}
