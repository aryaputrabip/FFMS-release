<div class="modal fade" id="modal-payment-form" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content" id="modal-payment-form-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Pembayaran
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="" id="modal-payment-btn-close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row pt-1 pl-3 pr-3 pb-2">
                                <label for="paymentGroup" class="col-sm-9 col-form-label">
                                    Pilih Pembayaran<span class="color-danger">*</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-caret-up"></i>
                                    </button>
                                </label>
                            </div>
                            <div class="card-body pt-0 pb-2" id="paymentGroup" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;"><?php
                                foreach($payment as $pay){?>
                                <div class="attachment-block selected_payment_model attachment-block-selector clearfix" id="payment-{{ $pay->id }}"
                                     style="padding: 15px; cursor: pointer;" data-payment="{{ $pay->payment }}" onclick="selectPaymentModel({{ $pay->id }}, this);">
                                    <h6 class="attachment-heading" id="payment-{{ $pay->id }}-name">
                                        <i class="{{ $pay->icon }} mr-2"></i>{{ $pay->payment }}
                                    </h6>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="card">
                            <div class="row pt-3 pr-3 pb-2 pl-3">
                                <label for="paymentMethodGroup" class="col-sm-9 col-form-label">
                                    Metode Pembayaran<span class="text-danger">*</span>
                                </label>
                            </div>
                            <div class="card-body pt-0" id="paymentMGroup" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;">
                                <select class="form-control select2 w-auto float-left mr-2" style="min-width: 200px;" id="paymentMethodGroup">
                                    <option value="full" selected>Lunas</option>
                                    <option value="cicilan">Cicilan</option>
                                </select>
                                <div id="paymentCicilanDurationContainer" style="display: none;">
                                    <input class="form-control w-auto float-left" type="number" min="2" value="2" style="max-width: 70px;" id="paymentCicilanDuration" name="paymentCicilanDuration">
                                    <h6 class="float-left mt-2 ml-2">Bulan</h6>
                                </div>
                            </div>

                            <div id="firstPaymentGroup" style="display: none">
                                <div class="row pt-3 pr-3 pb-2 pl-3">
                                    <label class="col-sm-9 col-form-label">
                                        Pembayaran Pertama<span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="card-body pt-0" id="firstPaymentGroup" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;">
                                    <select class="form-control select2 w-auto float-left mr-2" style="min-width: 200px;" id="firstPaymentMethodGroup" name="firstPaymentMethodGroup">
                                        <option value="auto" selected>Perhitungan Otomatis</option>
                                        <option value="manual">Manual</option>
                                    </select>
                                    <div id="firstPaymentAutoContainer">
                                        <h6 class="float-left mt-2 ml-2 mr-2" id="firstPaymentAutoLabel">Rp. 0</h6>
                                    </div>
                                    <div id="firstPaymentManualContainer" style="display: none;">
                                        <h6 class="float-left mt-2 ml-2 mr-2">Rp. </h6>
                                        <input class="form-control w-auto float-left" type="number" min="0" value="0" style="max-width: 160px;" id="firstPaymentManualInput" name="firstPaymentManualInput">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body pt-3 pb-3 pl-3 pr-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <b id="payment-title"> - </b>
                                    </div>
                                    <div class="col-md-8">
                                        Rp. <span id="price_to_pay">0</span>
                                        <span id="after_discount_price" style="display: none;">0</span>
                                    </div>
                                </div>
                                <div class="row text-right">
                                    <div class="col-12">
                                        <h6>Total Charge</h6>
                                    </div>
                                </div>
                                <div class="row text-right">
                                    <div class="col-12">
                                        <h2 id="total_price_parent"><b>Rp. </b><b id="total_price">0</b></h2>
                                        <h4 class="font-weight-bold" id="cicilanChargeContainer" style="display: none;">
                                            <i><b>Rp. </b><b id="cicilan_per_bulan">0</b><small>/bulan</small></i>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="" id="modal-payment-btn-continue">
                            <i class="fas fa-check fa-sm mr-1"></i> Tambah Catatan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-payment-notes-form" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content"  id="modal-payment-notes-form-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Catatan
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                <div class="row">
                    <textarea class="form-control w-100 ml-2 mr-2" id="paymentNotesData" name="paymentNotesData" rows="6" placeholder="Tambah Catatan (optional)..."></textarea>

                    <button type="button" class="btn btn-primary w-100 ml-2 mr-2 mt-2" id="finishPayment" data-action="" data-message="">
                        <i class="fas fa-check fa-sm mr-1"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
