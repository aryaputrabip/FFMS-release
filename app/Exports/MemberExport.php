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
        $memberQuery =
            DB::table("public.memberdata as dt1")
                ->join('secure.users as dt2', 'dt2.id', '=', 'dt1.created_by')
                ->join("membership as mShipData", "mShipData.mship_id", "=", "membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->select(
                    'dt1.created_at as join_date',
                    'dt1.member_id',
                    'dt1.name',
                    'dt1.email',
                    'mShipData.name as membership',
                    'dt1.phone',
                    'dt1.marketing as marketing',
                    'dt1.pt as personal_trainer',
                    'dt2.name as cs',
                    'dt1.member_notes as notes'
                )
                ->get();

        return view('export.members', [
            'members' => $memberQuery
        ]);
    }
}
