<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <!-- INIT STYLE -->
    @include('theme.default.source.css_source')

</head>
<body>

<style>
    h6{
        font-size: 14px;
    }
    .page-break {
        page-break-after: always;
    }
</style>

<?php
function asRupiah($value) {
    if ($value<0) return "-".asRupiah(-$value);
    return 'Rp. ' . number_format($value, 0);
}
?>

<div class="row">
    <div class="col-5 text-center">
        <img src="{{ asset('/data/logo/logo-full.png') }}" width="125px">
    </div>
    <div class="col-7 text-center" style="float: right;">
        <h6><b style="font-weight: normal; line-height: 0.5;">Ruko Oregon Square Blok TCR No. 6,7</b></h6>
        <h6><b style="font-weight: normal; line-height: 0.7;">Kelurahan Ciangsana, Kecamatan Gunung Putri, Bogor</b></h6>
        <h6><b style="font-weight: normal; line-height: 0.6;">Telp. 021 84942222</b></h6>
    </div>
</div>
<br>
<div class="row">
    <div class="col-12 text-center">
        <h4 style="line-height: 0.5;" class="mt-2"><b>PROFORMA INVOICE</b></h4>
        <h6 style="font-weight: normal; line-height: 0.8;">{{ $PINO }}</h6><br>
    </div>
</div>
<table class="mb-2 w-100">
    <tbody>
    <tr>
        <td width="150px" style="vertical-align: top;"><h6><b style="font-weight: normal">Member No.</b></h6></td>
        <td style="vertical-align: top;" width="10px"><h6><b style="font-weight: normal"> : </b></h6></td>
        <td><h6><b style="font-weight: normal">{{ $memberID }}</b></h6></td>
        <td><h6 style="float: right;"><b style="font-weight: normal;">DATE : {{ $time }}</b></h6></td>
    </tr>
    <tr>
        <td width="150px" style="vertical-align: top;"><h6><b style="font-weight: normal">Member Name</b></h6></td>
        <td style="vertical-align: top;" width="10px"><h6><b style="font-weight: normal"> : </b></h6></td>
        <td colspan="2">
            <h6><b style="font-weight: normal">{{ $data->memberName }}</b></h6>
        </td>
    </tr>
    {{--        <tr>--}}
    {{--            <td width="150px" style="vertical-align: top;"><h6><b style="font-weight: normal">Address</b></h6></td>--}}
    {{--            <td style="vertical-align: top;" width="10px"><h6><b style="font-weight: normal"> : </b></h6></td>--}}
    {{--            <td colspan="2">--}}
    {{--                <h6><b style="font-weight: normal"> - </b></h6>--}}
    {{--            </td>--}}
    {{--        </tr>--}}
    </tbody>
</table>

<table border="1" class="w-100">
    <tbody>
    <tr style="font-weight: bold;" class="text-center">
        <td class="pl-1 pr-1"><h6><b>NO</b></h6></td>
        <td class="pl-1 pr-1"><h6><b>DESCRIPTION</b></h6></td>
        <td class="pl-1 pr-1"><h6><b>AMOUNT</b></h6></td>
        <td class="pl-1 pr-1"><h6><b>DISC</b></h6></td>
        <td class="pl-1 pr-1"><h6><b>TOTAL</b></h6></td>
    </tr>
    <tr>
        <td class="text-center pl-1 pr-1"><h6><b style="font-weight: normal">1</b></h6></td>
        <td class="pl-1 pr-1"><h6><b style="font-weight: normal">{{$desc_title}} {{ $data->membership }} ({{ $data->type }})</b></h6></td>
        @if(isset($log->t_membership))
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($log->t_membership); ?></b></h6></td>
            <td class="text-center"><h6><b style="font-weight: normal"></b> - </h6></td>
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($log->t_membership); ?></b></h6></td>
        @else
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($data->membershipPrice); ?></b></h6></td>
            <td class="text-center"><h6><b style="font-weight: normal"></b> - </h6></td>
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($data->membershipPrice); ?></b></h6></td>
        @endisset

    </tr>
    @if(isset($log->t_sesi))
        <tr>
            <td class="text-center pl-1 pr-1"><h6><b style="font-weight: normal">2</b></h6></td>
            <td class="pl-1 pr-1"><h6><b style="font-weight: normal">{{$pt_title}}</b></h6></td>
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($log->t_sesi); ?></b></h6></td>
            <td class="text-center"><h6><b style="font-weight: normal"></b> - </h6></td>
            <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($log->t_sesi); ?></b></h6></td>
        </tr>
    @else
        @if(isset($data->session_reg))
            <tr>
                <td class="text-center pl-1 pr-1"><h6><b style="font-weight: normal">2</b></h6></td>
                <td class="pl-1 pr-1"><h6><b style="font-weight: normal">{{$pt_title}}</b></h6></td>
                <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($session->session_price); ?></b></h6></td>
                <td class="text-center"><h6><b style="font-weight: normal"></b> - </h6></td>
                <td class="text-right pl-1 pr-1"><h6><b style="font-weight: normal"><?php echo asRupiah($session->session_price); ?></b></h6></td>
            </tr>
        @endisset
    @endisset

    <tr>
        <td colspan="2" style="border-bottom: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-bottom: 1px solid #FFFFFF;"></td>
        <td colspan="2" class="pl-1 pr-1"><h6><b>SUBTOTAL</b></h6></td>
        @if(isset($log->t_sesi))
            <td class="text-right"><h6><b style="font-weight: normal;">
                <?php echo asRupiah(($log->t_membership + $log->t_sesi)); ?>
            </td>
        @else
            <td class="text-right"><h6><b style="font-weight: normal;">
                        @if($data->session_reg != null)
                            <?php echo asRupiah(($data->membershipPrice + $session->session_price)); ?>
                        @else
                            <?php echo asRupiah(($data->membershipPrice)); ?>
                        @endif</b></h6>
            </td>
        @endisset
        </tr>
    <tr>
        <td colspan="2" style="border-bottom: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-bottom: 1px solid #FFFFFF; border-top: 1px solid #FFFFFF;"></td>
        <td colspan="2" class="pl-1 pr-1"><h6><b>PPN</b></h6></td>
        <td class="text-right"><h6><b style="font-weight: normal;"></b> - </h6></td>
    </tr>
    <tr>
        <td colspan="2" style="border-bottom: 1px solid #FFFFFF; border-left: 1px solid #FFFFFF; border-bottom: 1px solid #FFFFFF; border-top: 1px solid #FFFFFF;"></td>
        <td colspan="2" class="pl-1 pr-1"><h6><b>GRAND TOTAL</b></h6></td>
        @if(isset($log->t_sesi))
            <td class="text-right">
                <h6><b style="font-weight: normal;">
                    <?php echo asRupiah(($log->t_membership + $log->t_sesi)); ?>
                </b></h6>
            </td>
        @else
            @if($data->session_reg != null)
                <td class="text-right">
                    <h6><b style="font-weight: normal;">
                        <?php echo asRupiah(($data->membershipPrice + $session->session_price)); ?>
                    </b></h6>
                </td>
            @else
                <td class="text-right">
                    <h6><b style="font-weight: normal;">
                        <?php echo asRupiah(($data->membershipPrice)); ?>
                    </b></h6>
                </td>
            @endif
        @endisset
    </tr>
    </tbody>
