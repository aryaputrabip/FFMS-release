<?php

namespace App\Exports;

use App\Model\member\MemberModel;
use App\Model\membership\MembershipModel;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MemberCheckinExport implements FromView
{
    public function view(): View
    {
        $date_now = Carbon::now()->toDateString();

        $checkinQuery = DB::table("public.memberdata as MDATA")
            ->leftJoin("cache_read as CACHE", "CACHE.author", "=", "MDATA.member_id")
            ->leftJoin("membership_memberlist as MSHIP_LIST", "MSHIP_LIST.author", "=", "MDATA.member_id")
            ->leftJoin("membership as MSHIP", "MSHIP.mship_id", "=", "MSHIP_LIST.membership_id")
            ->leftJoin("membership as MSHIP2", "MSHIP2.mship_id", "=", "MDATA.membership")
            ->leftJoin("logmember as LOG", "LOG.author", "=", "MDATA.member_id")
            ->leftJoin("marketingdata as MARKETING", "MARKETING.mark_id", "=", "CACHE.id_marketing")
            ->leftJoin("ptdata as PT", "PT.pt_id", "=", "CACHE.id_pt")
            ->leftJoin("secure.users as CS", "CS.id", "=", "MDATA.created_by")
            ->leftJoin("logcheckin as checkin", "checkin.author", "=", "MDATA.member_id")
            ->select(
                'MDATA.m_startdate as join_date',
                'MDATA.member_id',
                'MDATA.name',
                'MDATA.status',
                'MDATA.email',
                'MSHIP.name as type_membership',
                'MSHIP2.name as type_membership_2',
                'MDATA.phone',
                'MSHIP_LIST.payment as total_payment',
                'LOG.transaction as total_payment_2',
                'MARKETING.name as FC',
                'CS.name as CS',
                'PT.name as PT'
            )
            ->orderBy('LOG.date', 'ASC')
            ->orderBy('MSHIP_LIST.start_date', 'ASC')
            ->whereDate('checkin.date', "=", $date_now)
            ->get();

        $totalQuery = count($checkinQuery);
        $arrayValidate = [];

        for($i=0; $i < $totalQuery; $i++){
            if(in_array($checkinQuery[$i]->member_id, $arrayValidate)){
                $checkinQuery->forget($i);
            }else{
                array_push($arrayValidate, $checkinQuery[$i]->member_id);
            }
        }

        return view('export.members', [
            'members' => $checkinQuery
        ]);
    }
}
