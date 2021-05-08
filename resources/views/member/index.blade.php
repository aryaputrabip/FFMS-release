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
                    <div class="col-md-3 col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-users mr-1"></i> Jumlah Member</h6>
                        <h2>{{ $jMember }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Member Aktif</h6>
                        <h2>{{ $memberActive }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-male mr-1"></i> Laki-laki</h6>
                        <h2>{{ $memberLK }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center">
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
                        <a @if($role == 1) href="{{ route('suadmin.member.registration.index') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.member.registration.index') }}" @endif class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-plus fa-xs mr-1"></i> Tambah Member
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
                        <div class="float-right">

                        </div>
                    </div>
                </div>
                <table id="data_member" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama</th>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Jenis Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Tanggal Berlaku</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Tanggal Berakhir</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-action">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="fas fa-user-cog mr-1"></i> Aksi | <span class="member_id_copier" onclick="clipboard()" id="detailMember"> - </span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-action-content">

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
        $("#data_member").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'status', name: 'status' },
                { data: 'membership', name: 'membership' },
                { data: 'membership_type', name: 'membership_type' },
                { data: 'date_from', name: 'date_from' },
                { data: 'date_expired', name: 'date_expired' },
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

    function modalMember(id){
        $("#detailMember").html(id);
    }

    function clipboard(data){
        var range = document.createRange();
        range.selectNode(document.getElementById('detailMember'));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges();// to deselect
        messagingInfoCustom('ID Member disalin ke clipboard!');
    }

    function requestAction(id, duration){
        $("#modal-action-content").html("<div class='text-center'><i class='fas fa-sync fa-spin'></i></div>");

        $.ajax({
            type: 'GET',
            url: '{{ route('member.requestModalAction') }}',
            data:{
                id:id,
                duration:duration
            },
            success: function (data) {
                $("#modal-action-content").html(data);
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function activateMember(id, duration){
        var token = '{{ csrf_token() }}';

        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success mr-2',
                cancelButton: 'btn btn-outline-dark mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin melakukan Aktivasi untuk member ini ?</small>',
            showCancelButton: true,
            cancelButtonText: '<i class="fas fa-times fa-sm mr-1"></i> Batal',
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Aktivasi`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel
            ) {
                return false;
            }else{
                $.post("{{ route('member.aktivasi') }}", { id:id, _token:token, duration:duration}, function(data){
                    location.reload();
                });
            }
        });
    }
    @endsection
</script>

