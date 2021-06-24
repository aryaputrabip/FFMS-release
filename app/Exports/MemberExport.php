<?php

namespace App\Exports;

use App\Model\member\MemberModel;
use App\Model\membership\MembershipModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MemberExport implements FromView
{
    public function view(): View
    {
        $memberQuery = DB::table("public.memberdata as MEMBER")
                        ->leftJoin("membership as MEMBERSHIP", "MEMBERSHIP.mship_id", "=", "membership")
                        ->leftJoin("membership_type as MSHIP_TYPE", "MSHIP_TYPE.mtype_id", "=", "MEMBERSHIP.type")
                        ->leftJoin("cache_read as CACHE", "CACHE.author", "=", "MEMBER.member_id")
                        ->leftJoin("marketingdata as MARKETING", "MARKETING.mark_id", "=", "CACHE.id_marketing")
                        ->leftJoin("ptdata as PT", "PT.pt_id", "=", "CACHE.id_pt")
                        ->leftJoin("secure.users as CS", "CS.id", "=", "MEMBER.created_by")
                        ->leftJoin("logmember as LOG", "LOG.author", "=", "MEMBER.member_id")
                        ->leftJoin("membership_memberlist as MSHIP_LIST", "MSHIP_LIST.author", "=", "MEMBER.member_id")
                        ->select(
                            'MEMBER.created_at as join_date',
                            'MEMBER.member_id',
                            'MEMBER.name',
                            'MEMBER.email',
                            'MEMBER.phone',
                            'MEMBER.membership as membership_member',
                            'MEMBER.member_notes as notes',
                            //'MEMBERSHIP.name as membership',
                            'MSHIP_LIST.membership_id as membership',
                            'MARKETING.name as marketing',
                            'PT.name as pt',
                            'CS.name as cs',
                            'MSHIP_LIST.payment as last_transaction'
                            //'LOG.transaction as last_transaction'
                        )
                        ->orderBy('LOG.date', 'DESC')
                        ->get();

        $totalQuery = count($memberQuery);
        $arrayValidate = [];

        for($i=0; $i<$totalQuery; $i++){
            if (in_array($memberQuery[$i]->member_id, $arrayValidate)) {
                $memberQuery->forget($i);
            }else{
                array_push($arrayValidate, $memberQuery[$i]->member_id);
                if(isset($memberQuery[$i]->membership)){
                    $getMembershipName = MembershipModel::where('mship_id', $memberQuery[$i]->membership)->first();
                    $memberQuery[$i]->membership = $getMembershipName->name;
                }else{
                    if(isset($memberQuery[$i]->membership_member)){
                        $getMembershipName = MembershipModel::where('mship_id', $memberQuery[$i]->membership_member)->first();

                        if(isset($getMembershipName)){
                            $memberQuery[$i]->membership = $getMembershipName->name;
                        }else{
                            $memberQuery[$i]->membership = "-";
                        }
                    }else{
                        $memberQuery[$i]->membership = "-";
                    }
                }
            }
        }

        return view('export.members', [
            'members' => $memberQuery
        ]);
    }
}
