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
        //        $memberQuery = DB::table("public.memberdata as MEMBER")
//                        ->leftJoin("membership as MEMBERSHIP", "MEMBERSHIP.mship_id", "=", "membership")
//                        ->leftJoin("membership_type as MSHIP_TYPE", "MSHIP_TYPE.mtype_id", "=", "MEMBERSHIP.type")
//                        ->leftJoin("cache_read as CACHE", "CACHE.author", "=", "MEMBER.member_id")
//                        ->leftJoin("marketingdata as MARKETING", "MARKETING.mark_id", "=", "CACHE.id_marketing")
//                        ->leftJoin("ptdata as PT", "PT.pt_id", "=", "CACHE.id_pt")
//                        ->leftJoin("secure.users as CS", "CS.id", "=", "MEMBER.created_by")
//                        ->leftJoin("logmember as LOG", "LOG.author", "=", "MEMBER.member_id")
//                        ->leftJoin("membership_memberlist as MSHIP_LIST", "MSHIP_LIST.author", "=", "MEMBER.member_id")
//                        ->select(
//                            'MEMBER.created_at as join_date',
//                            'MEMBER.member_id',
//                            'MEMBER.name',
//                            'MEMBER.email',
//                            'MEMBER.phone',
//                            'MEMBER.membership as membership_member',
//                            'MEMBER.member_notes as notes',
//                            //'MEMBERSHIP.name as membership',
//                            'MSHIP_LIST.membership_id as membership',
//                            'MARKETING.name as marketing',
//                            'PT.name as pt',
//                            'CS.name as cs',
//                            'MSHIP_LIST.payment as last_transaction'
//                            //'LOG.transaction as last_transaction'
//                        )
//                        ->orderBy('LOG.date', 'DESC')
//                        ->get();

        $memberQuery = DB::table("public.memberdata as MDATA")
            ->leftJoin("cache_read as CACHE", "CACHE.author", "=", "MDATA.member_id")
            ->leftJoin("membership_memberlist as MSHIP_LIST", "MSHIP_LIST.author", "=", "MDATA.member_id")
            ->leftJoin("membership as MSHIP", "MSHIP.mship_id", "=", "MSHIP_LIST.membership_id")
            ->leftJoin("membership as MSHIP2", "MSHIP2.mship_id", "=", "MDATA.membership")
            ->leftJoin("logmember as LOG", "LOG.author", "=", "MDATA.member_id")
            ->leftJoin("marketingdata as MARKETING", "MARKETING.mark_id", "=", "CACHE.id_marketing")
            ->leftJoin("ptdata as PT", "PT.pt_id", "=", "CACHE.id_pt")
            ->leftJoin("secure.users as CS", "CS.id", "=", "MDATA.created_by")
            ->select(
                'MDATA.m_startdate as join_date',
                'MDATA.member_id',
                'MDATA.name',
                'MDATA.status',
                'MDATA.email',
                'MSHIP.name as type_membership',
                'MSHIP2.name as type_membership_2',
                'MDATA.phone',
                'MDATA.session as session',
                'MSHIP_LIST.payment as total_payment',
                'LOG.transaction as total_payment_2',
                'MARKETING.name as FC',
                'CS.name as CS',
                'PT.name as PT',
                'm_enddate as expired_date'
            )
            ->orderBy('LOG.date', 'ASC')
            ->orderBy('MSHIP_LIST.start_date', 'ASC')
            ->get();

        $totalQuery = count($memberQuery);
        $arrayValidate = [];

        for($i=0; $i < $totalQuery; $i++){
            if(in_array($memberQuery[$i]->member_id, $arrayValidate)){
                $memberQuery->forget($i);
            }else{
                array_push($arrayValidate, $memberQuery[$i]->member_id);
            }
        }

//        for($i=0; $i<$totalQuery; $i++){
//            if (in_array($memberQuery[$i]->member_id, $arrayValidate)) {
//                $memberQuery->forget($i);
//            }else{
//                array_push($arrayValidate, $memberQuery[$i]->member_id);
//
////                if(isset($memberQuery[$i]->membership)){
////                    $getMembershipName = MembershipModel::where('mship_id', $memberQuery[$i]->membership)->first();
////                    $memberQuery[$i]->membership = $getMembershipName->name;
////                }else{
////                    if(isset($memberQuery[$i]->membership_member)){
////                        $getMembershipName = MembershipModel::where('mship_id', $memberQuery[$i]->membership_member)->first();
////
////                        if(isset($getMembershipName)){
////                            $memberQuery[$i]->membership = $getMembershipName->name;
////                        }else{
////                            $memberQuery[$i]->membership = "-";
////                        }
////                    }else{
////                        $memberQuery[$i]->membership = "-";
////                    }
////                }
//            }
//        }

        return view('export.members', [
            'members' => $memberQuery
        ]);
    }
}
