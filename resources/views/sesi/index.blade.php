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
                    <div class="col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-address-card mr-1"></i> Total Paket Personal Trainer</h6>
                        <h2>{{ $jSesi }}</h2>
                    </div>
                    <div class="col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Paket Personal Trainer Aktif</h6>
                        <h2>{{ $sesiAktif }}</h2>
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
                        <a href="#modal-sesi" data-toggle="modal" onclick="newSesiModal();" class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-plus fa-xs mr-1"></i> Tambah Paket PT
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
                        <div class="float-right m-2">
                            @include('config.filter.filter_sesi')
                        </div>
                    </div>
                </div>
                <table id="data_sesi" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama Paket</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Jumlah Sesi</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Harga (Rp.)</th>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-sesi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <i id="SesiModalIcon" class="fas fa-plus fa-sm mr-1"></i><span id="SesiModalTitle"></span> Paket PT Baru
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="SesiForm" action="{{ $url }}" method="POST">
                    <div class="modal-body" id="modal-pt-content">
                        <input type="hidden" name="hiddenID" id="hiddenID" readonly>
                        @csrf

                        <div class="form-group row">
                            <label for="SesiTitle" class="col-sm-3 col-form-label font-weight-normal">Judul</label>
                            <div class="col-sm-9">
                                <input type="text" id="SesiTitle" name="SesiTitle" class="form-control" autocomplete="off" placeholder="Judul (optional)">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="SesiDuration" class="col-sm-3 col-form-label font-weight-normal">Jumlah Sesi<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" id="SesiDuration" name="SesiDuration" class="form-control" min="1" value="1" autocomplete="off" placeholder="Jumlah sesi paket">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="SesiPrice" class="col-sm-3 col-form-label font-weight-normal">Harga (Rp.)<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" id="SesiPrice" name="SesiPrice" class="form-control" min="0" value="0" autocomplete="off" placeholder="Harga paket">
                            </div>
                        </div>
                        <div class="form-group row" id="SesiStatusToggler">
                            <label for="SesiStatus" class="col-sm-3 col-form-label font-weight-normal">Status<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="SesiStatus" name="SesiStatus">
                                    <?php
                                    foreach($sesiStatus as $s){ ;?>
                                    <option value="{{ $s->gstatus_id }}">{{ $s->status }}</option><?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pl-2 pr-2 pt-1 pb-1" id="SesiFormButtonGroup" style="padding: 0; border-top: 1px solid #dde0e6!important;">
                        <button type="button" class="btn btn-outline-dark" style="padding-top: 4px; padding-bottom: 4px;" data-dismiss="modal"><i class="fas fa-times fa-sm"></i> Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveSesi" style="padding-top: 4px; padding-bottom: 4px;">
                            <i class="fas fa-save fa-sm mr-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
    const data_sesi = settingDatatablesSesi();

    $("#data_sesi_length").appendTo("#orderContainer");
    $("#data_sesi_filter").appendTo("#searchContainer");
    $("#data_sesi_info").addClass("pt-2 pl-2");
    $("#data_sesi_paginate").addClass("float-right");

    $("#tableFilterSesiStatus").on("change", function () {
        data_sesi.column($(this).data('column')).search($(this).val()).draw();
    });

    var mode = "create";

    function newSesiModal(){
        $("#SesiModalTitle").html("Tambah");
        $("#SesiModalIcon").removeClass("fa-edit");
        $("#SesiModalIcon").addClass("fa-plus");
        $("#SesiStatusToggler").hide();

        $("#saveSesi").html('<i class="fas fa-save fa-sm mr-1"></i> Save');
        $("#deleteSesi").remove();

        $("#sHiddenID").val("");
        $("#SesiForm").attr("action", "{{ $url }}");

        mode = "create";
        $("#SesiForm").trigger("reset");
    }

    function editSesi(id){
        $("#SesiModalTitle").html("Ubah");
        $("#SesiModalIcon").removeClass("fa-plus");
        $("#SesiModalIcon").addClass("fa-edit");
        $("#SesiStatusToggler").show();

        $("#saveSesi").html(
            '<i class="fas fa-edit fa-sm mr-1"></i> Ubah'
        );

        mode = "edit";
        $("#SesiForm").trigger("reset");
    }

    function editDataOf(id){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('sesi.edit') }}",
            data: {
                id: id,
            },
            success: function(data){
                var obj = JSON.parse(data);

                $("#hiddenID").val(obj.data.id);

                $("#SesiTitle").val(obj.data.title);
                $("#SesiDuration").val(obj.data.duration);
                $("#SesiPrice").val(obj.data.price);
                $("#SesiStatus").val(obj.data.status);

                if ($("#SesiFormButtonGroup").find("#deletePT").length == 0){
                    $("#SesiFormButtonGroup").prepend(obj.delete_button);
                }

                $("#SesiForm").attr("action", obj.url);
            }
        });
    }

    function settingDatatablesSesi(){
        return $("#data_sesi").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            ajax: "{{ route('sesi.getSesiData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'title', name: 'title' },
                { data: 'duration', name: 'duration' },
                { data: 'price', name: 'price' },
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
            language: { search: "", searchPlaceholder: "Cari...", lengthMenu: "_MENU_" }
        });
    }

    $("#SesiForm").on('keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            if($("#SesiDuration").val() == ""){
                messagingErrorCustomTimer("Jumlah Sesi Belum Diisi!");
            }else{
                if($("#SesiPrice").val() == ""){
                    messagingErrorCustomTimer("Harga Paket Belum Diisi!");
                }else{
                    checkingForm();
                }
            }
        }
    });

    $("#saveSesi").on('click', function(){
        if($("#SesiDuration").val() == ""){
            messagingErrorCustomTimer("Jumlah Sesi Belum Diisi!");
        }else{
            if($("#SesiPrice").val() == ""){
                messagingErrorCustomTimer("Harga Paket Belum Diisi!");
            }else{
                checkingForm();
            }
        }
    });

    function checkingForm(){
        if($("#SesiDuration").val() < 1){
            messagingErrorCustomTimer("Jumlah Sesi Tidak Boleh Kurang Dari 1!");
        }else{
            const DestroySwal = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary mr-2',
                    cancelButton: 'btn btn-outline-secondary mr-2'
                },
                buttonsStyling: false
            });

            var confirmMessage = "";
            var confirmBtn = "";

            if(mode == "create"){
                confirmMessage = "Apakah Anda yakin ingin menambahkan Paket PT ini?";
                confirmBtn = `<i class="fas fa-check fa-xs mr-1"></i> Tambah`;
            }else if(mode == "edit"){
                confirmMessage = "Apakah Anda yakin ingin mengubah Paket PT ini?";
                confirmBtn = `<i class="fas fa-edit fa-xs mr-1"></i> Ubah`;
            }

            DestroySwal.fire({
                icon: 'warning',
                html: confirmMessage,
                showCancelButton: true,
                cancelButtonText: `<i class="fas fa-arrow-left fa-xs"></i> Kembali`,
                confirmButtonText: confirmBtn,
                reverseButtons: true
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel
                ) {
                    return false;
                }else {
                    $("#SesiForm").submit();
                }
            });
        }
    }

    function deleteConfirmation(){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-secondary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin menghapus data ini? <br><small class="font-italic">(<b>Perhatian!</b> Data yang telah dihapus tidak dapat dikembalikan)</small>',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-trash fa-xs"></i> Hapus`,
            confirmButtonText: `Batal`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel
            ) {
                $("#SesiForm").attr("action", "{{ Route('sesi.deleteSesi') }}");
                $("#SesiForm").submit();
            }else{
                return false;
            }
        });
    }
    @endsection
</script>
