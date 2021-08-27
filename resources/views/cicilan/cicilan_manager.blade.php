@extends($app_layout)

<style>
    @section('css')
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
                    <div class="col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-user-check mr-1"></i> Member Dalam Cicilan</h6>
                        <h2>{{ $tMemberCicilan }}</h2>
                    </div>
                    <div class="col-4 text-center border-right">
                        <h6 class="mb-0"><i class="fas fa-male mr-1"></i> Laki-laki</h6>
                        <h2>{{ $memberCicilanLK }}</h2>
                    </div>
                    <div class="col-4 text-center">
                        <h6 class="mb-0"><i class="fas fa-female mr-1"></i> Perempuan</h6>
                        <h2>{{ $memberCicilanPR }}</h2>
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
                        <div class="float-right mt-2 mr-2 mb-2">
                            <!-- FILTER HERE -->
                        </div>
                    </div>
                </div>
                <table id="data_member" class="table table-bordered w-100" style="font-size: 14px; margin-top: 0 !important; margin-bottom: 0 !important; border: none !important;">
                    <thead>
                    <tr>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">No</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Member ID</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Nama</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Jenis Kelamin</th>
                        <th class="align-middle text-center pl-2 pr-2 w-auto" style="border-top: 0 !important;">Status</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Sisa Tenor</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Sisa Pembayaran</th>
                        <th class="align-middle w-auto" style="border-top: 0 !important;">Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <form id="pembayaranCicilanForm" method="POST" action="{{ route('member.cicilan.bayarCicilan') }}" class="row pb-0">
                @csrf

                <input type="hidden" id="hiddenID" name="hiddenID" readonly>
                <input type="hidden" id="hiddenDataPrice" name="hiddenDataPrice" readonly>
                <input type="hidden" id="hiddenDataPaymentType" name="hiddenDataPaymentType" readonly>
                <input type="hidden" id="hiddenDataPaymentModel" name="hiddenDataPaymentModel" readonly>
                <input type="hidden" id="hiddenDataResDuration" name="hiddenDataResDuration" readonly>
                <input type="hidden" id="hiddenDataDuration" name="hiddenDataDuration" readonly>

                <input type="hidden" id="cachePaymentModel" name="cachePaymentModel" readonly>
                <input type="hidden" id="cachePaymentType" name="cachepaymentType" readonly>
            </form>
        </div>
    </div>
@endsection

@section('modal')
    @include('member.modal.modal_debt_pay')
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
    @include('config.session.request_session')

    @include('config.swal.swal_message')
@endsection

