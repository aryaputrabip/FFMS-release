@extends($app_layout)

<style>
    @section('css')
    .col-photo{
        -ms-flex: 0 0 270px;
        flex: 0 0 270px;
    }
    @endsection
</style>

@section('bg')
    <div style="background-color: #FFFFFF; min-width: 100vw; min-height: 100vh; position:absolute;"></div>
@endsection

@section('content')
    <div class="container-fluid ml-2">
        <div class="row">
            <div class="col-photo">
                <img @if(isset($data->photo)) src="{{ $data->photo }}" @endisset width="250" height="250" data-target="#webcamModal" data-toggle="modal" data-backdrop="static" style="background-color: gray;" onclick="openWebcam();" id="photo">
                <button type="button" class="btn btn-danger mb-2 mt-3" style="width: 250px;" id="editBtn" onclick="edit();">
                    <i class="fas fa-edit fa-sm mr-1"></i> Ubah
                </button>
                <a @if($role == 1) href="{{ route('suadmin.member.index') }}" @elseif($role == 2) href="#"
                   @elseif($role == 3) href="{{ route('cs.member.index') }}" @endif type="button"
                   class="btn btn-outline-dark mb-2" style="width: 250px;">
                    <i class="fas fa-times fa-sm mr-1"></i> Batal
                </a>
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
                        <li class="nav-item">
                            <a class="nav-link" id="member-manage-accounts-tab" data-toggle="pill" href="#member-manage-accounts" role="tab" aria-controls="member-manage-accounts-messages" aria-selected="false">Akun</a>
                        </li>
                    </ul>
                    <div class="card-body">
                        <div class="tab-content" id="custom-content-below-tabContent">
                            <div class="tab-pane fade show active" id="member-manage-detail" role="tabpanel" aria-labelledby="member-manage-detail-tab">
                                <form id="editMemberForm" method="POST" action="{{ route('member.update') }}" class="row pb-0">
                                    <h3 class="text-left mt-0 mb-2 col-12 mb-2">Informasi Member<br><small style="font-size: 18px;">ID Member : <span>{{ $data->member_id }}</span></small></h3>

                                    <input type="hidden" name="hiddenID" id="hiddenID" value="{{ $data->member_id }}" readonly>
                                    {{ csrf_field() }}
                                    <div class="col-md-6 mt-4 pr-3">
                                        <h6><b>IDENTITAS</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>Nama Lengkap</b></h6>
                                        <input type="text" class="mb-3 w-100 form-control" id="dataNama" name="dataNama" value="@if(isset($data)) {{$data->name}} @endisset">

                                        <h6><b>Jenis Kelamin</b></h6>
                                        <select class="form-control select2 mb-3 w-100" style="width: 100%;" id="dataGender" name="dataGender">
                                            <option value="Laki-laki" @if(isset($data) && $data->gender == 'Laki-laki') selected="selected" @endisset>Laki-laki</option>
                                            <option value="Perempuan" @if(isset($data) && $data->gender == 'Perempuan') selected="selected" @endisset>Perempuan</option>
                                        </select>

                                        <h6><b>Pekerjaan</b></h6>
                                        <select class="form-control select2 mb-3 w-100" style="width: 100%;" id="dataJob" name="dataJob">
                                            <option value="Karyawan" @if(isset($data) && $data->job == 'Karyawan') selected="selected" @endisset>Karyawan</option>
                                            <option value="Ibu Rumah Tangga" @if(isset($data) && $data->job == 'Ibu Rumah Tangga') selected="selected" @endisset>Ibu Rumah Tangga</option>
                                            <option value="Lainnya" @if(isset($data) && $data->job == 'Lainnya') selected="selected" @endisset>Lainnya...</option>
                                        </select>

                                        <h6><b>Perusahaan/Instansi</b></h6>
                                        <input type="text" class="mb-3 w-100 form-control" id="dataCompany" name="dataCompany" value="@if(isset($data)) {{$data->company}} @endisset">
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <h6><b>KONTAK</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>No. Telp.</b></h6>
                                        <input type="text" class="mb-3 w-100 form-control" id="dataPhone" name="dataPhone" value="@if(isset($data)) {{$data->phone}} @endisset">

                                        <h6><b>Email</b></h6>
                                        <input type="email" class="mb-3 w-100 form-control" id="dataEmail" name="dataEmail" value="@if(isset($data)) {{$data->email}} @endisset">
                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <h6><b>ADDITIONAL INFO</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>Marketing</b></h6>
                                        <select class="form-control select2" style="width: 100%;" id="dataMarketing" name="dataMarketing">
                                            <option value="nothing" @if(isset($marketing)) @else selected @endisset> - </option>
                                            <?php
                                            foreach($marketingList as $marketing_boy){?>
                                            <option value="{{ $marketing_boy->mark_id }}" @if(isset($marketing)) @if($marketing_boy->mark_id == $marketing->mark_id) selected @endif @endisset data-name="{{ $marketing_boy->name }}">{{ $marketing_boy->name }}</option><?php
                                            }?>
                                        </select><br>

                                        <h6><b>Personal Trainer</b></h6>
                                        <select class="form-control select2" style="width: 100%;" id="dataPT" name="dataPT">
                                            <option value="nothing" @if(isset($pt)) @else selected @endisset> - </option>
                                            <?php
                                            foreach($ptList as $pt_boy){?>
                                            <option value="{{ $pt_boy->pt_id }}" @if(isset($pt)) @if($pt_boy->pt_id == $pt->pt_id) selected @endif @endisset data-name="{{ $pt_boy->name }}">{{ $pt_boy->name }}</option><?php
                                            }?>
                                        </select>
                                    </div>

                                    <input type="hidden" id="photoFile" name="photoFile" value="{{ $data->photo }}" readonly>
                                    <input type="hidden" id="cacheMarketing" name="cacheMarketing" readonly>
                                    <input type="hidden" id="cachePT" name="cachePT" readonly>
                                </form>
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
                            <div class="tab-pane fade" id="member-manage-accounts" role="tabpanel" aria-labelledby="member-manage-accounts-tab">
                                <div class="row pb-0">
                                    {{--                                    <div class="col-12">--}}
                                    {{--                                        <h3 class="text-left mt-0 mb-2 col-12 mb-2">Pengeluaran</h3>--}}
                                    {{--                                        <hr>--}}
                                    {{--                                        <h2>CHART_HERE</h2>--}}
                                    {{--                                    </div>--}}
                                    <div class="col-12">
                                        <h3 class="text-left mt-0 mb-2 col-12 mb-2">Riwayat Member</h3>
                                        <hr>
                                        <table id="accountHistoryTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                            <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal</th>
                                                <th class="align-middle">Deskripsi</th>
                                                <th class="align-middle">Kategori</th>
                                                <th class="align-middle">Status</th>
                                                <th class="align-middle">Transaksi</th>
                                                <th class="align-middle">Aksi</th>
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
@endsection

