@extends($app_layout)

<style>
    @section('css')
    .capsule-design{
        border-radius: 20px;
    }

    .action-button{
        min-width: 7rem;
    }

    .action-input{
        min-width: 13rem;
    }

    .capsule-group{
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: 0;
    }

    .input-group-text{
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
        background-color: #FFFFFF;
        padding-right: 0;
    }

    .color-dark{
        color: #4c4c4c;
    }

    .color-darker{
        color: #262626;
    }

    .btn-link{
        color: #262626;
        font-weight: bold;
    }

    .btn-link:hover{
        color: #dc3545;
        transition: 0.2s;
    }

    .filter-selected{
        color: #dc3545;
    }

    [data-initials]:before {
        background: #099bdd;
        color: white;
        opacity: 1;
        content: attr(data-initials);
        display: inline-block;
        font-weight: bold;
        border-radius: 50%;
        vertical-align: middle;
        width: 40px;
        height: 40px;
        line-height: 40px;
        text-align: center;
    }

    .logged-in{
        color: #28a745;
    }

    .logged-out{
        color: #dc3545;
    }

    .btn-light, .badge-light{
        border: 1px solid #c2c2c2;
    }

    .badge-filter{
        font-size: 16px;
    }

    @endsection
</style>

@section('content')
    <div class="container-fluid">

        <!-- HEADER SECTION -->
        <div class="row">
            <div class="col-md-4">
                <h1 class="text-md-left text-center">{{ $title }}</h1>
            </div>
            <div class="col-md-8 text-center">
                <div class="float-md-right d-inline-flex pt-2">
                    <span class='input-group-text'>
                        <i class="fas fa-search fa-xs"></i>
                    </span>
                    <input id="searchInput" type="text" class="form-control action-input capsule-group mr-2" placeholder="Search">

                    <button class="btn btn-danger action-button w-100" id="btnAddUser">
                        <i class="fas fa-user-plus fa-sm mr-1"></i> Add User
                    </button>

                    <button class="btn btn-light w-100 ml-2" data-target="dropdownFilter" onclick="showDropdown(this);">
                        <i class="fas fa-filter fa-sm"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg mr-2" id="dropdownFilter" data-active="false">
                        <p class="pt-3 pl-3 pr-3 pb-2">
                            <i class="fas fa-filter fa-sm mr-1"></i> Filter By
                        </p>
                        <div class="pl-3 pr-3 pb-3">
                            <label for="filterStatus" class="col-form-label font-weight-normal">Status</label>
                            <select class="form-control" id="filterStatus" name="filterStatus">
                                <option class="font-weight-bold" value="" selected>ALL</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF HEADER SECTION -->

        <!-- FILTER SECTION -->
        <div class="row mt-3 mb-3">
            <div class="col text-md-left text-center">
                <button class="btn btn-link filter-selected pl-0 table-filter" data-filter="" onclick="setFilter(this)">
                    ALL
                    <span class="badge badge-danger badge-filter right ml-1 font-weight-normal">{{ $totalAdmin + $totalCS }}</span>
                </button>
                <button class="btn btn-link table-filter" data-filter="Administrator" onclick="setFilter(this)">
                    Administrator
                    <span class="badge badge-light badge-filter right ml-1 font-weight-normal">{{ $totalAdmin }}</span>
                </button>
                <button class="btn btn-link table-filter" data-filter="Customer Service" onclick="setFilter(this)">
                    Customer Service
                    <span class="badge badge-light badge-filter right ml-1 font-weight-normal">{{ $totalCS }}</span>
                </button>

                <div class="float-right mt-3">
                    <div class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox" id="dangerAreaToggler">
                        <label for="dangerAreaToggler" class="custom-control-label"></label>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF FILTER SECTION -->

        <!-- CONTENT SECTION -->
        <div class="card overflow-auto">
            <div class="card-body p-0" style="min-width: 800px;">
                <table id="data_member" class="table table-striped table-hover text-nowrap w-100">
                    <thead>
                    <tr>
                        <th class="pl-3 pr-0 pt-3 pb-2" style="width: 10%;">
                            <!-- DATATABLE ORDER CONTAINER -->
                            <div id="orderContainer"></div>
                        </th>
                        <th class="align-middle color-dark" style="width: 30%;">
                            <span class="fas fa-user fa-sm"></span>
                            <span class="mr-1">User</span>
                        </th>
                        <th class="align-middle color-dark">User Type</th>
                        <th class="align-middle color-dark">Status</th>
                        <th class="pl-0 pr-0" style="width: 5%;"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- END OF CONTENT SECTION -->

        <!-- MODAL SECTION -->
        <div class="modal fade" id="modal-user-data" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h6 class="modal-title font-weight-bold">
                            <span class="input-create-mode">
                                <i class="fas fa-user-plus fa-sm mr-1"></i> Add New User
                            </span>
                            <span class="input-edit-mode">
                                <i class="fas fa-user-edit fa-sm mr-1"></i> Edit User | <span class="member_id_copier" id="detailMember" onclick="clipboard()"> 20048413</span>
                            </span>
                        </h6>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="dataUserManagementForm" action="" method="POST">
                            @csrf
                            <input type="hidden" class="input-edit-mode" id="dataUserID" name="dataUserID" readonly>

                            <div class="form-group row">
                                <label for="dataUserName" class="col-12">
                                    Nama Pengguna / User <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <input type="text" class="form-control w-100" id="dataUserName" name="dataUserName" placeholder="Nama...">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dataUserEmail" class="col-12">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <input type="text" class="form-control w-100" id="dataUserEmail" name="dataUserEmail" placeholder="Email...">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dataUserRole" class="col-12">
                                    Hak Akses <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <select class="form-control" id="dataUserRole" name="dataUserRole">
                                        <option value="1" selected>Administrator</option>
                                        <option value="3">Customer Service</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dataUserPass" class="col-12 input-create-mode">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <label for="dataUserPass" class="col-12 input-edit-mode" style="display: none;">
                                    Change Password <span class="text-danger"></span>
                                </label>
                                <div class="col-12">
                                    <input type="password" class="form-control w-100" id="dataUserPass" name="dataUserPass" placeholder="Password...">
                                </div>
                            </div>
                            <div class="form-group row input-create-mode">
                                <label for="dataUserRePass" class="col-12">
                                    Retype Password <span class="text-danger">*</span>
                                </label>
                                <div class="col-12">
                                    <input type="password" class="form-control w-100" id="dataUserRePass" name="dataUserRePass" placeholder="Retype Password...">
                                </div>
                            </div>
                            <div class="row input-edit-mode row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="dataUserCreatedDate" class="col-md-12">
                                            Date Created
                                        </label>
                                        <div class="col-12">
                                            <input type="text" class="form-control w-100" id="dataUserCreatedDate" placeholder="Unknown" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label for="dataUserStatus" class="col-md-12">
                                            Status
                                        </label>
                                        <div class="col-12">
                                            <select class="form-control" id="dataUserStatus" name="dataUserStatus">
                                                <option value="Active" selected>Active</option>
                                                <option value="Inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-4">
                                    <button type="button" class="btn btn-light w-100" data-dismiss="modal">Close</button>
                                </div>
                                <div class="col-8">
                                    <button type="submit" class="btn btn-danger input-create-mode w-100">
                                        <i class="fas fa-user-plus fa-sm mr-1"></i> Add User
                                    </button>
                                    <button type="submit" class="btn btn-danger input-edit-mode w-100">
                                        <i class="fas fa-pencil-alt fa-sm mr-1"></i> Edit User
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF MODAL SECTION -->
    </div>
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
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

    const data_member = settingDatatables("");
    $("#data_member_length").appendTo("#orderContainer");
    $("#data_member_filter").hide();

    let selectedFilter = "";
    let formCompleted = false;
    let actionMode = "create";



    $("#searchInput").on("keyup", function() {
        data_member.search($(this).val()).draw();
    });

    $("#filterStatus").on("change", function(){
        const table = $('#data_member').DataTable();
        if (table.column(3).search() !== $(this).val()) {
            table.column(3).search($(this).val()).draw();
        }
    });

    $("#btnAddUser").on("click", function() {
        addUserMode();
    });

    $("#dangerAreaToggler").on("change", function(){
       if(this.checked){
           $(".delete-user-group").show();
           $(".edit-user-group").attr("disabled", true);
       }else{
           $(".delete-user-group").hide();
           $(".edit-user-group").attr("disabled", false);
       }
    });

    function showDropdown(data){
        const container = "#" + $(data).data('target');

        if($(container).data("active")){
            $(container).data("active",false);
            $(container).hide();
        }else{
            $(container).data("active",true);
            $(container).show();
        }
    }

    function setFilter(element){
        reselectFilterTable(element);

        selectedFilter = $(element).data("filter");

        const table = $('#data_member').DataTable();
        if (table.column(2).search() !== selectedFilter) {
            table.column(2).search(selectedFilter).draw();
        }
    }

    function reselectFilterTable(element){
        $(".table-filter").removeClass("filter-selected");
        $(".badge-filter").removeClass("badge-danger badge-light");
        $(".badge-filter").addClass("badge-light");

        $(element).addClass('filter-selected');

        badgeFilter = $(element).find('.badge-filter');
        $(badgeFilter).removeClass('badge-light');
        $(badgeFilter).addClass('badge-danger');
    }

    function settingDatatables(){
        return $("#data_member").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            "order": [[ 1, 'asc' ], [ 2, 'asc' ]],
            ajax: "{{ route('suadmin.management.userManagementData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'user', name: 'user' },
                { data: 'type', name: 'type' },
                { data: 'status', name: 'status' },
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
    }

    $("#dataUserManagementForm").submit(function(e){
        if(!formCompleted){
            if(actionMode == "create"){
                recheckForm("register-user");

                if($("#dataUserName").val() == "" || $("#dataUserEmail").val() == "" ||
                    $("#dataUserRole").val() == "" || $("#dataUserPass").val() == "" ||
                    $("#dataUserRePass").val() == ""){
                    messagingErrorCustom("Data belum lengkap!");
                }else{
                    if($("#dataUserRePass").val() === $("#dataUserPass").val() && $("#dataUserPass").val().length >= 8){
                        if(isEmail($("#dataUserEmail").val())){

                            const DestroySwal = Swal.mixin({
                                customClass: {
                                    confirmButton: 'btn btn-danger',
                                    cancelButton: 'btn btn-outline-secondary mr-2'
                                },
                                buttonsStyling: false
                            });

                            DestroySwal.fire({
                                icon: 'question',
                                html: 'Apakah Anda yakin untuk menambahkan user ini ?',
                                showCancelButton: true,
                                cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
                                confirmButtonText: `<i class="fas fa-plus fa-sm mr-1"></i> Tambahkan`,
                                reverseButtons: true
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.confirm){
                                    checkEmailBeforeSubmit($("#dataUserEmail").val());
                                }else{
                                    return false;
                                }
                            });

                        }else{
                            invalidEmail();
                        }
                    }else{
                        if($("#dataUserPass").val().length >= 8){
                            invalidPassword("Password tidak sama!");
                        }else{
                            invalidPassword("Min Password : 8 karakter");
                        }
                    }
                }
            }else if(actionMode == "edit"){
                recheckForm("edit-user");

                if($("#dataUserName").val() == "" || $("#dataUserEmail").val() == "" ||
                    $("#dataUserRole").val() == ""  || $("#dataUserStatus").val() == ""){
                    messagingErrorCustom("Data belum lengkap!");
                }else{
                    var passValid = false;

                    if($("#dataUserPass").val() != ""){
                        if($("#dataUserPass").val().length >= 8){
                            passValid = true;
                        }else{
                            invalidPassword("Min Password : 8 karakter");
                        }
                    }else{
                        passValid = true;
                    }

                    if(passValid){
                        if(isEmail($("#dataUserEmail").val())){

                            const DestroySwal = Swal.mixin({
                                customClass: {
                                    confirmButton: 'btn btn-danger',
                                    cancelButton: 'btn btn-outline-secondary mr-2'
                                },
                                buttonsStyling: false
                            });

                            DestroySwal.fire({
                                icon: 'question',
                                html: 'Apakah Anda yakin untuk mengubah user ini ?',
                                showCancelButton: true,
                                cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
                                confirmButtonText: `<i class="fas fa-pencil-alt fa-sm mr-1"></i> Ubah`,
                                reverseButtons: true
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.confirm){
                                    checkEmailBeforeSubmit($("#dataUserEmail").val());
                                }else{
                                    return false;
                                }
                            });

                        }else{
                            invalidEmail();
                        }
                    }
                }
            }
            e.preventDefault();
        }
    });

    function checkEmailBeforeSubmit(email){
        if(actionMode == "create"){
            $.ajax({
                type: 'GET',
                dataType: 'html',
                url: "{{ route('suadmin.management.checkIsUserAvailable') }}",
                data: {
                    email: email,
                },
                success: function(data){
                    confirmDataAction(data);
                }
            });
        }else{
            confirmDataAction(0);
        }
    }

    function confirmDataAction(data){
        var url;
        if(actionMode == "create"){
            url = '{{ route('suadmin.management.addUser') }}';

            if(data == 0){
                $("#dataUserManagementForm").attr('action', url);
                formCompleted = true;
                $("#dataUserManagementForm").submit();
            }else{
                $($("#dataUserEmail").addClass("is-invalid"));
                messagingErrorCustom("Email sudah digunakan!");
            }
        }else if(actionMode == "edit"){
            url = '{{ route('suadmin.management.editUser') }}';

            $("#dataUserManagementForm").attr('action', url);
            formCompleted = true;
            $("#dataUserManagementForm").submit();
        }else if(actionMode == "delete"){
            url = '{{ route('suadmin.management.deleteUser') }}';

            $("#dataUserManagementForm").attr('action', url);
            formCompleted = true;
            $("#dataUserManagementForm").submit();
        }
    }

    function editUser(user){
        editUserMode(user);

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('suadmin.management.getUserData') }}",
            data: {
                id: user,
            },
            success: function(data){
                var obj = JSON.parse(data);

                if(obj.data == null){
                    Swal.fire({
                        icon: 'warning',
                        button: false,
                        html: 'Whoops! Terjadi kesalahan dalam pengambilan data'
                    });
                }else{
                    $("#dataUserID").val(obj.data.id);

                    $("#dataUserName").val(obj.data.name);
                    $("#dataUserEmail").val(obj.data.email);
                    $("#dataUserCreatedDate").val(obj.data.date_created);

                    $("#dataUserRole option[value='"+obj.data.type+"']").attr("selected", "selected");
                    $("#dataUserStatus option[value='"+obj.data.status+"']").attr("selected", "selected");
                }
            }
        });
    }

    function deleteUser(id, email){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary mr-2'
            },
            buttonsStyling: false
        });

        DestroySwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin menghapus user ini ?',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
            confirmButtonText: `<i class="fas fa-trash fa-sm mr-1"></i> Hapus`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                deleteUserConfirmation(id, email);
            }else{
                return false;
            }
        });
    }

    function deleteUserConfirmation(id, email){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-outline-secondary mr-2'
            },
            buttonsStyling: false
        });

        var title =
            '<p class="text-danger" id="deleteValidationLabel" style="display: none;">' +
            'Oops! Email salah.</p> ' +
            'Mohon isikan Email User untuk mengkonfirmasi aksi! ' +
            '<b>'+ email + '</b> ' +
            '<br> ' +
            '<input type="email" class="form-control mt-2" id="deleteValidationInput">';

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
                validateDelete(id, email);
            }else{
                return false;
            }
        });
    }

    function validateDelete(id, email){
        if(email == $("#deleteValidationInput").val()){
            actionMode = "delete";
            confirmDataAction(0);
        }else{
            deleteUserConfirmation(id, email);
            $("#deleteValidationLabel").show();
            $("#deleteValidationInput").addClass("is-invalid");
        }
    }

    function addUserMode(){
        $("#dataUserManagementForm").trigger("reset");
        $("#modal-user-data").modal("show");
        actionMode = "create";

        $(".input-create-mode").show();
        $(".input-edit-mode").hide();

        $("#dataUserEmail").attr("disabled", false);
    }

    function editUserMode(user){
        $("#dataUserManagementForm").trigger("reset");
        $("#modal-user-data").modal("show");
        actionMode = "edit";

        $(".input-edit-mode").show();
        $(".input-create-mode").hide();

        $("#dataUserEmail").attr("disabled", true);
    }

    function recheckForm(action){
        if(action == "register-user"){
            const valueRequired = [
                $("#dataUserName"), $("#dataUserEmail"), $("#dataUserRole"),
                $("#dataUserPass"), $("#dataUserRePass")
            ];

            for(i=0; i<5; i++){
                if(valueRequired[i].val() == ""){
                    $(valueRequired[i].addClass("is-invalid"));
                }else{
                    $(valueRequired[i].removeClass("is-invalid"));
                }
            }
        }
    }

    function invalidPassword(message){
        $("#dataUserRePass").addClass("is-invalid");
        messagingErrorCustom(message);
    }

    function invalidEmail(){
        $("#dataUserEmail").addClass("is-invalid");
        messagingErrorCustom("Format email tidak sesuai!");
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

    function isEmail(email){
        return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
    }
    @endsection
</script>
