<div class="modal fade" id="approvalModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>Harga Paket Member By Approval
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-1">
                        <h6 class="pt-1">Rp. </h6>
                    </div>
                    <div class="col">
                        <input type="number" id="approvalPrice" name="approvalPrice" class="form-control" autocomplete="off" min="0">
                    </div>
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


<div class="modal fade" id="approvalSesiModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>Harga Paket PT By Approval
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-1">
                        <h6 class="pt-1">Rp. </h6>
                    </div>
                    <div class="col">
                        <input type="number" id="approvalSesiPrice" name="approvalSesiPrice" class="form-control" autocomplete="off" min="0">
                    </div>
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


<div class="modal fade" id="familyModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-users fa-sm mr-1"></i>Family Member
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-5 col-sm-3">
                        <div class="nav flex-column nav-tabs h-100" id="family-member-tab" role="tablist" aria-orientation="vertical">
                            <!-- GENERATE TAB LIST HERE -->
                        </div>
                    </div>
                    <div class="col-7 col-sm-9">
                        <div class="tab-content" id="family-member-content">
                            <!-- GENERATE TAB CONTENT HERE -->
                        </div>
                    </div>
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

<div class="modal fade" id="paymentDebitModal">
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

