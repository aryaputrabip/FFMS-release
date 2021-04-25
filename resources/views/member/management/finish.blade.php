@extends($app_layout)

@section('bg')
    <div style="background-color: #FFFFFF; min-width: 100vw; min-height: 100vh; position:absolute;"></div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- STEP CARD -->
        <div class="card">
            <div class="card-body pt-2 pb-2 pl-3 pr-3">
                <h3 class="text-center pb-3" style="border-bottom: 1px solid rgba(0, 0, 0, 0.125);">Pendaftaran Member Berhasil</h3>
                <p class="text-center">
                    Pendaftaran member telah selesai. Silahkan klik <b>kembali</b>
                    atau <b>cetak invoice</b> pendaftaran.<br>
                    <i>
                        (invoice pendaftaran juga dapat dicetak melalui menu member)
                    </i>
                </p>

                <div class="attachment-block clearfix mt-4 text-center" style="padding: 15px;">
                    <p class="font-weight-bold text-uppercase">ID Member Anda:</p>
                    <h1 class="font-weight-bold">
                        <span class="clipboard-text member_id_copier" id="mdata" title="Klik untuk menyalin" onclick="copyToClipboard();">{{ Request::get('mdata') }}</span></h1>
                </div>

                <div class="attachment-block clearfix mt-2 mb-4 text-center" style="padding: 15px;">
                    <p class="font-weight-bold text-uppercase">SCAN QR CODE:</p>
                    <div id="qrcode"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('member.registration.print', Request::get('mdata')) }}" type="button" class="btn btn-primary mt-2 w-100" id="continueToPrint">
                            <i class="fas fa-print fa-sm mr-1"></i> Cetak Invoice
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary mt-2 w-100" id="continueToCheckin" disabled>
                            <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                        </button>
                    </div>
                    <div class="col-12">
                        <a @if($role == 1) href="{{ route('suadmin.index') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.index') }}" @endif class="btn btn-secondary mt-2 w-100" id="continueToBack">
                            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('import_script')
    @include('theme.default.import.modular.qrcode.script')
@endsection

@section('message')
    @if(Session::has('success'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'success',
                button: false,
                html: '{{Session::get("success")}}',
                timer: 1500
            })
        </script>
        <?php Session::forget('success') ?>
    @endif

    @if(Session::has('failed'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'warning',
                button: false,
                html: '{{Session::get("failed")}}',
                timer: 1500
            })
        </script>
        <?php Session::forget('failed') ?>
    @endif
@endsection

<script>
    @section('script')
    $(function () {
        new QRCode(document.getElementById("qrcode"), $("#mdata").html());
        setTimeout(function(){
            $("#qrcode").children('img').css('display', 'inline')
        }, 1);

        $("#continueToPrint").on('click', function(){

        });

        $("#continueToBack").on('click', function(){

        });
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    function messagingInfoCustom(message){
        Toast.fire({
            icon: 'info',
            html: message
        })
    }

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',
            html: message
        })
    }

    function copyToClipboard(data){
        var range = document.createRange();
        range.selectNode(document.getElementById("mdata"));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
        messagingInfoCustom('Member ID disalin ke clipboard!');
    }
    @endsection
</script>
