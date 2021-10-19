function renewPaket(){
    resetPaymentForm();
    $("#modal-m-renew-content").modal("show");
}

function confirmRenewPaket(){
    //console.log(renewData);
    pay_data = renewData;

    $("#finishPayment").data("message", "Upgrade Paket Member Ini");

    paymentForm(pay_data.data_action, '#modal-m-renew-content', renewData);
}

function confirmChangePaket(){
    //console.log(changeData);
    pay_data = changeData;

    $("#finishPayment").data("message", "Ubah Paket Member Ini");

    paymentForm(pay_data.data_action, '#modal-m-change-content', changeData);
}

function confirmUpgradePaket(){
    //console.log(changeData);
    pay_data = changeData;

    $("#finishPayment").data("message", "Upgrade Paket Member Ini");

    paymentForm(pay_data.data_action, '#modal-m-change-content', changeData);
}

function confirmRegisterSession(){
    pay_data = sessionData;

    $("#finishPayment").data("message", "Daftar Paket PT untuk Member Ini");
    paymentForm(pay_data.data_action, '#modal-pt-add', sessionData);
}

function confirmBuySession(){
    pay_data = sessionData;

    $("#finishPayment").data("message", "Tambah Sesi untuk Member Ini");
    paymentForm(pay_data.data_action, '#modal-s-add', sessionData);
}

function returnToParentModal(modal){
    resetPaymentForm();
    $(modal).modal("show");
}

function paymentForm(action, action_form, data){
    $(action_form).modal("hide");

    $("#modal-payment-form").modal("show");
    $("#modal-payment-btn-close").attr("onclick", "returnToParentModal('"+action_form+"')");

    $("#payment-title").html(data.payment_title);

    pay_data.payment_method = "lunas";

    //$("#payment-title").html("Paket Member <br>" + `{{ $membership->duration }}` + " Bulan<br>(" + $("#extend-membership-type").html() + ")");

    if(data.discount > 0){
        $("#price_to_pay").html("<i style='text-decoration: line-through;'>" + asRupiah(data.price));
        $("#total_price").html(asRupiah(data.price - data.discount));
        $("#after_discount_price").html($("#total_price").html());
        $("#after_discount_price").show();
    }else{
        $("#price_to_pay").html(asRupiah(data.price));
        $("#total_price").html(asRupiah(data.price));
        $("#after_discount_price").hide();
    }

    if(data.note_after_pay){
        $("#modal-payment-btn-continue").attr("onclick", "confirmPaymentForm('"+action+"')");
    }else{
        confirmPaymentForm(action, data);
    }
}

function selectPaymentModel(type, element){
    $(".selected_payment_model").removeClass('block_active');
    $(element).addClass('block_active');

    pay_data.payment_type = $(element).data('payment');
    pay_data.payment_addition = null;

    switch(type){
        case 2:
            $("#modal-payment-form").modal("hide");
            $('#paymentDebitModal').modal('show');
            break;

        case 3:
            $("#modal-payment-form").modal("hide");
            $('#paymentCreditModal').modal('show');
            break;
    }
}

function selectPaymentType(type, element){
    $(".selected_payment_addition").removeClass('block_active');
    $(element).addClass('block_active');

    pay_data.payment_addition = $(element).data('bank');
}

$('#paymentDebitModal').on('hide.bs.modal', function() {
    $("#modal-payment-form").modal("show");
});

$('#paymentCreditModal').on('hide.bs.modal', function() {
    $("#modal-payment-form").modal("show");
});

function notesForm(action, data){
    $("#modal-payment-form").modal("hide");
    $("#modal-payment-notes-form").modal("show");

    data.data_action = action;
    data.note = null;
    //console.log(data);
}

$("#modal-payment-notes-form").on('hide.bs.modal', function() {
    $("#modal-payment-form").modal("show");
});

function confirmPaymentForm(action){
    if(pay_data.payment_type == null){
        messagingErrorCustom('Jenis Pembayaran Belum Dipilih!');
    }else if(pay_data.payment_method == null){
        messagingErrorCustom('Metode Pembayaran Belum Dipilih!');
    }else if(pay_data.payment_type != "Cash" && pay_data.payment_addition == null){
        messagingErrorCustom('Bank Pembayaran Belum Dipilih!');
    }else{
        notesForm(action, pay_data);
    }
}

