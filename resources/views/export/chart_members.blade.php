<html>
@include('theme.default.source.script_source')
@include('theme.default.source.css_source')

<table class="table table-bordered w-100" style="border: solid 1px #000000;">
    <thead>
    <tr>
        <th colspan="10" align="center" valign="top middle" height="30" style="background-color: #c00000; color: #ffffff; font-size: 16px; border: 1px solid #000000;">
            <b>{{ $title }}</b>
        </th>
    </tr>
    <tr>
        <th align="center" width="5" style="background-color: #ffc000; border: 2px solid #000000;">NO</th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;">{{ $filter}}</th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;">Member (Laki-laki)</th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;">Member (Perempuan)</th>
        <th align="center" width="15" style="background-color: #ffc000; border: 2px solid #000000;"><b>Total Member</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($members as $key => $member)
        <tr>
            <td align="center" style="border: 1px solid #000000;">{{ $loop->iteration }}</td>
            <td align="center" style="border: 1px solid #000000;">{{ $data->filter }}</td>
            <td align="center" style="border: 1px solid #000000;">{{ $data->memberLK }}</td>
            <td align="left" style="border: 1px solid #000000;">{{ $data->memberPR }}</td>
            <td align="left" style="border: 1px solid #000000;"><b>{{ $data->memberTotal }}</b></td>
        </tr>
    @endforeach
    </tbody>
</table>

</html>
