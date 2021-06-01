@extends($app_layout)

<style>
    @section('css')
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

    .btn-light, .badge-light{
        border: 1px solid #c2c2c2;
    }

    .dataTables_length label{
        margin: 0;
    }

    .table-left-action tbody tr td:last-child{
        padding-right: 0 !important;
    }

    .col-sm-12{
        padding: 0;
    }

    @endsection
</style>

@section('content')
    <div class="container-fluid @if($role == 3) bg-white p-3 @endif">

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
                    <input id="searchInput" type="text" class="form-control action-input capsule-group mr-2" placeholder="Search Member">

                    <button class="btn btn-light w-100 ml-0" data-target="dropdownFilter" onclick="showDropdown(this);">
                        <i class="fas fa-filter fa-sm"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg mr-2" id="dropdownFilter" data-active="false">
                        <p class="pt-3 pl-3 pr-3 pb-2">
                            <i class="fas fa-filter fa-sm mr-1"></i> Filter By
                        </p>
                        <div class="pl-3 pr-3 pb-3">
                            <label for="filterStatus" class="col-form-label font-weight-normal">Personal Trainer</label>
                            <select class="form-control" id="filterPT" name="filterPT">
                                <option class="font-weight-bold" value="" selected>ALL</option>
                                <?php
                                foreach($ptdata as $pt){?>
                                    <option value="{{ $pt->name }}">{{ $pt->name }}</option><?php
                                }?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF HEADER SECTION -->

        <!-- CONTENT SECTION -->
        <div class="row mt-4">
            <div class="col-6">
                <div class="card overflow-auto">
                    <div class="card-body p-0" style="min-height: 500px;">
                        <table id="data_member" class="table table-bordered table-left-action table-hover text-nowrap w-100 pr-0">
                            <thead>
                            <tr>
                                <th colspan="5" class="text-center">
                                    <p class="float-left mt-1 mb-0">DATA MEMBER</p>
                                    <span class="float-right" id="data_member_order_container"></span>
                                </th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Member ID</th>
                                <th>Nama</th>
                                <th>Personal Trainer</th>
                                <th class="pl-0 pr-0"></th>
                            </tr>
                            </thead>
                        </table>

                        <div id="table_footer_container">
                            <span id="table_entries_label_container" class="text-center"></span>
                            <div class="float-right" id="table_entries_order_container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card overflow-auto">
                    <div class="card-body p-0" style="min-height: 500px;">
                        <form action="{{ route('sesi.useSesi') }}" method="POST" id="formSessionSubmit">
                            @csrf

                            <table id="data_member_sesi" class="table table-bordered text-nowrap w-100">
                                <thead>
                                <tr>
                                    <th colspan="7" class="text-center">
                                        <p class="float-left mt-1 mb-0">LIST UPDATE SESI</p>
                                        <span class="float-right ml-2">
                                            <button type="button" class="btn btn-primary btn-sm ml" id="updateSesi" disabled="true">
                                                <i class="fa fa-check fa-sm mr-1"></i> Update Sesi
                                            </button>
                                        </span>
                                        <span class="float-right" id="data_member_sesi_order_container"></span>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="pl-0 pr-0"></th>
                                    <th>No</th>
                                    <th>Member ID</th>
                                    <th>Nama</th>
                                    <th>Personal Trainer
                                    <th>Sisa Sesi</th>
                                    <th>Use</th>
                                </tr>
                                </thead>
                            </table>

                            <div id="table_sesi_footer_container">
                                <span id="table_sesi_entries_label_container" class="text-center"></span>
                                <div class="float-right" id="table_sesi_entries_order_container"></div>
                            </div>

                            <input type="hidden" id="listdata" name="listdata" readonly>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- END OF CONTENT SECTION -->
    </div>
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
    @include('config.session.request_session')

    @include('config.swal.swal_message')
@endsection

