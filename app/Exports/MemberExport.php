<?php

namespace App\Exports;

use App\Model\member\MemberModel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MemberExport implements FromView
{
    public function view(): View
    {
        return view('export.members', [
            'invoices' => MemberModel::from("memberdata as PK")
                ->join("membership as mShipData", "mShipData.mship_id", "=", "PK.membership")
                ->join("membership_type as mShipType", "mShipType.mtype_id", "=", "mShipData.type")
                ->join("member_status as mStatus", "mStatus.mstatus_id", "=", "PK.status")
                ->select(
                    'PK.created_at as join_date',
                    'PK.member_id',
                    'PK.name',
                    'PK.email',
                    'mShipData.name as membership',
                    'PK.phone',
                    'PK.marketing as marketing',
                    'PK.pt as personal_trainer',
                    'PK.member_notes as notes'
                )
                ->orderBy('PK.name')
                ->get()
        ]);
    }
}
