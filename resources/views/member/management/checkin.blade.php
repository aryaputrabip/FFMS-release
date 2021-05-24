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
    <div class="container-fluid w-100">
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-body p-3">
                        <button class="btn btn-primary w-100 mb-2" id="btnCheckinManual">
                            Check-In (Manual)
                        </button>
                        <button class="btn btn-outline-primary mb-2 w-100" id="btnCheckingScan">
                            Check-In (Scan)
                        </button>
                        <hr>
                        <a @if($role == 1) href="{{ route('suadmin.member.checkout') }}" @elseif($role == 2) href="#"
                           @elseif($role == 3) href="{{ route('cs.member.checkout') }}" @endif class="btn btn-danger w-100">
                            <i class="fas fa-calendar-times fa-sm mr-1"></i> Checkout
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card" id="containerCheckinManual">
                    <div class="card-body p-3">
                        <h4 class="d-inline"><span class="fas fa-calendar-check fa-sm mr-1"></span> Check-In (Manual)</h4><hr>
                        <div class="row">
                            <div class="col-12">
                                <form id="checkinForm" action="{{ route('member.checkin') }}" method="POST">
                                    {{ csrf_field() }}

                                    <input type="hidden" id="visitlog" name="visitlog" readonly>
                                    <input type="hidden" id="dataCheckinSource" name="dataCheckinSource" value="checkin" readonly>

                                    <div class="form-group row">
                                        <label for="dataUserNama" class="col-sm-3 col-form-label">
                                            ID Member<span class="text-danger">*</span>
                                        </label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="dataIDMember" name="dataIDMember" placeholder="Masukkan ID Member..." autofocus="true">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 mt-3" id="checkinBtn"><span class="fas fa-search fa-sm mr-1"></span> Check</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card" id="containerCheckinScan" style="display: none;">
                    <div class="card-body p-3">
                        <h4 class="d-inline"><span class="fas fa-calendar-check fa-sm mr-1"></span> Check-In (Scan)</h4><hr>
                        <div class="row">
                            <div class="col-12">
                                <form id="checkinFormScan" action="{{ route('member.checkin') }}" method="POST">
                                    {{ csrf_field() }}

                                    <p>Menunggu Hasil Scan...</p>
                                    <input type="text" class="form-control" id="dataIDMemberScan" name="dataIDMemberScan" style="width: 0; overflow: hidden; opacity: 0;">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-view" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark">
                        <i class="fas fa-user fa-sm mr-1"></i> Preview Member
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-action-content">
                    <div class="row m-1">
                        <div class="col-photo">
                            <img width="250" height="250" style="background-color: gray;" id="photo">
                            <button type="button" class="btn btn-primary mb-2 mt-3" style="width: 250px;" id="checkinConfirm">
                                <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                            </button>
                            <button type="button" class="btn btn-outline-dark mb-2" data-dismiss="modal" style="width: 250px;">
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
                                                <div class="col-8">
                                                    <h3 class="text-left mt-0 mb-2 col-12 mb-2">Paket Member</h3>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <button type="button" class="btn btn-secondary mt-0 mb-2" disabled><i class="fas fa-plus mr-1 fa-xs"></i> Perpanjang</button>
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

                                                <div class="col-8">
                                                    <h3 class="text-left mt-3 mb-2 col-12 mb-2">Personal Trainer & Sesi Latihan</h3>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <button type="button" class="btn btn-secondary mt-3 mb-2" disabled><i class="fas fa-plus mr-1 fa-xs"></i> Tambah Sesi</button>
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
    $(function(){
        $("#checkinForm").on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                if($("#dataIDMember").val() == ""){
                    messagingErrorCustom("ID Member Belum Diisi!");
                }else{
                    checkingData($("#dataIDMember").val());
                }
            }
        });

        $("#checkinFormScan").on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                if($("#dataIDMemberScan").val() == ""){
                    messagingErrorCustom("ID Member Belum Diisi!");
                }else{
                    $("#dataIDMember").val($("#dataIDMemberScan").val());
                    checkingData($("#dataIDMember").val());
                }
            }
        });

        $("#btnCheckinManual").on('click', function() {
            $("#btnCheckinManual").removeClass();
            $("#btnCheckingScan").removeClass();

            $("#btnCheckinManual").addClass('btn btn-primary w-100 mb-2');
            $("#btnCheckingScan").addClass('btn btn-outline-primary w-100 mb-2');

            $("#dataIDMember").val("");
            $("#dataIDMemberScan").val("");

            $("#containerCheckinManual").show();
            $("#containerCheckinScan").hide();

            $("#dataIDMember").focus();
        });

        $("#btnCheckingScan").on('click', function() {
            $("#btnCheckinManual").removeClass();
            $("#btnCheckingScan").removeClass();

            $("#btnCheckingScan").addClass('btn btn-primary w-100 mb-2');
            $("#btnCheckinManual").addClass('btn btn-outline-primary w-100 mb-2');

            $("#dataIDMember").val("");
            $("#dataIDMemberScan").val("");

            $("#containerCheckinScan").show();
            $("#containerCheckinManual").hide();

            $("#dataIDMemberScan").focus();
        });

        $("#checkinBtn").on('click', function() {
            if($("#dataIDMember").val() == ""){
                messagingErrorCustom("ID Member Belum Diisi!");
            }else{
                checkingData($("#dataIDMember").val());
            }
        });

        $('#modal-view').on('hide.bs.modal', function() {
            setCheckinStatusNormal("#checkinBtn");
            $("#dataIDMemberScan").val("");
        });

        $("#checkinConfirm").on('click', function() {
            const DestroySwal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary mr-2',
                    cancelButton: 'btn btn-outline-secondary mr-2'
                },
                buttonsStyling: false
            })

            DestroySwal.fire({
                icon: 'info',
                html: 'Konfirmasi Checkin ?',
                showCancelButton: true,
                cancelButtonText: 'Kembali',
                confirmButtonText: `<i class="fas fa-calendar-check fa-xs"></i> Check-In`,
                reverseButtons: true
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.confirm){
                    $("#checkinForm").submit();
                }else{
                    return false;
                }
            });
        });

        //SWAL INIT
        const Toast = Swal.mixin({
            toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
        });

        function messagingErrorCustom(message){
            Toast.fire({
                icon: 'error',html: message
            })
        }
    });

    function checkingData(mid){
        setCheckinStatusLoad("#checkinBtn");

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('member.preview') }}",
            data: {
                uid: mid,
            },
            success: function(data){
                getMemberContentData(data);
            }
        });
    }

    function getMemberContentData(data){
        var obj = JSON.parse(data);
        console.log(obj.data);

        if(obj.data == null){
            Swal.fire({
                icon: 'warning',
                button: false,
                html: 'ID Member Tidak Ditemukan!'
            });

            setCheckinStatusNormal("#checkinBtn");
            $("#dataIDMemberScan").val("");
        }else{
            if(obj.data.status != 1) {
                if(obj.data.status == 2){
                    Swal.fire({
                        icon: 'warning',
                        button: false,
                        html: 'Member ini <b>belum diaktivasi!</b>'
                    });
                }else if(obj.data.status == 3){
                    Swal.fire({
                        icon: 'warning',
                        button: false,
                        html: 'Member ini sedang dalam <b>cuti!</b>'
                    });
                }else if(obj.data.status == 4){
                    Swal.fire({
                        icon: 'warning',
                        button: false,
                        html: 'Member ini telah <b>expired!</b>'
                    });
                }

                setCheckinStatusNormal("#checkinBtn");
            }else if(obj.data.checkin_status == true){
                Swal.fire({
                    icon: 'warning',
                    button: false,
                    html: 'ID Member ditemukan dan belum di <b>checkout</b>!'
                });

                setCheckinStatusNormal("#checkinBtn");
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

                $("#modal-view").modal("show");
                setCheckinStatusNormal("#checkinBtn");
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

    function setCheckinStatusLoad(element){
        $(element).html('<span class="fas fa-sync fa-spin fa-sm mr-1"></span> Checking...');
        $(element).attr("disabled", true);
    }

    function setCheckinStatusNormal(element){
        $(element).html('<span class="fas fa-search fa-sm mr-1"></span> Check');
        $(element).attr("disabled", false);
    }

    @endsection
</script>