<script>
    @section('script')

    //INIT DATATABLE
    const data_member = settingDatatablesMember();
    const data_member_sesi = settingDatatablesSesi();

    $("#data_member_length").appendTo("#data_member_order_container");
    $("#data_member_info").appendTo("#table_entries_label_container");
    $("#data_member_paginate").appendTo("#table_entries_order_container");
    $("#data_member_filter").hide();

    $("#data_member_sesi_length").appendTo("#data_member_sesi_order_container");
    $("#data_member_sesi_info").appendTo("#table_sesi_entries_label_container");
    $("#data_member_sesi_paginate").appendTo("#table_sesi_entries_order_container");
    $("#data_member_sesi_filter").hide();

    let updateList = [];

    function settingDatatablesMember(){
        return $("#data_member").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            ajax: "{{ route('sesi.getMemberData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'member_id', name: 'member_id' },
                { data: 'name', name: 'name' },
                { data: 'pt', name: 'pt' },
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
            language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "_MENU_" }
        });
    }

    function settingDatatablesSesi(){
        return $("#data_member_sesi").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            iDisplayLength: 10,
            columns: [
                {
                    data: 'action_reverse',
                    name: 'action_reverse',
                    orderable: false,
                    searchable: false
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'member_id', name: 'member_id' },
                { data: 'name', name: 'name' },
                { data: 'pt', name: 'pt' },
                { data: 'session', name: 'session' },
                {
                    data: 'session_use',
                    name: 'session_use',
                    orderable: false,
                    searchable: false
                },
            ],
            columnDefs: [
                {"className": "dt-center", "targets": "DT_RowIndex"}
            ],
            language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "_MENU_" }
        });
    }

    $("#searchInput").on("keyup", function() {
        data_member.search($(this).val()).draw();
        data_member_sesi.search($(this).val()).draw();
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

    $("#filterPT").on("change", function(){
        if (data_member.column(3).search() !== $(this).val()) {
            data_member.column(3).search($(this).val()).draw();
        }
        if (data_member_sesi.column(4).search() !== $(this).val()) {
            data_member_sesi.column(4).search($(this).val()).draw();
        }
    });

    function selectMember(data){
        const dtable = $(data).parents('table').last();
        const row = $(data).parents('tr').last();

        if($(dtable).attr("id") == "data_member"){
            updateListData('add', $(data).data("member"));

            const addRow = data_member.row($(row));
            data_member_sesi.row.add(addRow.data()).draw();
            addRow.remove().draw();
        }else{
            updateListData('remove', $(data).data("member"));

            const addRow = data_member_sesi.row($(row));
            data_member.row.add(addRow.data()).draw();
            addRow.remove().draw();
        }

        if(data_member_sesi.data().count() < 1){
            $("#updateSesi").attr("disabled", true);
        }else{
            $("#updateSesi").attr("disabled", false);
        }

        //const addRow = data_member.row();
    }

    function updateListData(action, data){
        if(action == "add"){
            if(!updateList.includes(data)){
                updateList.push(data);
                updateList.push(1);
            }
        }else if(action == "remove"){
            const index = updateList.indexOf(data);

            if (index > -1) {
                updateList.splice((index + 1), 1);
            }
            updateList = updateList.filter(e => e !== data);
        }
    }

    $(".sesi_source_input").on("keyup", function () {

    });

    function addSesi(source){
        const index = updateList.indexOf($(source).data("member"));
        updateList[index+1] = $(source).val();
    }

    $("#updateSesi").on("click", function() {
        const checkValue = updateList.filter(item => item < 1);

        if(updateList.includes("")){
            messagingErrorCustomTimer("Terdapat Sesi Kosong!");
        }else{
            if(checkValue.length > 0){
                messagingErrorCustomTimer("Sesi tidak boleh kurang dari 1!");
            }else{
                if(data_member_sesi.data().count() < 1){
                    messagingErrorCustomTimer("Oops! Error Encountered");
                }else{
                    const DestroySwal = Swal.mixin({
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-outline-secondary mr-2'
                        },
                        buttonsStyling: false
                    });

                    DestroySwal.fire({
                        icon: 'question',
                        html: '<b>Gunakan Sesi</b> Member-member yang dipilih ?',
                        showCancelButton: true,
                        cancelButtonText: `<i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali`,
                        confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Gunakan`,
                        reverseButtons: true
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.confirm){
                            $("#listdata").val(updateList);
                            $("#formSessionSubmit").submit();
                        }else{
                            return false;
                        }
                    });
                }
            }
        }
    });

    @endsection
</script>
