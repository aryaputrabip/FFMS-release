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
                        <h6 class="mb-0"><i class="fas fa-address-card mr-1"></i> Jumlah Marketing</h6>
                        <h2>{{ $jMarketing }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Marketing Aktif</h6>
                        <h2>{{ $marketingActive }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-male mr-1"></i> Laki-laki</h6>
                        <h2>{{ $marketingLK }}</h2>
                    </div>
                    <div class="col-md-3 col-6 text-center">
                        <h6 class="mb-0"><i class="fas fa-female mr-1"></i> Perempuan</h6>
                        <h2>{{ $marketingPR }}</h2>
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
                        <a href="#modal-marketing" data-toggle="modal" onclick="newMarketingModal();" class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-plus fa-xs mr-1"></i> Tambah Marketing
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
                <table id="data_marketing" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle pl-2 pr-2 w-auto" style="border-top: 0 !important;">Nama Marketing</th>
                        <th class="align-middle pl-2 pr-2 w-auto" style="border-top: 0 !important;">Jenis Kelamin</th>
                        <th class="align-middle pl-2 pr-2 w-auto" style="border-top: 0 !important;">Bergabung Sejak</th>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle pl-2 pr-2 w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-marketing">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <i class="fas fa-plus fa-sm mr-1"></i><span id="MarketingModalTitle"></span> Marketing Baru
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="marketingForm" action="{{route($url )}}" method="POST">
                    <div class="modal-body" id="modal-pt-content">
                        <input type="hidden" name="hiddenID" id="hiddenID" readonly>
                        {{ csrf_field() }}

                        <div class="form-group row">
                            <label for="marketingName" class="col-sm-3 col-form-label font-weight-normal">Nama<span class="color-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="marketingName" name="marketingName" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="marketingGender" class="col-sm-3 col-form-label font-weight-normal">Jenis Kelamin<span class="color-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="marketingGender" name="marketingGender">
                                    <option value="Laki-laki" selected>Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="marketingStatusToggler">
                            <label for="marketingStatus" class="col-sm-3 col-form-label font-weight-normal">Status<span class="color-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="marketingStatus" name="marketingStatus">
                                    <?php
                                    foreach($status as $s){
                                    $selected = "";?>
                                    <option value="{{ $s->gstatus_id }}">{{ $s->status }}</option><?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pl-2 pr-2 pt-1 pb-1" id="marketingFormButtonGroup" style="padding: 0; border-top: 1px solid #dde0e6!important;">
                        <button type="button" class="btn btn-outline-dark" style="padding-top: 4px; padding-bottom: 4px;" data-dismiss="modal"><i class="fas fa-times fa-sm"></i> Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveMarketing" style="padding-top: 4px; padding-bottom: 4px;"><i class="fas fa-save fa-sm"></i> Save</button>
                    </div>
                </form>
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
        $("#data_marketing").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('marketing.getMarketingData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'gender', name: 'gender' },
                { data: 'join_from', name: 'join_from' },
                { data: 'markStatus', name: 'markStatus' },
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

        $("#data_marketing_length").appendTo("#orderContainer");
        $("#data_marketing_filter").appendTo("#searchContainer");
        $("#data_marketing_info").addClass("pt-2 pl-2");
        $("#data_marketing_paginate").addClass("float-right");
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    function messagingSuccess(){
        Toast.fire({
            icon: 'info',
            html: '<span>Menyimpan Data...</span>'
        })
    }

    function messagingError(){
        Toast.fire({
            icon: 'error',
            html: 'Data belum lengkap!'
        })
    }

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

    function newMarketingModal(){
        $("#marketingStatusToggler").hide();
        $("#deleteMarketing").remove();
        $("#marketingModalTitle").html("Tambah");
        $("#marketingModalIco").removeClass("fa-user-edit");
        $("#marketingModalIco").addClass("fa-user-plus");
        $("#saveMarketing").html('<i class="fas fa-save fa-sm"></i> Simpan');

        $("#marketingForm").prop("action", "{{ route('marketing.store') }}");

        resetForm();
    }

    function marketingEditMode(){
        $("#marketingStatusToggler").show();
        $("#marketingModalIco").removeClass("fa-user-plus");
        $("#marketingModalIco").addClass("fa-user-edit");
        $("#marketingModalTitle").html("Ubah");
        $("#saveMarketing").html('<i class="fas fa-edit fa-sm"></i> Ubah');

        resetForm();
    }

    $("#saveMarketing").on('click', function(){
        if(!requiredMarketingData()){
            messagingError();
        }else{
            messagingSuccess();
            $("#marketingModal").modal('toggle');
            $("#marketingAdd").prop('disabled', true);
            $("#marketingAdd").html('<span class="fa fa-sync fa-plus mr-2"></span>Menambahkan...');
            $("#marketingForm").submit();
        }
    });

    function requiredMarketingData(){
        if($("#marketingName").val() == ""){
            return false;
        }else{
            return true;
        }
    }

    function resetForm() {
        $("#marketingForm").trigger("reset");
    }

    function editDataOf(id){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('marketing.edit') }}",
            data: {
                mark_id: id,
            },
            success: function(data){
                var obj = JSON.parse(data);

                $("#marketingName").val(obj.data.name);
                $("#hiddenID").val(obj.data.mark_id);
                $("#marketingGender option:selected").removeAttr("selected");
                $("#marketingStatus option:selected").removeAttr("selected");
                $("#marketingGender option[value='"+obj.data.gender+"']").attr("selected", "selected");
                $("#marketingStatus option[value='"+obj.data.status+"']").attr("selected", "selected");
                if ($("#marketingFormButtonGroup").find("#deleteMarketing").length == 0){
                    $("#marketingFormButtonGroup").prepend(obj.delete_button);
                }
                $("#marketingForm").attr("action", obj.url);
            }
        });
    }

    function destroy(uid){
        var token = '{{ csrf_token() }}';

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
            cancelButtonText: `<i class="fas fa-trash fa-sm"></i> Hapus`,
            confirmButtonText: `Batal`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel
            ) {
                deleteConfirmation(uid, token);
            }else{
                return false;
            }
        });
    }

    function deleteConfirmation(uid, token){
        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-secondary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: 'Data Marketing pada setiap Member yang berkaitan dengan <Marketing></Marketing> ini akan dihapus. <br><small class="font-italic font-weight-bold">Apakah Anda yakin?</small>',
            showCancelButton: true,
            cancelButtonText: `<i class="fas fa-trash fa-xs"></i> Konfirmasi`,
            confirmButtonText: `Batal`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel
            ) {
                $.post("{{ route('marketing.destroy') }}", { mark_id:uid, _token:token}, function(data){
                    location.reload();
                })
            }else{
                return false;
            }
        });
    }
    @endsection
</script>
