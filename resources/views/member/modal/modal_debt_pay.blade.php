<div class="modal fade" id="modal-debt_pay">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i> Pembayaran Cicilan
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body p-3">
                        <h6 class="font-weight-bold">Detail Pembayaran</h6>
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Member ID
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-normal font-weight-bold" id="detailMemberID"></h6>
                        </div>
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Nama
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-normal" id="detailMemberName"></h6>
                        </div>
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Jenis Kelamin
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-normal" id="detailMemberGender"></h6>
                        </div>
                        <hr class="mt-2 mb-2">
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Total Pembayaran
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-bold" id="detailMemberTotalCicilan"></h6>
                        </div>
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Sisa Tenor
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-normal" id="detailMemberTenor"></h6>
                        </div>
                        <div class="form-group row mb-0">
                            <h6 class="col-sm-3 col-form-label font-weight-normal">
                                Sisa Pembayaran
                            </h6>
                            <h6 class="col-sm-9 col-form-label font-weight-normal" id="detailMemberSisaCicilan"></h6>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-3">
                        <h6 class="font-weight-bold">Pembayaran Cicilan</h6>
                        <select id="paymentType" class="form-control float-left select2 w-auto mr-2" style="width: 100%;" name="paymentType">
                            <option value="cicilan" selected>Bayar Bulanan</option>
                            <option value="penuh">Bayar Penuh</option>
                            <option value="manual">Pembayaran Manual</option>
                        </select>
                        <div id="paymentFullGroup">
                            <input id="paymentDuration" type="number" class="float-left form-control w-auto mr-2" style="max-width: 70px;" min="1" value="1">
                            <h6 class="float-left mt-2">Bulan</h6>
                        </div>
                        <div id="paymentManualGroup" style="display: none;">
                            <h6 class="float-left mt-2 mr-2">Rp. </h6>
                            <input id="paymentManualPrice" type="number" class="float-left form-control w-100 mr-2" style="max-width: 100px;" min="0" value="0">
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary w-100 mb-1" onclick="pilihPembayaran();">
                    <i class="fas fa-arrow-right fa-sm mr-1"></i> Pilih Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-f-payment" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content" id="modal-f-payment-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Pembayaran
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="returnToDetail();">
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
                                <div class="attachment-block attachment-block-selector clearfix" id="payment-{{ $pay->id }}"
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
                            <div class="card-body pt-3 pb-3 pl-3 pr-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <b id="payment-title"> Pembayaran Cicilan </b>
                                    </div>
                                    <div class="col-md-8">
                                        Rp. <span id="total_payment">0</span>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="verifyPaymentRequirement()">
                            <i class="fas fa-check fa-sm mr-1"></i> Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentDebitModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="far fa-credit-card mr-1"></i>Pilih Nama Bank
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                <div class="row">
                    <?php
                    foreach($debitType as $bank){?>
                    <div class="attachment-block attachment-block-selector clearfix w-100" style="padding: 15px; cursor: pointer;"
                         data-bank="{{ $bank->name }}" onclick="selectPaymentType({{ $bank->id }}, this);"
                         id="bank-{{ $bank->id }}">
                        <h6 class="attachment-heading"><i class="{{ $bank->icon }} mr-2"></i>{{ $bank->name }}</h6>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer pt-1 pb-1" style="border-top: 1px solid #dde0e6!important;">
                <button type="button" class="btn btn-dark pt-1 pb-1" data-dismiss="modal">
                    <i class="fas fa-check fa-sm mr-1"></i> Ok
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentCreditModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="far fa-credit-card mr-1"></i>Pilih Jenis Kartu
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                <div class="row">
                    <?php
                    foreach($creditType as $card){?>
                    <div class="attachment-block attachment-block-selector clearfix w-100" style="padding: 15px; cursor: pointer;"
                         data-bank="{{ $card->name }}" onclick="selectPaymentType({{ $card->id }}, this);"
                         id="bank-{{ $card->id }}">
                        <h6 class="attachment-heading"><i class="{{ $card->icon }} mr-2"></i>{{ $card->name }}</h6>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="modal-footer pt-1 pb-1" style="border-top: 1px solid #dde0e6!important;">
                <button type="button" class="btn btn-dark pt-1 pb-1" data-dismiss="modal">
                    <i class="fas fa-check fa-sm mr-1"></i> Ok
                </button>
            </div>
        </div>
    </div>
</div>
