<html>
@include('theme.default.source.script_source')
@include('theme.default.source.css_source')

<table class="table table-bordered w-100" style="border: solid 1px #000000;">
    <thead>
    <tr>
        <th colspan="12" align="center" valign="top middle" height="30" style="background-color: #c00000; color: #ffffff; font-size: 16px; border: 1px solid #000000;">
            <b>MEMBER DATA</b>
        </th>
    </tr>
    <tr>
        <th align="center" width="5" style="background-color: #ffc000; border: 2px solid #000000;"><b>NO</b></th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;"><b>JOIN DATE</b></th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;"><b>ID MEMBER</b></th>
        <th align="center" width="28" style="background-color: #ffc000; border: 2px solid #000000;"><b>MEMBER NAME</b></th>
        <th align="center" width="28" style="background-color: #ffc000; border: 2px solid #000000;"><b>EMAIL</b></th>
        <th align="center" width="25" style="background-color: #ffc000; border: 2px solid #000000;"><b>TYPE MEMBERSHIP</b></th>
        <th align="center" width="18" style="background-color: #ffc000; border: 2px solid #000000;"><b>NO TELP</b></th>
        <th align="center" width="24" style="background-color: #ffc000; border: 2px solid #000000;"><b>TOTAL PAYMENT</b></th>
        <th align="center" width="14" style="background-color: #ffc000; border: 2px solid #000000;"><b>FC</b></th>
        <th align="center" width="14" style="background-color: #ffc000; border: 2px solid #000000;"><b>CS</b></th>
        <th align="center" width="14" style="background-color: #ffc000; border: 2px solid #000000;"><b>PT</b></th>
        <th align="center" width="14" style="background-color: #ffc000; border: 2px solid #000000;"><b>Expired Date</b></th>
    </tr>
    </thead>
    <tbody>
        <?php
        function asRupiah($value) {
            if ($value<0) return "-".asRupiah(-$value);
            return 'Rp. ' . number_format($value, 0);
        }

        ?>

        @foreach($members as $key => $member)
        <tr>
            <td align="center" style="border: 1px solid #000000;">{{ $loop->iteration }}</td>
            <td align="center" style="border: 1px solid #000000;">{{ date("d-M-y", strtotime($member->join_date)) }}</td>
            <td align="center" style="border: 1px solid #000000;">{{ $member->member_id }}</td>
            <td align="left" style="border: 1px solid #000000;">{{ $member->name }}</td>
            <td align="left" style="border: 1px solid #000000;">{{ $member->email }}</td>
            <td align="center" style="border: 1px solid #000000;">@if(isset($member->type_membership)) {{ $member->type_membership }} @else {{ $member->type_membership_2 }} @endisset</td>
            <td align="center" style="border: 1px solid #000000;">{{ $member->phone }}</td>
            <td align="right" style="border: 1px solid #000000;">@if(isset($member->total_payment)) {{ asRupiah($member->total_payment) }} @else {{ asRupiah($member->total_payment_2) }} @endisset</td>
            <td align="left" style="border: 1px solid #000000;">{{ $member->FC }}</td>
            <td align="left" style="border: 1px solid #000000;">{{ $member->CS }}</td>
            <td align="left" style="border: 1px solid #000000;">{{ $member->PT }}</td>
            <td align="left" style="border: 1px solid #000000;">@if(isset($member->expired_date)) {{ date("d M Y", strtotime($member->expired_date)) }} @else - @endif</td>
        </tr>
        @endforeach
    </tbody>
</table>

</html>
