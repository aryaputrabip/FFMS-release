@extends($app_layout)

<style>
    @section('css')
    .col-photo{
        -ms-flex: 0 0 270px;
        flex: 0 0 270px;
    }
    @endsection
</style>

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
                    <div class="col-sm-12 col-md-4 col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-users mr-1"></i> Jumlah Member Cuti</h6>
                        <h2>{{ $jMemberCuti }}</h2>
                    </div>
                    <div class="col-sm-6 col-md-4 col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-male mr-1"></i> Laki-laki</h6>
                        <h2>{{ $jMemberCutiLK }}</h2>
                    </div>
                    <div class="col-sm-6 col-md-4 col-4 text-center">
                        <h6 class="mb-0"><i class="fas fa-female mr-1"></i> Perempuan</h6>
                        <h2>{{ $jMemberCutiPR }}</h2>
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
                        <a href="#modal-pt" data-toggle="modal" onclick="newPTModal();" class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-plus fa-xs mr-1"></i> Tambah Member Cuti
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
                <table id="data_cuti" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Jenis Membership</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Cuti Sejak</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Cuti Hingga</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-cuti" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <span class="fas fa-calendar-minus mr-1"></span> Pengajuan Cuti Member
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="modal-pt-content">
                    <form id="cutiForm" action="#" method="POST">
                        <input type="hidden" id="activeMemberID" name="activeMemberID" readonly>
                        <input type="hidden" id="activeCutiDuration" name="activeStartDate" readonly>
                        <input type="hidden" id="endCutiDate" name="endCutiDate" readonly>
                        <input type="hidden" id="newMembershipEnd" name="newMembershipEnd" readonly>
                        {{ csrf_field() }}

                        <div class="form-group row">
                            <label for="dataIDMember" class="col-sm-3 col-form-label">
                                ID Member<span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="dataIDMember" name="dataIDMember" placeholder="Masukkan ID Member...">
                                <button type="button" class="btn btn-primary w-100 mt-3" id="searchBtn"><span class="fas fa-search fa-sm mr-1"></span> Check</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-view" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="fas fa-user fa-sm mr-1"></i> Preview Member
                    </h5>
                    <button type="button" class="close viewclose" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-action-content">
                    <div class="row m-1">
                        <div class="col-photo">
                            <img width="250" height="250" style="background-color: gray;" id="photo">
                            <button type="button" class="btn btn-primary mb-2 mt-3" style="width: 250px;" id="defineCuti">
                                <i class="fas fa-calendar-minus fa-sm mr-1"></i> Cutikan Member
                            </button>
                            <button type="button" class="btn btn-outline-dark mb-2 viewclose" data-dismiss="modal" style="width: 250px;">
                                <i class="fas fa-times fa-sm mr-1"></i> Batal
                            </button>
                        </div>

                        <div class="col-md col-sm-12">
                            <div class="card h-100">
                                <ul class="nav nav-tabs" id="member-manage-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="member-manage-detail-tab" data-toggle="pill" href="#member-manage-detail" role="tab" aria-controls="member-manage-detail-home" aria-selected="true">Informasi</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="member-manage-membership-tab" data-toggle="pill" href="#member-manage-membership" role="tab" aria-controls="member-manage-membership-profile" aria-selected="false">Paket Member</a>
                                    </li>
                                </ul>

                                <div class="card-body">
                                    <div class="tab-content" id="custom-content-below-tabContent">
                                        <div class="tab-pane fade show active" id="member-manage-detail" role="tabpanel" aria-labelledby="member-manage-detail-tab">
                                            <h3 class="text-left mt-0 mb-2 col-12 mb-2 pl-0">Informasi Member<br><small style="font-size: 18px;">ID Member : <span id="dataMemberID">123</span></small></h3>
                                            <div class="row">
                                                <div class="col-lg-6 mt-4 pr-3">
                                                    <h6><b>IDENTITAS</b></h6>
                                                    <hr>
                                                    <h6 class="mt-2"><b>Nama Lengkap</b></h6>
                                                    <h6 class="mb-3" id="dataMemberNama"> - </h6>

                                                    <h6><b>Jenis Kelamin</b></h6>
                                                    <h6 class="mb-3" id="dataMemberGender"> - </h6>

                                                    <h6><b>Pekerjaan</b></h6>
                                                    <h6 class="mb-3" id="dataMemberJob"> - </h6>

                                                    <h6><b>Perusahaan/Instansi</b></h6>
                                                    <h6 class="mb-3" id="dataMemberCompany"> - </h6>
                                                </div>
                                                <div class="col-lg-6 mt-4">
                                                    <h6><b>KONTAK</b></h6>
                                                    <hr>
                                                    <h6 class="mt-2"><b>No. Telp.</b></h6>
                                                    <h6 class="mb-3" id="dataMemberPhone"> - </h6>

                                                    <h6><b>Email</b></h6>
                                                    <h6 class="mb-3" id="dataMemberEmail"> - </h6>

                                                    <br>
                                                    <h6><b>ADDITIONAL INFO</b></h6>
                                                    <hr>
                                                    <h6 class="mt-2"><b>Marketing</b></h6>
                                                    <h6 class="mb-3" id="dataMemberMarketing"> - </h6>

                                                    <h6><b>Personal Trainer</b></h6>
                                                    <h6 class="mb-3" id="dataMemberPT"> - </h6>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="member-manage-membership" role="tabpanel" aria-labelledby="member-manage-membership-tab">
                                            <div class="row pb-0">
                                                <div class="col-12">
                                                    <h3 class="text-left mt-0 mb-2 col-12 mb-2">Paket Member</h3>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                    <table id="membershipTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                                        <thead>
                                                        <tr>
                                                            <th class="align-middle">Paket Member</th>
                                                            <th class="align-middle">Jenis</th>
                                                            <th class="align-middle">Durasi</th>
                                                            <th class="align-middle">Tanggal Mulai</th>
                                                            <th class="align-middle">Tanggal Berakhir</th>
                                                            <th class="align-middle">Total Kunjungan</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>

                                                <div class="col-12">
                                                    <h3 class="text-left mt-3 mb-2 col-12 mb-2">Personal Trainer & Sesi Latihan</h3>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                    <table id="ptTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                                        <thead>
                                                        <tr>
                                                            <th class="align-middle">Personal Trainer</th>
                                                            <th class="align-middle">Jenis Kelamin</th>
                                                            <th class="align-middle">Jumlah Sesi</th>
                                                            <th class="align-middle">Sisa Sesi</th>
                                                        </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-cuti-entry" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <span class="fas fa-calendar-minus mr-1"></span> Waktu Cuti
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="modal-pt-content">
                    <div class="form-group row">
                        <label for="dataCutiDuration" class="col-sm-3 col-form-label">
                            Cuti Selama<span class="text-danger">*</span>
                        </label>
                        <div class="col-7">
                            <input type="number" min="1" value="1" class="form-control" id="dataCutiDuration" name="dataCutiDuration">
                        </div>
                        <label class="col-2 col-form-label">
                            Bulan
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark pt-1 pb-1" data-dismiss="modal">
                        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
                    </button>
                    <button type="button" class="btn btn-primary pt-1 pb-1" id="confirmPengajuanCuti">
                        <i class="fas fa-check fa-sm mr-1"></i> Ajukan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-cuti-manager" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <span class="fas fa-user-cog mr-1"></span> Aksi | <span class="member_id_copier" onclick="clipboard()" id="detailMember"> - </span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <button id="aboardCutiBtn" href="#" class="btn btn-danger w-100" onclick="">
                        <i class="fas fa-calendar-times fa-sm mr-1"></i> Batalkan Cuti
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div>
        <form id="cutiRemoveForm" action="{{ route('cuti.remove') }}" method="POST">
            {{ csrf_field() }}

            <input type="text" id="formMember" name="formMember" readonly>
            <input type="text" id="formCuti" name="formCuti" readonly>
            <input type="text" id="formExpired" name="formExpired" readonly>
        </form>
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
        $("#cutiForm").on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                if($("#dataIDMember").val() == ""){
                    messagingErrorCustom("ID Member Belum Diisi!");
                }else{
                    checkingData($("#dataIDMember").val());
                }
            }
        });

        $("#data_cuti").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('cuti.getCutiData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'membership', name: 'membership' },
                { data: 'membership_type', name: 'membership_type' },
                { data: 'start_cuti', name: 'start_cuti' },
                { data: 'end_cuti', name: 'end_cuti' },
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

        $("#data_cuti_length").appendTo("#orderContainer");
        $("#data_cuti_filter").appendTo("#searchContainer");
        $("#data_cuti_info").addClass("pt-2 pl-2");
        $("#data_cuti_paginate").addClass("float-right");

        $("#searchBtn").on('click', function() {
            if($("#dataIDMember").val() == ""){
                messagingErrorCustom("ID Member Belum Diisi!");
            }else{
                checkingData($("#dataIDMember").val());
            }
        });

        $('#modal-cuti-entry').on('hide.bs.modal', function() {
            $("#defineCuti").attr("disabled", false);
            $("#modal-cuti").modal("hide");
            $("#modal-view").modal("show");
        });

        $("#defineCuti").on("click", function() {
            $(this).attr("disabled", true);
            $("#modal-cuti-entry").modal("show");
            $("#modal-view").modal("hide");
        });

        $(".viewclose").on("click", function () {
            $("#modal-cuti").modal("show");
            setSearchStatusNormal("#searchBtn");
        });

        $("#confirmPengajuanCuti").on("click", function () {
            checkRequired($("#dataCutiDuration").val(), this);
        });

        //SWAL INIT
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
        });

        function messagingErrorCustom(message){
            Toast.fire({
                icon: 'error',html: message
            })
        };
    });

    //SWAL INIT
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
    });

    function messagingInfoCustom(message){
        Toast.fire({
            icon: 'info',
            html: message
        })
    }

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',html: message
        })
    };

    function newPTModal(){
        $("#modal-cuti").modal("show");
    }

    function checkingData(mid){
        setSearchStatusLoad("#searchBtn");

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('cuti.preview') }}",
            data: {
                uid: mid,
            },
            success: function(data){
                getMemberContentData(data);
                $("#activeMemberID").val(mid);
            }
        });
    }

    function getMemberContentData(data){
        var obj = JSON.parse(data);

        if(obj.data == null){
            Swal.fire({
                icon: 'warning',
                button: false,
                html: 'ID Member Tidak Ditemukan!'
            });

            setSearchStatusNormal("#searchBtn");
        }else{
            if(obj.data.status == 2) {
                Swal.fire({
                    icon: 'warning',
                    button: false,
                    html: 'ID Member ditemukan namun <b>belum diaktivasi</b>!'
                });

                setSearchStatusNormal("#searchBtn");
            }else if(obj.data.status == 3){
                Swal.fire({
                    icon: 'warning',
                    button: false,
                    html: 'Member ini telah dicutikan!'
                });

                setSearchStatusNormal("#searchBtn");
            }else{
                var actionMembership = "{{ URL::to('member/edit') }}/"+ obj.data.member_id + "/getMemberMembership";
                var actionPT = "{{ URL::to('member/edit') }}/"+ obj.data.member_id + "/getMemberPT";

                var elementGroup = ["#dataMemberID", "#dataMemberNama", "#dataMemberGender", "#dataMemberJob",
                    "#dataMemberCompany", "#dataMemberPhone", "#dataMemberEmail", "#dataMemberMarketing", "#dataMemberPT"];

                var elementWrite = [obj.data.member_id, obj.data.name, obj.data.gender, obj.data.job,
                    obj.data.company, obj.data.phone, obj.data.email, obj.data.marketing, obj.data.pt];

                for(i = 0; i < elementGroup.length; i++){
                    if(elementWrite[i] == "" || elementWrite[i] == null){
                        $(elementGroup[i]).html(" - ");
                    }else{
                        $(elementGroup[i]).html(elementWrite[i]);
                    }
                }

                $("#visitlog").val(obj.data.visitlog);

                refreshPhoto(obj.data.photo);
                refreshMembershipTable(actionMembership);
                refreshPTTable(actionPT);
                refreshPreviewTab();

                $("#modal-cuti").modal("hide");
                $("#modal-view").modal("show");
                setSearchStatusNormal("#searchBtn");
            }
        }
    }

    function refreshPhoto(data){
        if(data == null){
            $("#photo").removeAttr("src").replaceWith(
                '<img width="250" height="250" style="background-color: gray;" id="photo">');
        }else{
            $("#photo").attr("src", data);
        }
    }

    function refreshPreviewTab(){
        $("#member-manage-detail").removeClass("show active");
        $("#member-manage-membership").removeClass("show active");
        $("#member-manage-detail-tab").removeClass("active");
        $("#member-manage-membership-tab").removeClass("active");

        $("#member-manage-detail").addClass("show active");
        $("#member-manage-detail-tab").addClass("active");
    }

    function refreshMembershipTable(actionMembership){
        $("#membershipTable").DataTable({
            destroy: true,
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: actionMembership,
            iDisplayLength: 10,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'duration', name: 'duration' },
                { data: 'start_date', name: 'start_date' },
                { data: 'expired_date', name: 'expired_date' },
                { data: 'visit', name: 'visit' },
            ],
        });
    }

    function refreshPTTable(actionPT){
        $("#ptTable").DataTable({
            destroy: true,
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: actionPT,
            iDisplayLength: 10,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'gender', name: 'gender' },
                { data: 'jsession', name: 'jsession' },
                { data: 'session_left', name: 'session_left' },
            ],
        });
    }

    function checkRequired(duration, btn){

        if(duration == "" || duration == null || duration <= 0){
            messagingErrorCustom("Durasi Cuti Belum Diisi!");
        }else{
            setConfirmLoad();

            $.ajax({
                type: 'GET',
                dataType: 'html',
                url: "{{ route('cuti.checkCapability') }}",
                data: {
                    uid: $("#activeMemberID").val(),
                    duration: $("#dataCutiDuration").val()
                },
                success: function(data){
                    var obj = JSON.parse(data);

                    if(obj.pass == -1){
                        notifyErrorCustom("Member ini telah dicutikan! Tidak dapat memproses pengajuan cuti!");
                        setConfirmNormal();
                    }else{
                        if(obj.pass < $("#dataCutiDuration").val()){
                            notifyErrorCustom("Durasi Cuti melebihi sisa masa berlaku membership! <br><b>Max : "+obj.pass+" Bulan</b>");
                            setConfirmNormal();
                        }else{
                            setConfirmNormal();
                            notifyCutiAccept(obj.olddate, obj.newdate, $("#dataCutiDuration").val(), obj.currentdate, obj.endcuti, obj.endcutiformat);
                        }
                    }
                }
            });
        }
    }

    function notifyCutiAccept(olddate, newdate, duration, current, endcuti, endcutiformat){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: '<h6 class="mb-3 text-center">Apakah Anda yakin untuk men-cutikan member ini?</h6>' +
                    '<center>' +
                        '<table class="table table-bordered w-100"><thead>' +
                            '<tr>' +
                                '<th>Membership Expired (<b>old</b>)</th>' +
                                '<th>Membership Expired (<b>new</b>)</th>' +
                            '</tr>' +
                            '<tr>' +
                                '<td>'+olddate+'</td>' +
                            '<td>'+newdate+'</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<th colspan="2">Durasi Cuti</th>' +
                            '</tr>' +
                            '<tr>' +
                                '<td colspan="2">'+duration+' Bulan (<i>'+current+' <b> - </b> '+endcuti+'</i>)</td>' +
                            '</tr>' +
                        '</thead></table>' +
                    '</center>',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Cutikan`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm
            ){
                $("#activeCutiDuration").val(duration);
                $("#endCutiDate").val(endcuti);
                $("#newMembershipEnd").val(newdate);

                $("#cutiForm").attr("action", "{{ route('cuti.approve') }}");
                $("#cutiForm").submit();
            }else{
                return false;
            }
        });
    }

    function cutiManager(id){
        $("#detailMember").html(id);
        $("#aboardCutiBtn").attr("onclick", "abortCuti('"+id+"')");
    }

    function abortCuti(id){
        setAbortLoad();

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('cuti.abortCuti') }}",
            data: {
                uid: id,
            },
            success: function(data){
                setAbortNormal();
                notifyAbortCuti(data);
            }
        });
    }

    function notifyAbortCuti(data){
        var obj = JSON.parse(data);

        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger mr-2',
                cancelButton: 'btn btn-outline-dark mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: '<h6 class="mb-3 text-center">Akhiri cuti untuk member ini?</h6>' +
                '<center>' +
                '<table class="table table-bordered w-100"><thead>' +
                '<tr>' +
                '<th class="w-50">Tanggal Sekarang</th>' +
                '<th class="w-50">Cuti Hingga</th>' +
                '</tr>' +
                '<tr>' +
                '<td>'+obj.today+'</td>' +
                '<td>'+obj.old_cuti_expired+'</td>' +
                '</tr>' +
                '</thead></table>' +
                '<table class="table table-bordered w-100"><thead>' +
                '<tr>' +
                '<th class="w-50">Membership Expired <br> <span class="font-weight-normal">(cuti)</span></th>' +
                '<th class="w-50">Membership Expired <br> <span class="font-weight-normal">(setelah dibatalkan)</span></th>' +
                '</tr>' +
                '<tr>' +
                '<td>'+obj.old_expired+'</td>' +
                '<td>'+obj.new_expired+'</td>' +
                '</tr>' +
                '</thead></table>' +
                '</center>',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Akhiri Cuti`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm
            ){
                $("#formMember").val(obj.member.member_id);
                $("#formCuti").val(obj.data.id);
                $("#formExpired").val(obj.new_expired);
                $("#cutiRemoveForm").submit();
            }else{
                return false;
            }
        });
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

    function notifyErrorCustom(message){
        Swal.fire({
            icon: 'warning',
            button: false,
            html: message
        });
    }

    function setAbortNormal(){
        $("#aboardCutiBtn").html('<i class="fas fa-calendar-times fa-sm mr-1"></i> Batalkan Cuti');
        $("#aboardCutiBtn").attr("disabled", false);
    }

    function setAbortLoad(){
        $("#aboardCutiBtn").html('<i class="fas fa-sync fa-spin fa-sm mr-1"></i> Memproses...');
        $("#aboardCutiBtn").attr("disabled", true);
    }

    function setConfirmNormal(){
        $("#confirmPengajuanCuti").html('<i class="fas fa-check fa-sm mr-1"></i> Ajukan');
        $("#confirmPengajuanCuti").attr("disabled", false);
    }

    function setConfirmLoad(){
        $("#confirmPengajuanCuti").html('<span class="fas fa-sync fa-spin fa-sm mr-1"></span> Memproses...');
        $("#confirmPengajuanCuti").attr("disabled", true);
    }

    function setSearchStatusLoad(element){
        $(element).html('<span class="fas fa-sync fa-spin fa-sm mr-1"></span> Checking...');
        $(element).attr("disabled", true);
    }

    function setSearchStatusNormal(element){
        $(element).html('<span class="fas fa-search fa-sm mr-1"></span> Check');
        $(element).attr("disabled", false);
    }
    @endsection
</script>
