@extends($app_layout)

@section('content')
    <div class="container-fluid">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modal-action" style="display: none;">
            <span class="fas fa-plus mr-1"></span> DEBUG
        </button>

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
                    <div class="col-md-6 col-12 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-users mr-1"></i> Jumlah Paket Member</h6>
                        <h2>{{ $jMembership }}</h2>
                    </div>
                    <div class="col-md-6 col-12 text-center">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Paket Member Tersedia</h6>
                        <h2>{{ $membershipActive }}</h2>
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
                        <a href="#modal-membership" data-toggle="modal" onclick="newMembershipModal();" class="btn btn-sm btn-primary mt-2 mr-3" style="height: calc(1.8125rem + 2px); color: #FFFFFF;">
                            <i class="fas fa-plus fa-xs mr-1"></i> Tambah Paket Member
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
                <table id="data_membership" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama Paket Member</th>
                        <th class="align-middle text-center w-auto" style="border-top: 0 !important;">Jenis</th>
                        <th class="align-middle text-center w-auto" style="border-top: 0 !important;">Kategori</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Durasi</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Harga (Rp.)</th>
                        <th class="align-middle text-center w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="modal-membership">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <i id="membershipModalIco" class="fas fa-plus fa-sm mr-1"></i><span id="membershipModalTitle"></span> Paket Member Baru
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="membershipForm" action="{{route($url )}}" method="POST">
                    <div class="modal-body" id="modal-membership-content">
                        <input type="hidden" name="hiddenID" id="hiddenID" readonly>
                        <input type="hidden" name="hiddenDuration" id="hiddenDuration" readonly>
                        {{ csrf_field() }}

                        <div class="form-group row">
                            <label for="membershipName" class="col-sm-3 col-form-label font-weight-normal">Nama Paket<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" id="membershipName" name="membershipName" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="membershipDesc" class="col-sm-3 col-form-label font-weight-normal">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea id="membershipDesc" name="membershipDesc" class="form-control" rows="5" placeholder="Deskripsi Paket Member..."></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="membershipType" class="col-sm-3 col-lg-3 col-form-label font-weight-normal">Jenis<span class="text-danger">*</span></label>
                            <div class="col-sm-9 col-lg-3 mb-3">
                                <select class="form-control w-100" id="membershipType" name="membershipType">
                                    <?php
                                    foreach($type as $t){
                                    $selected = "";?>
                                    <option value="{{ $t->mtype_id }}">{{ $t->type }}</option><?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <label for="membershipDuration" class="col-sm-3 col-lg-2 col-form-label font-weight-normal">Durasi (bulan)<span class="text-danger">*</span></label>
                            <div class="col-sm-9 col-lg-4 mb-3">
                                <input type="number" id="membershipDuration" name="membershipDuration" class="form-control" autocomplete="off" value="1" min="1">
                            </div>

                            <label for="membershipCategory" class="col-sm-3 col-lg-3 col-form-label font-weight-normal">Kategori<span class="text-danger">*</span></label>
                            <div class="col-sm-9 col-lg-3 mb-2">
                                <select class="form-control w-100" id="membershipCategory" name="membershipCategory">
                                    <?php
                                    foreach($category as $t){
                                    $selected = "";?>
                                    <option value="{{ $t->id }}">{{ $t->category }}</option><?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <label for="membershipPrice" class="col-sm-3 col-lg-2 col-form-label font-weight-normal">Harga (Rp.)<span class="text-danger">*</span></label>
                            <div class="col-sm-9 col-lg-4">
                                <input type="number" id="membershipPrice" name="membershipPrice" class="form-control" autocomplete="off" value="0" min="0">
                            </div>
                        </div>

                        <div class="form-group row" id="membershipStatusToggler">
                            <label for="membershipStatus" class="col-sm-3 col-form-label font-weight-normal">Status<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control" id="membershipStatus" name="membershipStatus">
                                    <?php
                                    foreach($status as $s){
                                    $selected = "";?>
                                    <option value="{{ $s->id }}">{{ $s->status }}</option><?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer pr-2 pb-0" id="membershipFormButtonGroup" style="border-top: 1px solid #dde0e6!important;">
                        <button type="button" class="btn btn-outline-dark" style="padding-top: 4px; padding-bottom: 4px;" data-dismiss="modal"><i class="fas fa-times fa-sm"></i> Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveMembership" style="padding-top: 4px; padding-bottom: 4px;"><i class="fas fa-save fa-sm"></i> Save</button>
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
        $("#data_membership").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('membership.getMembershipData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'membershipType', name: 'membershipType' },
                { data: 'membershipCategory', name: 'membershipCategory' },
                { data: 'duration', name: 'duration' },
                { data: 'price', name: 'price' },
                { data: 'membershipStatus', name: 'membershipStatus' },
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

        $("#data_membership_length").appendTo("#orderContainer");
        $("#data_membership_filter").appendTo("#searchContainer");
        $("#data_membership_info").addClass("pt-2 pl-2");
        $("#data_membership_paginate").addClass("float-right");

        $("#membershipDuration").on("keyup mouseup change click", function () {
            if($(this).prop('disabled')){
            }else{
                $("#hiddenDuration").val($(this).val());
            }
        });
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

    function newMembershipModal(){
        $("#membershipStatusToggler").hide();
        $("#deleteMembership").remove();
        $("#membershipModalTitle").html("Tambah");
        $("#membershipModalIco").removeClass("fa-pencil-alt");
        $("#membershipModalIco").addClass("fa-plus");
        $("#saveMembership").html('<span class="fas fa-save fa-xs mr-1"></span> Simpan');

        $("#membershipForm").prop("action", "{{ route('membership.store') }}");

        durationEnabled();

        resetForm();
    }

    function membershipEditMode(){
        $("#membershipStatusToggler").show();
        $("#membershipModalIco").removeClass("fa-plus");
        $("#membershipModalIco").addClass("fa-pencil-alt");
        $("#membershipModalTitle").html("Ubah");
        $("#saveMembership").html('<span class="fas fa-pencil-alt fa-xs mr-1"></span> Ubah');

        resetForm();
    }

    $("#saveMembership").on('click', function(){
        if(!requiredMembershipData()){
            messagingError();
        }else{
            messagingSuccess();
            $("#membershipModal").modal('toggle');
            $("#membershipAdd").prop('disabled', true);
            $("#membershipAdd").html('<span class="fa fa-sync fa-plus mr-2"></span>Menambahkan...');
            $("#membershipForm").submit();
        }
    });

    function requiredMembershipData(){
        if($("#membershipName").val() == "" || $("#membershipDuration").val() == "" ||
            $("#membershipPrice").val() == ""){
            return false;
        }else{
            return true;
        }
    }

    function resetForm() {
        $("#membershipForm").trigger("reset");
    }

    function editDataOf(id){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('membership.edit') }}",
            data: {
                mship_id: id,
            },
            success: function(data){
                var obj = JSON.parse(data);

                $("#membershipName").val(obj.data.name);
                $("#membershipDesc").val(obj.data.desc);
                $("#membershipDuration").val(obj.data.duration);
                $("#membershipPrice").val(obj.data.price);
                $("#hiddenID").val(obj.data.mship_id);
                $("#hiddenDuration").val(obj.data.duration);
                $("#membershipType option:selected").removeAttr("selected");
                $("#membershipStatus option:selected").removeAttr("selected");
                $("#membershipCategory option:selected").removeAttr("selected");
                $("#membershipType option[value='"+obj.data.type+"']").attr("selected", "selected");
                $("#membershipStatus option[value='"+obj.data.status+"']").attr("selected", "selected");
                $("#membershipCategory option[value='"+obj.data.category+"']").attr("selected", "selected");
                if ($("#membershipFormButtonGroup").find("#deleteMembership").length == 0){
                    $("#membershipFormButtonGroup").prepend(obj.delete_button);
                }
                $("#membershipForm").attr("action", obj.url);

                if(obj.subscription != 0){
                    durationDisabled();
                    $("#deleteMembership").attr("onclick", "durationDisabledMessage();");
                }else{
                    durationEnabled();
                    $("#deleteMembership").attr("onclick", "destroy("+obj.data.mship_id+");");
                }
            }
        });
    }

    function destroy(mship_id){
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
                $.post("{{ route('membership.destroy') }}", { mship_id:mship_id, _token:token}, function(data){
                    location.reload();
                })
            }else{
                return false;
            }
        });
    }

    function durationEnabled(){
        $("#membershipDuration").attr('readonly', false);
        $("#membershipDuration").attr("onclick","");
    }

    function durationDisabled(){
        $("#membershipDuration").attr('readonly', true);
        $("#membershipDuration").attr("onclick","durationDisabledMessage();");
    }

    function durationDisabledMessage(){
        messagingErrorCustom("Durasi tidak dapat diubah. Terdapat Member yang berlangganan paket ini!");
    }

    function deleteDisabledMessage(){
        messagingErrorCustom("Tidak dapat menghapus paket member. Terdapat Member yang berlangganan paket ini!");
    }

    @endsection
</script>