<script>
@section('script')
    const data_member = settingDatatablesMember();

    $("#data_member_length").appendTo("#orderContainer");
    $("#data_member_filter").appendTo("#searchContainer");
    $("#data_member_info").addClass("pt-2 pl-2");
    $("#data_member_paginate").addClass("float-right");

    function settingDatatablesMember(){
        return $("#data_member").DataTable({
            searching: true,
            lengthChange: true,
            paging: true,
            info: true,
            processing: true,
            ajax: "{{ route('member.cicilan.cicilanMemberData') }}",
            iDisplayLength: 10,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'member_id', name: 'member_id' },
                { data: 'name', name: 'name' },
                { data: 'gender', name: 'gender' },
                { data: 'status', name: 'status' },
                { data: 'tenor', name: 'tenor' },
                { data: 'pembayaran', name: 'pembayaran' },
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

    $("#paymentType").on("change",  function(){
       if($(this).val() == "penuh"){
           $("#paymentFullGroup").hide();
           $("#paymentManualGroup").hide();
       }else if($(this).val() == "manual"){
           $("#paymentFullGroup").hide();
           $("#paymentManualGroup").show();
       }else{
           $("#paymentFullGroup").show();
           $("#paymentManualGroup").hide();
       }
    });

    $('#paymentDebitModal').on('hide.bs.modal', function () {
        $("#modal-f-payment").modal("show");
    });

    $('#paymentCreditModal').on('hide.bs.modal', function () {
        $("#modal-f-payment").modal("show");
    });

    $('#modal-f-payment').on('hide.bs.modal', function () {
        if(modal_action == "" || modal_action == null){
            $("#modal-debt_pay").modal("show");
        }
    });

    function payDebt(member){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('member.cicilan.getMemberCicilanData') }}",
            data: {
                member_id: member,
            },
            success: function(data){
                getMemberContentData(data);
                $("#modal-debt_pay").modal("show");
            }
        });
    }

    $("#paymentDuration").on("change", function(){
        $("#paymentConfigAutoPrice").html("Rp. " + hitungCicilan($("#hiddenDataPrice").val(), $("#paymentDuration").val()));
    });

    function getMemberContentData(data){
        var obj = JSON.parse(data);

        $("#detailMemberID").html(obj.data.member_id);
        $("#detailMemberName").html(obj.data.name);
        $("#detailMemberGender").html(obj.data.gender);
        $("#detailMemberTotalCicilan").html("Rp. " + asRupiah(obj.data.total_cicilan));
        $("#paymentConfigAutoPrice").html("Rp. " + hitungCicilan(obj.data.total_cicilan, $("#paymentDuration").val()));
        $("#detailMemberTenor").html(obj.data.tenor + " Bulan");
        $("#detailMemberSisaCicilan").html("Rp. " + asRupiah(obj.data.sisa_cicilan));

        $("#paymentFullGroup").show();

        $("#paymentDuration").val(1);
        $("#paymentType").val("cicilan");
        $("#paymentDuration").attr("max", obj.data.tenor);

        $("#cachePaymentType").val("");
        $("#cachePaymentModel").val("");

        $("#hiddenID").val(obj.data.member_id);
        $("#hiddenDataPrice").val(obj.data.sisa_cicilan);
        $("#hiddenDataResDuration").val(obj.data.tenor);
    }

    function returnToDetail(){
        modal_action = null;
    }

    function pilihPembayaran(){
        if($("#paymentDuration").val() == "" || $("#paymentMethodGroup").val() == ""){
            messagingErrorCustomTimer("Data belum lengkap!");
        }else{
            if($("#paymentDuration").val() < 1 || $("#paymentDuration").val() > $("#hiddenDataResDuration").val()){
                messagingErrorCustomTimer("Min Tenor: 1 Bulan, Max: " + $("#hiddenDataResDuration").val() + " Bulan");
            }else{
                var dataPay = 0;
                const resourcePay = ($("#hiddenDataPrice").val() / $("#hiddenDataResDuration").val()).toFixed(0);
                if($("#paymentType").val() == "cicilan"){
                    dataPay = resourcePay * $("#paymentDuration").val();
                    $("#hiddenDataDuration").val($("#paymentDuration").val());
                }else{
                    dataPay = resourcePay * $("#hiddenDataResDuration").val();
                    $("#hiddenDataDuration").val($("#hiddenDataResDuration").val());
                }

                if($("#paymentType").val() == "manual"){
                    $("#total_payment").html(asRupiah($("#paymentManualPrice").val()));
                    $("#total_price").html(asRupiah($("#paymentManualPrice").val()));
                    $("#hiddenDataPaymentType").val("manual");

                    $("#hiddenDataPrice").val($("#paymentManualPrice").val());
                }else{
                    $("#total_payment").html(asRupiah(dataPay));
                    $("#total_price").html(asRupiah(dataPay));
                }

                $("#modal-debt_pay").modal("hide");
                $("#modal-f-payment").modal("show");
            }
        }
    }

    var modal_action;
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

    function selectPaymentModel(type, element){
        reselectPaymentCard(type);
        $("#cachePaymentModel").val($(element).data('payment'));
        $("#cachePaymentType").val("");

        modal_action = "payment_action";

        switch(type){
            case 2:
                $('#paymentDebitModal').modal('show');
                $('#modal-f-payment').modal("hide");
                break;

            case 3:
                $('#paymentCreditModal').modal('show');
                $('#modal-f-payment').modal("hide");
                break;
        }
    }

    function verifyPaymentRequirement(){

        if($("#cachePaymentModel").val() == ""){
            messagingErrorCustomTimer('Jenis Pembayaran Belum Dipilih!');
        }else{
            if($("#cachePaymentModel").val() == "Cash"){
                notifyConfirmPayment();
            }else{
                if($("#cachePaymentType").val() != ""){
                    verifyInputRequirement();
                }else{
                    messagingErrorCustomTimer('Bank Pembayaran Belum Dipilih!');
                }
            }
        }
    }

    function selectPaymentType(type, element){
        reselectPaymentBank(type);
        $("#cachePaymentType").val($(element).data('bank'));
    }

    function notifyConfirmPayment(){
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

                $("#pembayaranCicilanForm").submit();
            }else{
                return false;
            }
        });
    }

    function verifyInputRequirement(){
        if($("#cachePaymentModel").val() == "" || $("#cachePaymentType").val() == "" ||
            $("#paymentType").val() == "" || $("#paymentDuration").val() == ""){
            messagingErrorCustomTimer('Data belum lengkap!');
        }else{
            notifyConfirmPayment();
        }
    }

    function setPaymentLoad(){
        $("#modal-f-payment-content").append(
            '<div class="overlay d-flex justify-content-center align-items-center">' +
            '   <i class="fas fa-2x fa-sync fa-spin"></i>' +
            '</div>'
        );
    }

    function hitungCicilan(price, tenor){
        var cicilan = price / tenor;
        return asRupiah(cicilan);
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
@endsection
</script>