@section('modal')
    @include('member.plugin.webcam')
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
@endsection

<script>
    @section('script')
    $(function () {

        $("#membershipTable").DataTable({
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberMembership', $data->member_id ) }}",
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

        $("#ptTable").DataTable({
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberPT', $data->member_id ) }}",
            iDisplayLength: 10,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'gender', name: 'gender' },
                { data: 'jsession', name: 'jsession' },
                { data: 'session_left', name: 'session_left' },
            ],
        });

        $('#accountHistoryTable').DataTable({
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberLog', $data->member_id ) }}",
            iDisplayLength: 10,
            columns: [
                { data: 'date', name: 'date' },
                { data: 'desc', name: 'desc' },
                { data: 'category', name: 'category' },
                { data: 'status', name: 'status' },
                { data: 'transaction', name: 'transaction' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });

        var nextMonth = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".$membership->duration ." month")) }});
        var nextMonth2 = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".($membership->duration-1)." month")) }});
        var date = new Date({{ $data->membership_sdate }});

        $("#dataMarketing").on("change", function() {
            $("#cacheMarketing").val($(this).find(':selected').data('name'));
        });

        $("#dataPT").on("change", function() {
            $("#cachePT").val($(this).find(':selected').data('name'));
        });
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    function messagingError(){
        Toast.fire({
            icon: 'error',
            html: 'Data belum lengkap!'
        })
    }

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',
            html: message
        })
    }

    function edit(){
        if($("#dataNama").val() == "" || $("#dataGender").val() == "" || $("#dataJob").val() == "" ||
            $("#dataPhone").val() == "" || $("#dataEmail").val() == ""){
            messagingError();
        }else{
            if(isEmail($("#dataEmail").val())){
                const EditSwal = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-primary mr-2',
                        cancelButton: 'btn btn-danger mr-2'
                    },
                    buttonsStyling: false
                })

                EditSwal.fire({
                    icon: 'warning',
                    html: 'Apakah Anda yakin ingin mengubah data ini?',
                    showCancelButton: true,
                    cancelButtonText: `Batal`,
                    confirmButtonText: `<i class="fas fa-edit fa-sm"></i> Ubah`,
                    reverseButtons: true
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.confirm
                    ) {
                        $("#editMemberForm").submit();
                    }else{
                        return false;
                    }
                });
            }else{
                messagingErrorCustom('Format Email Tidak Sesuai!');
            }
        }
    }

    function openWebcam(){
        var videocam = document.querySelector("#memberCapture");

        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    videocam.srcObject = stream;
                })
                .catch(function (err0r) {
                    console.log("Something went wrong!");
                });
        }
    }

    function closeCam(){
        var videocam = document.querySelector("#memberCapture");
        var stream = videocam.srcObject;
        var tracks = stream.getTracks();

        for (var i = 0; i < tracks.length; i++) {
            var track = tracks[i];
            track.stop();
        }

        videocam.srcObject = null;
    }

    video = document.getElementById('memberCapture');
    canvas = document.getElementById('canvas');
    photo = document.getElementById('photo');
    width = 400;
    height = 400;

    function takePicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
            closeCam();
            $("#webcamModal").modal('hide');
            $("#photoFile").val(data);
        } else {
            clearPhoto();
            closeCam();
            $("#webcamModal").modal('hide');
        }
    }

    function clearPhoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
    }

    function isEmail(email){
        return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
    }
    @endsection
</script>
