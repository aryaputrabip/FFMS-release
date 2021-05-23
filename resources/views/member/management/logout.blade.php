@extends($app_layout)

@section('content')
    <div class="container-fluid">

        <!-- STATISTIC CARD -->
        <div class="card mb-3">
            <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>Statistik</b>
                <div class="card-tools mr-0">
                    <button type="button" class="btn btn-tool mt-0" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body pl-2 pr-2 pt-1 pb-1">
                <div class="row pt-2 pb-1">
                    <div class="col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Member Check-in</h6>
                        <h2>{{ $memberCheckin }}</h2>
                    </div>
                    <div class="col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-male mr-1"></i> Laki-laki</h6>
                        <h2>{{ $memberLK }}</h2>
                    </div>
                    <div class="col-4 text-center">
                        <h6 class="mb-0"><i class="fas fa-female mr-1"></i> Perempuan</h6>
                        <h2>{{ $memberPR }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATA CARD -->
        <div class="card">
            <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <div class="float-left">
                    <div style="margin-top: 10px">
                        <b>{{ $title }}</b>
                    </div>
                </div>
                <div class="float-right">
                    <div class='input-group'>
                        <a href="#modal-scan" data-toggle="modal" id="btnScanMemberID" class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-search fa-xs mr-1"></i> Scan Member ID
                        </a>
                        <div class='input-group-prepend mt-2' style="height: calc(1.8125rem + 2px);">
                            <span class='input-group-text'><i class="fas fa-search fa-xs"></i></span>
                        </div>
                        <div class='input-group-prepend mt-2 ml-0' id="searchContainer"></div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row">
                    <div class="col-md-6">
                        <div class='input-group'>
                            <div class='input-group-prepend mt-2 ml-2' style="height: calc(1.8125rem + 2px);">
                                <span class='input-group-text'>Show All</span>
                            </div>
                            <div class='input-group-prepend mt-2 ml-0' id="orderContainer"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right mt-2 mr-2 mb-2">
                            @include('config.filter.filter_member')
                        </div>
                    </div>
                </div>
                <table id="data_member" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Member ID</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama</th>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Jenis Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>

                <form id="checkoutForm" action="{{ route('member.checkoutMember') }}" method="POST">
                    {{ csrf_field() }}

                    <input type="hidden" id="checkoutMemberID" name="checkoutMemberID" readonly>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-scan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="modal-scan-overlay"></div>
                <div class="modal-header">
                    <h6 class="modal-title text-dark font-weight-bold">
                        <i class="fas fa-search fa-xs mr-1"></i>
                        Scan Member ID
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="scanForm" action="{{ route('member.getCheckoutMemberData') }}" method="POST">
                        {{ csrf_field() }}

                        <p>Menunggu Hasil Scan...</p>
                        <input type="hidden" class="form-control" id="dataIDMemberScan" name="dataIDMemberScan" autofocus="false">
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
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
        var member_table =
        $("#data_member").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getCheckinMemberData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'member_id', name: 'member_id' },
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'membership', name: 'membership' },
                { data: 'membership_type', name: 'membership_type' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [
                {"className": "dt-center", "targets": "DT_RowIndex"}
            ],
            language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "_MENU_" },
        });

        $("#data_member_length").appendTo("#orderContainer");
        $("#data_member_filter").appendTo("#searchContainer");
        $("#data_member_info").addClass("pt-2 pl-2");
        $("#data_member_paginate").addClass("float-right");

        $("#tableFilterStatus").on("change", function () {
            member_table.column($(this).data('column')).search($(this).val()).draw();
        });

        $("#tableFilterMembershipType").on("change", function () {
            member_table.column($(this).data('column')).search($(this).val()).draw();
        });

        $("#scanForm").on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                if($("#dataIDMemberScan").val() == ""){
                    messagingErrorCustom("ID Member Belum Diisi!");
                }else{
                    checkingData($("#dataIDMemberScan").val());
                }
            }
        });

        $("#modal-scan").on('shown.bs.modal', function(){
            $("#dataIDMemberScan").focus();
            $("#dataIDMemberScan").val("");
        });

        $("#modal-scan").on('hidden.bs.modal', function(){
            $("#dataIDMemberScan").blur();
            $("#dataIDMemberScan").val("");
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
        });
    }

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',
            html: message
        });
    }

    function checkingData(member_id){
        setScanLoading("#modal-scan-overlay");

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('member.getCheckoutMemberData') }}",
            data: {
                member_id: member_id,
            },
            success: function(data){
                var obj = JSON.parse(data);

                if(obj.member.length == 0) {
                    Swal.fire({
                        icon: 'warning',
                        button: false,
                        html: 'ID Member Tidak Ditemukan!'
                    });

                    setScanStatusNormal("#modal-scan-overlay");
                }else{
                    if(obj.member.checkin_status == false || obj.member.checkin_status == null)
                    {
                        Swal.fire({
                            icon: 'error',
                            button: false,
                            html: 'Member telah Check-out!'
                        });

                        setScanStatusNormal("#modal-scan-overlay");
                    }else{
                        checkoutMember(member_id);
                    }
                }
            }
        });
    }

    function checkoutMember(member_id){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger mr-2',
                cancelButton: 'btn btn-outline-secondary mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'info',
            html: 'Checkout Member <b>'+member_id+'</b> ?',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: `<i class="fas fa-calendar-check fa-xs"></i> Checkout`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                setScanStatusNormal("#modal-scan-overlay");

                $("#checkoutMemberID").val(member_id);
                $("#checkoutForm").submit();
            }else{
                setScanStatusNormal("#modal-scan-overlay");
                $("#dataIDMemberScan").val("");
                return false;
            }
        });
    }

    function setScanLoading(element){
        $(element).html('<div class="overlay d-flex justify-content-center align-items-center">' +
                            '<i class="fas fa-2x fa-sync fa-spin"></i>' +
                        '</div>');
    }

    function setScanStatusNormal(element){
        $(element).html("");
    }

    @endsection
</script>
