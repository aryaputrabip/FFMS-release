@extends($app_layout)

<style>
    @section('css')
    .col-photo{
        -ms-flex: 0 0 270px;
        flex: 0 0 270px;
    }

    .attachment-block-selector:hover{
        background-color: #e8e8e8;
    }

    .block_active:hover{
        background-color: #dcedff !important;
    }

    .block_active{
        background-color: #dcedff;
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

                                        <h6><b>Catatan</b></h6>
                                        <textarea class="form-control w-100" id="dataUserNote" name="dataUserNote" rows="6" placeholder="Catatan Member...">@if(isset($data->member_notes)){{ $data->member_notes }}@endisset</textarea>

                                    </div>

                                    <div class="col-md-6 mt-4">
                                        <h6><b>ADDITIONAL INFO</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>Marketing</b></h6>
                                        <select class="form-control select2" style="width: 100%;" id="dataMarketing" name="dataMarketing">
                                            <option value="nothing" @if(isset($marketing)) @else selected @endisset> - </option>
                                            @if(isset($marketing))
                                                <option value="{{ $marketing->mark_id }}" data-name="{{ $marketing->name }}" selected>{{ $marketing->name }}</option>
                                            @endisset

                                            <?php
                                            foreach($marketingList as $marketing_boy){?>
                                            @if(isset($marketing))
                                                @if($marketing_boy->mark_id != $marketing->mark_id)
                                                    <option value="{{ $marketing_boy->mark_id }}" data-name="{{ $marketing_boy->name }}">{{ $marketing_boy->name }}</option>
                                                @endif
                                            @else
                                                <option value="{{ $marketing_boy->mark_id }}" data-name="{{ $marketing_boy->name }}">{{ $marketing_boy->name }}</option>
                                            @endisset
                                             <?php
                                            }?>
                                        </select><br>
                                    </div>

                                    <div class="col-12 mt-5">
                                        <div class="card collapsed-card">
                                            <div class="row p-3">
                                                <label class="col-sm-9 col-form-label">
                                                    Danger Area<span class="color-danger">*</span>
                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                        <i class="fas fa-caret-down"></i>
                                                    </button>
                                                </label>
                                            </div>
                                            <div class="card-body pt-0" style="overflow-y: auto; overflow-x: hidden;">
                                                <hr style="margin: 0 0 20px 0;">
                                                <h6 class="mt-2 text-red"><b>Hapus Member</b></h6>
                                                <button type="button" class="btn btn-danger w-100" onclick="deleteMember();">
                                                    <i class="fas fa-trash fa-sm mr-1"></i> Hapus Member ini
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="photoFile" name="photoFile" value="{{ $data->photo }}" readonly>
                                    <input type="hidden" id="cacheMarketing" name="cacheMarketing" readonly>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="member-manage-membership" role="tabpanel" aria-labelledby="member-manage-membership-tab">
                                <div class="row pb-0">
                                    <div class="col-8">
                                        <h3 class="text-left mt-0 mb-2 col-12 mb-2">Paket Member</h3>
                                    </div>
                                    <div class="col-4 text-right">
                                        <button type="button" class="btn btn-dark mt-1"
                                                data-toggle="modal" data-target="#modal-membership">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mt-0">
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
                                        <button type="button" class="btn btn-dark mt-3"
                                                data-toggle="modal" data-target="#modal-pt">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </button>
                                    </div>
                                    <div class="col-12">
                                        <hr class="mt-0">
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
                                        <div class="row">
                                            <h3 class="text-left col-8 mt-0 mb-2 mb-2">Riwayat Member</h3>
                                            <div class="col-4">
                                                <div class="float-right">
                                                    @include('config.filter.filter_log')
                                                </div>
                                            </div>
                                        </div>
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
    @include('member.modal.modal_edit')
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
                html: '{{Session::get("success")}}'
            })
        </script>
        <?php Session::forget('success') ?>
    @endif

    @if(Session::has('failed'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'warning',
                button: false,
                html: '{{Session::get("failed")}}'
            })
        </script>
        <?php Session::forget('failed') ?>
    @endif
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

        const log_table =
        $('#accountHistoryTable').DataTable({
            searching: true,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberLog', $data->member_id ) }}",
            iDisplayLength: 10,
            "order": [[ 0, "desc" ]],
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
        $("#accountHistoryTable_filter").hide();

        $("#tableFilterLogMemberStatus").on("change", function () {
            if (log_table.column(3).search() !== $(this).val()) {
                log_table.column(3).search($(this).val()).draw();
            }
        });

        $("#tableFilterLogMemberKategori").on("change", function () {
            if (log_table.column(2).search() !== $(this).val()) {
                log_table.column(2).search($(this).val()).draw();
            }
        });

        var nextMonth = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".$membership->duration ." month")) }});
        var nextMonth2 = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".($membership->duration-1)." month")) }});
        var date = new Date({{ $data->membership_sdate }});

        $("#dataMarketing").on("change", function() {
            $("#cacheMarketing").val($(this).find(':selected').data('name'));
        });

        $("#dataPT").on("change", function() {
            $("#cachePT").val($(this).val());
        });

        $("#addSessionConfirm").on("click", function() {
            extendSession();
            $("#modal-s-add").modal("hide");
            $("#modal-f-payment").modal("show");
            $("#payment-title").html("+ " + $("#dataUserPTSession option:selected").val() + " Sesi");
            $("#total_payment").html(asRupiah($("#dataUserPTSession option:selected").data("price")));
            $("#total_price").html(asRupiah($("#dataUserPTSession option:selected").data("price")));

            $("#confirmPayment").data("action", 'extend-session');
        });

        $("#modal-membership").on('show.bs.modal', function() {
            resetMembershipTransaction();
        });

        $("#modal-pt").on('show.bs.modal', function() {
           resetPTTransaction();
        });

        $('#paymentDebitModal').on('hide.bs.modal', function() {
            toggleModal('modal-f-payment');
        });

        $('#paymentCreditModal').on('hide.bs.modal', function() {
            toggleModal('modal-f-payment');
        });

        $("#confirmPayment").on("click", function(){
            verifyPaymentRequirement($(this).data("action"));
        });

        $("#payMembershipChange").on("click", function(){
           checkRequiredData('membership');
        });

        $("#payMembershipExtend").on("click", function(){
            $("#modal-m-extend").modal("hide");
            $("#modal-f-payment").modal("show");

            $("#payment-title").html("Paket Member <br>" + `{{ $membership->duration }}` + " Bulan<br>(" + $("#extend-membership-type").html() + ")");
            $("#total_payment").html(asRupiah({{ $membership->price }}));
            $("#total_price").html(asRupiah({{ $membership->price }}));

            $("#confirmPayment").data("action", 'extend-membership');
        });

        $("#changeApprovalBtn").on("click", function() {
           toggleModal("modal-m-change");
        });

        $("#payPTRegister").on("click", function() {
           if($("#dataPTRegSession").find(':selected').val() == null || $("#dataPTRegSession").find(':selected').val() == ""){
               messagingErrorCustom("Sesi Belum Dipilih!");
           }else{
               $("#modal-pt-add").modal("hide");
               $("#modal-f-payment").modal("show");

               var currentSessionTitle = $("#dataPTRegSession").find(':selected');

               $("#cacheRegPTID").val($("#dataPTReg").find(':selected').val());
               $("#cacheRegSessionDuration").val(currentSessionTitle.val());
               $("#cacheRegSessionGroup").val(currentSessionTitle.data("title"));
               $("#cacheRegSessionPrice").val(currentSessionTitle.data("price"));

               if(currentSessionTitle.data("title") == null || currentSessionTitle.data("title") == ""){
                   $("#payment-title").html("Paket PT <br>" + "("+ currentSessionTitle.val() +" Sesi)");
               }else{
                   $("#payment-title").html("Paket PT <br>" + "("+ currentSessionTitle.data("title") + " - <br>" + currentSessionTitle.val() +" Sesi)");
               }

               $("#total_payment").html(asRupiah(currentSessionTitle.data("price")));
               $("#total_price").html(asRupiah(currentSessionTitle.data("price")));

               $("#confirmPayment").data("action", 'register-session');
           }
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

    function deleteMember(){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary mr-2'
            },
            buttonsStyling: false
        });

        DestroySwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin menghapus member ini ? <br><br> <i>(Perhatian! Data tidak dapat dikembalikan)</i>',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
            confirmButtonText: `<i class="fas fa-trash fa-sm mr-1"></i> Hapus`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                deleteMemberConfirmation('{{ $data->member_id }}');
            }else{
                return false;
            }
        });
    }

    function deleteMemberConfirmation(id){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary mr-2'
            },
            buttonsStyling: false
        });

        var title =
            '<p class="text-danger" id="deleteValidationLabel" style="display: none;">' +
            'Oops! Member ID salah.</p> ' +
            'Mohon isikan Member ID untuk mengkonfirmasi aksi! ' +
            '<b>'+ {{ $data->member_id }} + '</b> ' +
            '<br> ' +
            '<input type="text" class="form-control mt-2" id="deleteValidationInput">';

        //CONFIRMATION DELETE STEP-2
        DestroySwal.fire({
            icon: 'warning',
            html: title,
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Batal`,
            confirmButtonText: `<i class="fas fa-trash fa-sm mr-1"></i> OK`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                validateDelete(id);
            }else{
                return false;
            }
        });
    }

    function validateDelete(id){
        if($("#deleteValidationInput").val() == id){
            $("#editMemberForm").attr("action", "{{ route('member.deleteMember') }}");
            $("#editMemberForm").submit();
        }else{
            deleteMemberConfirmation(id);
            $("#deleteValidationLabel").show();
            $("#deleteValidationInput").addClass("is-invalid");
        }
    }

    function extendSession(){

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

    function tambahSesi(){
        $("#modal-pt").modal("hide");
        $("#modal-s-add").modal("show");
    }

    function ubahPT(){
        $("#modal-pt").modal("hide");
        $("#modal-pt-change").modal("show");
    }

    function registerPT(){
        $("#modal-pt").modal("hide");
        $("#modal-pt-add").modal("show");
    }

    function selectPaymentModel(type, element){
        reselectPaymentCard(type);
        $("#cachePaymentModel").val($(element).data('payment'));
        $("#cachePaymentType").val("");

        switch(type){
            case 2:
                $('#paymentDebitModal').modal('show');
                toggleModal('modal-f-payment');
                break;

            case 3:
                $('#paymentCreditModal').modal('show');
                toggleModal('modal-f-payment');
                break;
        }
    }

    function toggleModal(modal){
        if(modal == 'payment-return'){
            if($("#confirmPayment").data("action") == 'extend-session'){
                $("#modal-s-add").modal("show");
            }else if($("#confirmPayment").data("action") == 'change-membership'){
                $("#modal-m-change").modal("show");
            }else if($("#confirmPayment").data("action") == 'register-session'){
                $("#modal-pt-add").modal("show");
            }
        }else{
            $("#"+modal).modal("toggle");
        }
    }

    function selectPaymentType(type, element){
        reselectPaymentBank(type);
        $("#cachePaymentType").val($(element).data('bank'));
    }

    var selectedBank;
    function reselectPaymentBank(selected) {
        $("#bank-"+selected).addClass('block_active');

        if(selectedBank != null){
            $(selectedBank).removeClass('block_active');
        }
        selectedBank = "#bank-"+selected;
    }

    var selectedPayment;
    function reselectPaymentCard(selected){
        $("#payment-"+selected).addClass('block_active');

        if(selectedPayment != null){
            $(selectedPayment).removeClass('block_active');
        }
        selectedPayment = "#payment-"+selected;
    }

    function verifyPaymentRequirement(action){

        if($("#cachePaymentModel").val() == ""){
            messagingErrorCustom('Jenis Pembayaran Belum Dipilih!');
        }else{
            if($("#cachePaymentModel").val() == "Cash"){
                notifyConfirmPayment(action);
            }else{
                if($("#cachePaymentType").val() != ""){
                    notifyConfirmPayment(action);
                }else{
                    messagingErrorCustom('Bank Pembayaran Belum Dipilih!');
                }
            }
        }
    }

    function notifyConfirmPayment(action){
        var token = '{{ csrf_token() }}';

        const ConfirmSwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },buttonsStyling: false
        });

        ConfirmSwal.fire({
            icon: 'warning',
            html: 'Konfirmasi Transaksi ?',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-times fa-sm mr-1"></i> Tidak`,
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Iya`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                setPaymentLoad();

                $("#sTransaction").val($("#confirmPayment").data("action"));
                $("#nPayment").val($("#cachePaymentType").val());
                $("#nBank").val($("#cachePaymentModel").val());

                if($("#confirmPayment").data("action") == 'extend-session') {
                    $("#nSession").val($("#dataUserPTSession option:selected").val());
                    $("#nPrice").val($("#dataUserPTSession option:selected").data("price"));
                    $("#nTitle").val($("#dataUserPTSession option:selected").data("title"));
                }else if($("#confirmPayment").data("action") == 'register-session'){
                    $("#nSession").val($("#dataPTRegSession option:selected").val());
                    $("#nPrice").val($("#dataPTRegSession option:selected").data("price"));
                    $("#nTitle").val($("#dataPTRegSession option:selected").data("title"));
                    $("#nPT").val($("#dataPTReg option:selected").val());
                }else if($("#confirmPayment").data("action") == 'change-membership'){
                    $("#mShipID").val($("#cacheMembershipID").val());
                    $("#mShipName").val($("#cacheMembership").val());
                    $("#mShipPrice").val($("#cacheMembershipPrice").val());
                    $("#mShipDuration").val($("#cacheMembershipDuration").val());
                    $("#mShipType").val($("#cacheMembershipType").val());
                    $("#mShipCategory").val($("#cacheMembershipCategory").val());
                    $("#mShipApproval").val(mShipApprovalPrice);
                }else if($("#confirmPayment").data("action") == 'extend-membership'){
                    $("#mShipID").val($("#extend-membership-id").val());
                    $("#mShipName").val($("#extend-membership-name").html());
                    $("#mShipPrice").val($("#extend-membership-price").data("price"));
                    $("#mShipDuration").val($("#extend-membership-duration").html());
                    $("#mShipType").val($("#extend-membership-type").html());
                }

                $("#nNotes").val($("#dataNote").val());
                $("#sAddForm").submit();
            }else{
                return false;
            }
        });
    }

    function setPaymentLoad(){
        $("#modal-f-payment-content").append(
            '<div class="overlay d-flex justify-content-center align-items-center">' +
            '   <i class="fas fa-2x fa-sync fa-spin"></i>' +
            '</div>'
        );
    }

    function setEditPTLoad(){
        $("#modal-pt-change-content").append(
            '<div class="overlay d-flex justify-content-center align-items-center">' +
            '   <i class="fas fa-2x fa-sync fa-spin"></i>' +
            '</div>'
        );
    }

    var mShipApprovalPrice;
    function setApprovalPrice(){
        $("#changeApprovalBtn").html('<i class="fas fa-pencil-alt fa-sm mr-1"></i>' + ' Rp. ' + asRupiah($("#approvalPrice").val()));
        mShipApprovalPrice = $("#approvalPrice").val();

        toggleModal('modal-m-change');
    }

    function resetMembershipTransaction(){
        $("#cacheMembershipID").val("");
        $("#cacheMembership").val("");
        $("#cacheMembershipPrice").val("");
        $("#cacheMembershipDuration").val("");
        $("#cacheMembershipType").val("");
        $("#cacheMembershipCategory").val("");

        $("#cacheMembershipAction").val("");

        $(".attachment-block-selector").removeClass("block_active");
        $("#changeApprovalBtn").prop("disabled", true);

        resetPayment();
        resetRequestTransaction();
    }

    function resetPTTransaction(){
        $("#cachePT").val("");
        $("#cacheRegSessionPrice").val("");
        $("#cacheRegSessionGroup").val("");
        $("#cacheRegPTID").val("");
        $("#cacheRegSessionDuration").val("");

        resetPayment();
        resetRequestTransaction();
    }

    function resetPayment(){
        $("#cachePaymentModel").val("");
        $("#cachePaymentType").val("");
    }

    function resetRequestTransaction(){
        $("#sTransaction").val("");
        $("#nSession").val("");
        $("#nPrice").val("");
        $("#nTitle").val("");
        $("#nPayment").val("");
        $("#nBank").val("");
        $("#nNotes").val("");
        $("#mShipID").val("");
        $("#mShipName").val("");
        $("#mShipPrice").val("");
        $("#mShipDuration").val("");
        $("#mShipType").val("");
        $("#mShipCategory").val("");
    }

    function asRupiah(value){
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'IDR',
        });

        var split = formatter.format(value).split(".00");
        var splitCurrency = split[0].split("IDR");

        return splitCurrency[1];
    }

    function editPTNameConfirm(){
        const ConfirmSwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },buttonsStyling: false
        });

        ConfirmSwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin mengubah Personal Trainer Member ini ?',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-times fa-sm mr-1"></i> Tidak`,
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Iya`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                setEditPTLoad();

                $("#cachePT").val($("#dataPT").val());

                $("#ptEditForm").submit();
            }else{
                return false;
            }
        });
    }

    function activatePaket(id, duration){
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
                $.post("{{ route('member.aktivasi') }}", {
                    id:id, _token:token, duration:duration}, function(data){
                    location.reload();
                });
            }
        });
    }

    function extendPaket(){
        $("#modal-m-extend").modal("show");
        $("#cacheMembershipAction").val("extend-membership");
    }

    function ubahPaket(){
        $("#modal-m-change").modal("show");
        $("#cacheMembershipAction").val("change-membership");
    }

    var selectedMembership;
    function selectMembership(selected){
        $("#cacheMembership").val($("#membership-"+selected+"-name").html());
        $("#cacheMembershipID").val(selected);
        $("#cacheMembershipDuration").val($("#membership-"+selected+"-duration").html());
        $("#cacheMembershipType").val($("#membership-"+selected+"-type").html());
        $("#cacheMembershipPrice").val($("#membership-"+selected+"-price").data('price'));
        $("#cacheMembershipCategory").val($("#membership-"+selected+"-category").val());
        $("#changeApprovalBtn").prop("disabled", false);

        reselectMembershipCard(selected);
    }

    function reselectMembershipCard(selected){
        $("#membership-"+selected).addClass('block_active');
        resetApprovalPrice();

        if(selectedMembership != null){
            $(selectedMembership).removeClass('block_active');
        }

        selectedMembership = "#membership-"+selected;
    }

    function resetApprovalPrice(){
        $("#changeApprovalBtn").html('<i class="fas fa-pencil-alt fa-sm mr-1"></i> Pasang Harga');
        $("#approvalPrice").val(null);
        mShipApprovalPrice = null;
    }

    function checkRequiredData(type){
        switch(type){
            case "membership":
                if($("#cacheMembershipID").val() == ""){
                    messagingErrorCustom("Paket Member Belum Dipilih!");
                }else{
                    $("#modal-m-change").modal("hide");
                    $("#modal-f-payment").modal("show");

                    $("#payment-title").html("Paket Member <br>" + $("#cacheMembershipDuration").val() + " Bulan <br>" + "("+$("#cacheMembershipType").val()+")");
                    $("#total_payment").html(asRupiah($("#cacheMembershipPrice").val()));
                    $("#total_price").html(asRupiah($("#cacheMembershipPrice").val()));

                    $("#confirmPayment").data("action", 'change-membership');

                    if(mShipApprovalPrice != null){
                        $("#total_payment").html(
                            "<i style='text-decoration: line-through;'>" +
                                asRupiah($("#cacheMembershipPrice").val()) +
                            "</i><b>" + asRupiah(mShipApprovalPrice)+"</b>"
                        );

                        $("#total_price").html(asRupiah(mShipApprovalPrice));
                    }
                }
                break;
        }
    }

    function isEmail(email){
        return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
    }
    @endsection
</script>
