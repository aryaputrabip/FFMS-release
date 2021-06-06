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

    @if(Session::has('payment_success'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'success',
                button: false,
                showCancelButton: false,
                showConfirmButton: false,
                html: 'Pembayaran Berhasil! Klik <b>tombol</b> di bawah ini untuk mencetak invoice pembayaran.' +
                    '<br><br><a href="{{ route('member.printPembelianSesi', Session::get("payment_success")) }}" class="btn btn-primary"><i class="fas fa-print fa-sm mr-1"></i> Cetak Invoice</a>',
            });

            <?php Session::forget('payment_success') ?>
        </script>
    @endif
@endsection