function resetPaymentForm(){
    $("#price_to_pay").html("");

    $("#paymentMethodGroup").val("full");
    $("#paymentCicilanDurationContainer").hide();

    $("#firstPaymentMethodGroup").val("auto");
    $("#firstPaymentManualContainer").hide();

    $("#paymentCicilanDuration").val(2);
    $("#firstPaymentGroup").hide();
    $("#cicilanChargeContainer").hide();

    $(".attachment-block .attachment-block-selector").removeClass("block_active");
}



$("#paymentMethodGroup").on("change", function(){
    if($(this).val() == "cicilan"){
        stateMetodePembayaranCicilan('enable', pay_data);
    }else{
        stateMetodePembayaranCicilan('disable', pay_data);
    }
});

$("#paymentCicilanDuration").on("keyup change", function(){
    if($(this).val() < 2){
        $(this).val(2);
    }

    hitungPembayaranPertama($("#firstPaymentMethodGroup").val(), pay_data.price);
    hitungKalkulasiCicilan(pay_data.price);
});

$("#firstPaymentMethodGroup").on("change", function(){
    $("#firstPaymentManualInput").val(0);
    pay_data.debt_first_pay = null;

    if($(this).val() == "manual" && $("#paymentCicilanDuration").val() > 0){
        $("#firstPaymentAutoContainer").hide();
        $("#firstPaymentManualContainer").show();
        $("#firstPaySet").val("manual");
        $("#firstPayData").val(0);
        cekCicilanPerBulan(pay_data.price);
    }else{
        $("#firstPaySet").val("auto");

        if($("#paymentCicilanDuration").val() > 0){
            $("#firstPaymentManualContainer").hide();
            $("#firstPaymentAutoContainer").show();
            $("#firstPayData").val(0);
            cekCicilanPerBulan(pay_data.price);
        }
    }
});

$("#firstPaymentManualInput").on("keyup change", function(){
    hitungKalkulasiCicilan(pay_data.price);
    pay_data.debt_first_pay = $(this).val();
});

function stateMetodePembayaranCicilan(state, data){
    if(state == 'enable'){
        $("#paymentCicilanDurationContainer").show();
        $("#firstPaymentGroup").show();
        $("#cicilanChargeContainer").show();

        pay_data.payment_method = "cicilan";

        hitungPembayaranPertama("auto", data.price);
        hitungKalkulasiCicilan(data.price);
    }else if(state == 'disable'){
        $("#paymentCicilanDurationContainer").hide();
        $("#firstPaymentGroup").hide();
        $("#cicilanChargeContainer").hide();

        pay_data.payment_method = "lunas";

        resetPembayaranCicilan();
    }
}

function resetPembayaranCicilan(){
    $("#paymentCicilanDuration").val(2);

    $("#firstPaymentMethodGroup").val("auto");
    $("#paymentCicilanDuration").val(2);

    $("#firstPaymentManualContainer").hide();
}

function hitungPembayaranPertama(state, price){
    const first_pay = price / $("#paymentCicilanDuration").val();

    if(state == "auto"){
        $("#firstPaymentAutoLabel").html("Rp. " + asRupiah(first_pay));
    }else if(state == "manual"){
        //$("#firstPaymentManualInput").val(first_pay);
    }
}

function hitungKalkulasiCicilan(price){
    const cicilan = (price - $("#firstPaymentManualInput").val()) / $("#paymentCicilanDuration").val();

    $("#cicilan_per_bulan").html(asRupiah(cicilan));
}

$("#finishPayment").click(function() {
    submitPayment($(this).data('message'));
});

function submitPayment(message){
    const ConfirmSwal = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary mr-2',
            cancelButton: 'btn btn-danger mr-2'
        },buttonsStyling: false
    });

    ConfirmSwal.fire({
        icon: 'warning',
        html: message + ' ?',
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-times fa-sm mr-1"></i> Tidak`,
        confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Iya`,
        reverseButtons: true
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.confirm){
            setPaymentLoad();

            pay_data.note = $("#paymentNotesData").val();
            pay_data.debt_length = $("#paymentCicilanDuration").val();

            const form = document.createElement("form");

            form.setAttribute("type", "hidden");
            form.setAttribute("method", "POST");
            form.setAttribute("action", pay_data.data_action);

            for(var key in pay_data) {
                if(pay_data.hasOwnProperty(key)) {
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", pay_data[key]);

                    form.appendChild(hiddenField);
                }
            }

            document.body.appendChild(form);
            //console.log(form);
            form.submit();

        }else{
            return false;
        }
    });
}

function setPaymentLoad(){
    $("#modal-payment-notes-form-content").append(
        '<div class="overlay d-flex justify-content-center align-items-center">' +
        '   <i class="fas fa-2x fa-sync fa-spin"></i>' +
        '</div>'
    );
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
