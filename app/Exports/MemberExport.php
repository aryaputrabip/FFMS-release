<?php

namespace App\Exports;

use App\Model\member\MemberModel;
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
                        ->join("membership as MEMBERSHIP", "MEMBERSHIP.mship_id", "=", "membership")
                        ->join("membership_type as MSHIP_TYPE", "MSHIP_TYPE.mtype_id", "=", "MEMBERSHIP.type")
                        ->join("cache_read as CACHE", "CACHE.author", "=", "MEMBER.member_id")
                        ->join("marketingdata as MARKETING", "MARKETING.mark_id", "=", "CACHE.id_marketing")
                        ->join("ptdata as PT", "PT.pt_id", "=", "CACHE.id_pt")
                        ->join("secure.users as CS", "CS.id", "=", "MEMBER.created_by")
                        ->select(
                            'MEMBER.created_at as join_date',
                            'MEMBER.member_id',
                            'MEMBER.name',
                            'MEMBER.email',
                            'MEMBER.phone',
                            'MEMBER.member_notes as notes',
                            'MEMBERSHIP.name as membership',
                            'MARKETING.name as marketing',
                            'PT.name as pt',
                            'CS.name as cs'
                        )->get();

        return view('export.members', [
            'members' => $memberQuery
        ]);
    }
}
