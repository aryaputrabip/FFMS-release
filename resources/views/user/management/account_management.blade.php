@extends($app_layout)

@section('content')
    <div class="container-fluid @if($user_data->role_id == 3) bg-white p-3 @endif">

        <!-- HEADER SECTION -->
        <div class="row">
            <div class="col-md-6">
                <h1 class="text-md-left text-center">{{ $title }}</h1>
            </div>
            <div class="col-md-6 mt-2 text-center">
                <div class="float-md-right">
                    <button class="btn btn-primary view-mode-group" onclick="editMode()">
                        <i class="fas fa-edit fa-sm mr-1"></i> Edit User
                    </button>

                    <button class="btn btn-danger edit-mode-group" onclick="viewMode()" style="display: none;">
                        <i class="fas fa-arrow-left fa-sm mr-1"></i> Batal
                    </button>
                    <button class="btn btn-primary edit-mode-group ml-1" onclick="confirmEdit()" style="display: none;">
                        <i class="fas fa-check fa-sm mr-1"></i> Ubah Data
                    </button>
                </div>
            </div>
        </div>
        <!-- END OF HEADER SECTION -->

        <!-- CONTENT SECTION -->
        <div class="card overflow-auto mt-4 pt-2 pb-2 pl-3 pr-3">
            <div class="card-body p-0">
                <h5>Account Details</h5>
                <hr class="mt-0">

                <form id="formUserAccount" action="#" method="POST">
                    @csrf

                    <div class="form-group row">
                        <label for="dataUserNama" class="col-md-2 col-form-label text-md-right font-weight-normal">
                            Nama Pengguna<span class="text-danger">*</span>
                        </label>
                        <div class="col-md-4 ml-md-4">
                            <input type="text" class="form-control edit-mode-group" id="dataUserNama" name="dataUserNama" placeholder="Nama Lengkap">
                            <h6 id="dataUserNamaView" class="view-mode-group" style="padding-top: 0.675rem; font-weight: bold; display: none;">
                                {{ $user_data->name }}
                            </h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dataUserEmail" class="col-md-2 col-form-label text-md-right font-weight-normal">
                            Email<span class="text-danger">*</span>
                        </label>
                        <div class="col-md-3 ml-md-4">
                            <h6 id="dataUserEmail" style="padding-top: 0.675rem; font-weight: bold;">
                                {{ $user_data->email }}
                            </h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dataUserRole" class="col-md-2 col-form-label text-md-right font-weight-normal">
                            Role User<span class="text-danger">*</span>
                        </label>
                        <div class="col-md-2 ml-md-4">
                            <h6 id="dataUserRole" style="padding-top: 0.675rem; font-weight: bold;">
                                {{ $user_data->role }}
                            </h6>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3 font-weight-bold edit-mode-group">Hanya isikan password jika ingin diubah.</h6>
                    <div class="form-group row edit-mode-group">
                        <label for="dataUserPass" class="col-md-2 col-form-label text-md-right font-weight-normal">
                            Password Baru
                        </label>
                        <div class="col-md-4 ml-md-4">
                            <input type="password" class="form-control" id="dataUserPass" name="dataUserPass">
                        </div>
                    </div>
                    <div class="form-group row edit-mode-group">
                        <label for="dataUserRetypePass" class="col-md-2 col-form-label text-md-right font-weight-normal">
                            Retype New Password
                        </label>
                        <div class="col-md-4 ml-md-4">
                            <input type="password" class="form-control" id="dataUserRePass" name="dataUserRePass">
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <!-- END OF CONTENT SECTION -->
    </div>
@endsection

@section('import_script')
    @include('config.session.request_session')
@endsection

<script>
    @section('script')
    //SWAL INIT
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
    });

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',html: message
        });
    }

    function messagingInfoCustom(message){
        Toast.fire({
            icon: 'info',
            html: message
        })
    }

    $(function() {
       viewMode();
    });

    function viewMode(){
        $(".view-mode-group").show();
        $(".edit-mode-group").hide();

        $("#formUserAccount").attr("action", "");
    }

    function editMode(){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('management.getAccountData') }}",
            success: function(data){
                var obj = JSON.parse(data);

                $(".edit-mode-group").show();
                $(".view-mode-group").hide();

                $("#dataUserNama").val(obj.data.name);
                $("#dataUserRole").val(obj.data.role);
                $("#dataUserPass").val("");
                $("#dataUserRePass").val("");
            }
        });
    }

    function confirmEdit(){
        if($("#dataUserPass").val() != ""){
            if($("#dataUserPass").val() == $("#dataUserRePass").val()){
                $("#dataUserRePass").removeClass("is-invalid");
                validatePassword();
            }else{
                $("#dataUserRePass").addClass("is-invalid");
                messagingErrorCustom("Password tidak sama!");
            }
        }else{
            validateEdit();
        }
    }

    function validatePassword(){
        if($("#dataUserPass").val().length < 8){
            messagingErrorCustom("Min Password : 8 karakter");
            $("#dataUserPass").addClass("is-invalid");
        }else{
            $("#dataUserPass").removeClass("is-invalid");
            validateEdit();
        }
    }

    function validateEdit(){
        if($("#dataUserNama").val() == ""){
            messagingErrorCustom("Nama Pengguna Kosong!");
            $("#dataUserNama").addClass("is-invalid");
        }else{
            $("#dataUserNama").removeClass("is-invalid");

            const DestroySwal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-secondary mr-2'
                },
                buttonsStyling: false
            });

            DestroySwal.fire({
                icon: 'warning',
                html: 'Apakah Anda yakin ingin mengubah profil Anda ?',
                showCancelButton: true,
                cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
                confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Ubah`,
                reverseButtons: true
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.confirm){
                    $("#formUserAccount").attr("action", "{{ route('management.editAccountData') }}");
                    $("#formUserAccount").submit();
                }else{
                    return false;
                }
            });
        }
    }

    @endsection
</script>