</table>
<br>
<div class="row">
    <div class="col-10">
        <h6 class="mt-2"><small><i><b>Transfer Via :</b></i></small></h6>
        <div class="row">
            <div class="col-3">
                <h6><small><i><b>Metode</b></i></small></h6>
                <h6><small><i><b>Bank Name</b></i></small></h6>
                <br>
            </div>
            <div class="col-8" style="padding-left: 150px;">
                <h6><small><i><b>: {{ $metodeBayar }}</b></i></small></h6>
                <h6><small><i><b>: {{ $namaBank }}</b></i></small></h6>
            </div>
        </div>
    </div>
{{--    <br>--}}
{{--    <div class="col-5">--}}
{{--        <h6 class="mt-2"><small><i><b>Catatan :</b></i></small></h6>--}}
{{--        @if(isset($log->notes))--}}
{{--            <h6 style="font-weight: normal;">{{ $log->notes }}</h6>--}}
{{--        @else--}}
{{--            <h6 style="font-weight: normal;"> - </h6>--}}
{{--        @endisset--}}
{{--    </div>--}}
    <div class="col-3 text-center" style="float: right;">
        <h6 style="font-weight: normal;">Prepared by,</h6><br><br>
        <h6 style="font-weight: normal;">(
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            )
        </h6>
    </div>
</div>
@if(isset($log->notes))
    <div class="row mt-5">
        <div class="col-12">
            <div class="attachment-block">
                <h6 class="mb-0 pl-1 pr-1"><b>Catatan :</b></h6>
                <h6 class="pl-1 pr-1" style="font-weight: normal; text-align: justify">
                    {{ $log->notes }}
                </h6>
            </div>
        </div>
    </div>
@endisset

{{--    <div class="page-break"></div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-5 text-center">--}}
{{--            <img src="{{ asset('/data/logo/logo-full.png') }}" width="125px">--}}
{{--        </div>--}}
{{--        <div class="col-7 text-center" style="float: right;">--}}
{{--            <h6><b style="font-weight: normal; line-height: 0.5;">Ruko Oregon Square Blok TCR No. 6,7</b></h6>--}}
{{--            <h6><b style="font-weight: normal; line-height: 0.7;">Kelurahan Ciangsana, Kecamatan Gunung Putri, Bogor</b></h6>--}}
{{--            <h6><b style="font-weight: normal; line-height: 0.6;">Telp. 021 84942222</b></h6>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <div class="row">--}}
{{--        <div class="col-12">--}}
{{--            <div class="attachment-block clearfix mt-2 mb-4 text-center" style="padding: 15px;">--}}
{{--                <p class="font-weight-bold text-uppercase">SCAN QR CODE:</p>--}}
{{--                <div id="qrcode"></div>--}}
{{--                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(200)->generate($memberID)) !!} ">--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


<!-- INIT SCRIPTS -->
@include('theme.default.source.script_source')
</body>
</html>
