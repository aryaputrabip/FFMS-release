<div class="modal fade" id="modal-membership">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-edit fa-sm mr-1"></i> Paket Member
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! $membership_action !!}
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-pt">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-edit fa-sm mr-1"></i> Personal Trainer & Sesi Latihan
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! $pt_action !!}
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="modal-m-extend" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-calendar-plus fa-sm mr-1"></i> Perpanjang Paket
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Paket Member Aktif</h6>
                <div class="attachment-block clearfix" style="padding: 15px;">
                    <input type="hidden" id="extend-membership-id" value="{{ $membership->mship_id }}" readonly>
                    <h5 class="attachment-heading" id="extend-membership-name">{{ $membership->name }}</h5>
                    <div class="attachment-text"><b>Durasi: </b> <span id="extend-membership-duration">{{ $membership->duration }}</span> Bulan</div>
                    <div class="attachment-text"><b>Tipe: </b> <span id="extend-membership-type"> @if($membership->type == 1) GYM Only @else All Access @endif </span> </div>
                    <div class="attachment-text mt-2"><b>Harga: </b><span id="extend-membership-price" data-price="{{ $membership->price }}"><?php echo asRupiah($membership->price); ?></span></div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dde0e6!important;">
                <button type="button" class="btn btn-primary w-100" id="payMembershipExtend">
                    <i class="fas fa-arrow-right fa-sm mr-1"></i> Pilih Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-pt-add" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Daftar Paket Personal Trainer
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><b>Personal Trainer</b></h6>
                <select class="form-control select2 mb-2" style="width: 100%;" id="dataPTReg" name="dataPTReg">
                    <option value="nothing" selected> - </option>
                    <?php
                    foreach($ptList as $pt_boy){?>
                    <option value="{{ $pt_boy->pt_id }}" data-name="{{ $pt_boy->name }}">{{ $pt_boy->name }}</option>
                    <?php
                    }?>
                </select>

                <select class="form-control select2 mb-4" style="width: 100%;" id="dataPTRegSession" name="dataPTRegSession"><?php
                    foreach($session as $session_list){?>
                    <option value="{{ $session_list->duration }}" data-price="{{ $session_list->price }}" data-title="{{ $session_list->title }}">
                        @if($session_list->title != null){{ $session_list->title }} - @endif{{ $session_list->duration }} Sesi</option>
                    <?php
                    }
                    ?>
                </select>

                <button type="button" class="btn btn-primary w-100" id="payPTRegister">
                    <i class="fas fa-arrow-right fa-sm mr-1"></i> Pilih Pembayaran
                </button>

                <input type="hidden" id="cacheRegPTID" name="cacheRegPTID" readonly>
                <input type="hidden" id="cacheRegSessionPrice" name="cacheRegSessionPrice" readonly>
                <input type="hidden" id="cacheRegSessionDuration" name="cacheRegSessionDuration" readonly>
                <input type="hidden" id="cacheRegSessionGroup" name="cacheRegSessionGroup" readonly>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-pt-change" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content" id="modal-pt-change-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Ubah Personal Trainer
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6><b>Personal Trainer</b></h6>
                <select class="form-control select2" style="width: 100%;" id="dataPT" name="dataPT">
                    <option value="nothing" @if(isset($pt)) @else selected @endisset> - </option>
                    @if(isset($pt))
                        <option value="{{ $pt->pt_id }}" data-name="{{ $pt->name }}" selected>{{ $pt->name }}</option>
                    @endisset

                    <?php
                    foreach($ptList as $pt_boy){?>
                    @if(isset($pt))
                        @if($pt_boy->pt_id != $pt->pt_id)
                            <option value="{{ $pt_boy->pt_id }}" data-name="{{ $pt_boy->name }}">{{ $pt_boy->name }}</option>
                        @endif
                    @else
                        <option value="{{ $pt_boy->pt_id }}" data-name="{{ $pt_boy->name }}">{{ $pt_boy->name }}</option>
                    @endisset
                    <?php
                    }?>
                </select>
                <form id="ptEditForm" name="ptEditForm" method="POST" action="{{ route('member.changePT') }}">
                    {{ csrf_field() }}

                    <input type="hidden" id="ptEditHiddenID" name="ptEditHiddenID" value="{{ $data->member_id }}" readonly>
                    <input type="hidden" id="cachePT" name="cachePT" readonly>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #dde0e6!important;">
                <button type="button" class="btn btn-primary w-100" onclick="editPTNameConfirm();">
                    <i class="fas fa-plus fa-sm mr-1"></i> Ubah Personal Trainer
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-s-add" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Sesi
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="toggleModal('modal-pt')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row mb-0" id="familyMemberGroup">
                    <label for="dataUserFamily" class="col-sm-4 mb-0 col-form-label font-weight-normal">
                        Nama PT
                    </label>
                    <div class="col-sm-8 col-form-label">
                        : <b>@if(isset($pt)) {{$pt->name}} @else - @endisset</b>
                    </div>
{{--                    <div class="col-sm-4"></div>--}}
{{--                    <div class="col-sm-8">--}}
{{--                        <button type="button" class="btn btn-outline-dark w-100">--}}
{{--                            <span class="fas fa-edit fa-sm"></span> Ubah PT--}}
{{--                        </button>--}}
{{--                    </div>--}}
                </div>
                <div class="form-group row mb-0" id="familyMemberGroup">
                    <label for="dataUserFamily" class="col-sm-4 mb-0 col-form-label font-weight-normal">
                        Sisa Sesi
                    </label>
                    <div class="col-sm-8 col-form-label">
                        : <b>@if(isset($data)) {{$data->session}} @endisset Sesi</b>
                    </div>
                </div>
                <hr>
                <div class="form-group row mb-0" id="familyMemberGroup">
                    <label for="dataUserFamily" class="col-sm-4 mb-0 col-form-label">
                        Tambah Sesi
                    </label>
                    <div class="col-sm-8">
                        <select class="form-control select2" style="width: 100%;" id="dataUserPTSession" name="dataUserPTSession"><?php
                            foreach($session as $session_list){?>
                            <option value="{{ $session_list->duration }}" data-price="{{ $session_list->price }}" data-title="{{ $session_list->title }}">
                                @if($session_list->title != null){{ $session_list->title }} - @endif{{ $session_list->duration }} Sesi</option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>


            </div>
            <div class="modal-footer" style="border-top: 1px solid #dde0e6!important;">
                <button type="button" id="addSessionConfirm" class="btn btn-primary w-100">
                    <i class="fas fa-plus fa-sm mr-1"></i> Tambah Sesi
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="toggleModal('payment-return')">
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
                                    <input class="form-control w-auto float-left" type="number" min="1" value="1" style="max-width: 70px;" id="paymentCicilanDuration" name="paymentCicilanDuration">
                                    <h6 class="float-left mt-2 ml-2">Bulan</h6>
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
                                        <h4 class="font-weight-bold" id="cicilanChargeContainer" style="display: none;">
                                            <i><b>Rp. </b><b id="cicilan_per_bulan">0</b><small>/bulan</small></i>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" data-dismiss="modal" aria-label="Close" onclick="toggleModal('notesModal')">
                            <i class="fas fa-check fa-sm mr-1"></i> Selesai
                        </button>
                    </div>
                </div>

                <input type="hidden" id="cachePaymentModel" name="cachePaymentModel" readonly>
                <input type="hidden" id="cachePaymentType" name="cachepaymentType" readonly>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="notesModal" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-plus fa-sm mr-1"></i> Catatan
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="toggleModal('modal-f-payment')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
                <div class="row">
                    <textarea class="form-control w-100 ml-2 mr-2" id="dataNote" name="dataNote" rows="6" placeholder="Tambah Catatan (optional)..."></textarea>

                    <form id="sAddForm" name="sAddForm" method="POST" action="{{ route('member.addTransaction') }}">
                        {{ csrf_field() }}

                        <input type="hidden" id="sHiddenID" name="sHiddenID" value="{{ $data->member_id }}" readonly>
                        <input type="hidden" id="sName" name="sName" value="{{ $data->name }}" readonly>
                        <input type="hidden" id="sTransaction" name="sTransaction" readonly>
                        <input type="hidden" id="sOld" name="sOld" value="{{ $data->session }}" readonly>
                        <input type="hidden" id="lOld" name="lOld" value="{{ $data->session_reg }}" readonly>
                        <input type="hidden" id="nSession" name="nSession" readonly>
                        <input type="hidden" id="nPT" name="nPT" readonly>
                        <input type="hidden" id="nPrice" name="nPrice" readonly>
                        <input type="hidden" id="nTitle" name="nTitle" readonly>
                        <input type="hidden" id="nPayment" name="nPayment" readonly>
                        <input type="hidden" id="nBank" name="nBank" readonly>
                        <input type="hidden" id="nRegNo" name="nRegNo" value="{{ $reg_no }}" readonly>
                        <input type="hidden" id="nNotes" name="nNotes" readonly>

                        <input type="hidden" id="mShipID" name="mShipID" readonly>
                        <input type="hidden" id="mShipName" name="mShipName" readonly>
                        <input type="hidden" id="mShipPrice" name="mShipPrice" readonly>
                        <input type="hidden" id="mShipDuration" name="mShipDuration" readonly>
                        <input type="hidden" id="mShipType" name="mShipType" readonly>
                        <input type="hidden" id="mShipCategory" name="mShipCategory" readonly>
                        <input type="hidden" id="mShipApproval" name="mShipApproval" readonly>
                        <input type="hidden" id="paymentMethodGroup2" name="paymentMethodGroup" readonly>
                        <input type="hidden" id="durasiCicilan" name="durasiCicilan" readonly>
                        <input type="hidden" id="jumlahCicilan" name="jumlahCicilan" readonly>
                    </form>

                    <button type="button" class="btn btn-primary w-100 ml-2 mr-2 mt-2" id="confirmPayment" data-action="">
                        <i class="fas fa-check fa-sm mr-1"></i> Selesai
                    </button>
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

<div class="modal fade" id="modal-m-change" data-backdrop="static">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-edit fa-xs mr-1"></i> Ganti Paket Member
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="toggleModal('modal-membership')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row p-3">
                                <label class="col-sm-9 col-form-label">
                                    Paket Membership<span class="color-danger">*</span>
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-caret-up"></i>
                                    </button>
                                </label>
                                <div class="col-sm-3">
                                    <select class="form-control select2" id="membershipFilter" style="width: 100%;">
                                        <option value="" selected>Semuanya</option>
                                        <option value="All-Access">All Access</option>
                                        <option value="GYM-Only">GYM Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body pt-0" style="max-height: 350px; overflow-y: auto; overflow-x: hidden;">
                                <?php
                                function asRupiah($value) {
                                    if ($value<0) return "-".asRupiah(-$value);
                                    return 'Rp. ' . number_format($value, 0);
                                }


                                foreach($membership_data as $mship){?>
                                <div class="attachment-block attachment-block-selector clearfix" id="membership-{{ $mship->mship_id }}" style="padding: 15px; cursor: pointer;" onclick="selectMembership({{$mship->mship_id}})">
                                    <h5 class="attachment-heading" id="membership-{{ $mship->mship_id }}-name">{{ $mship->name }}</h5>
                                    <div class="attachment-text"><b>Durasi: </b> <span id="membership-{{ $mship->mship_id }}-duration">{{ $mship->duration }}</span> Bulan</div>
                                    <div class="attachment-text @if($mship->type == "All Access") All-Access @else GYM-Only @endif"><b>Tipe: </b> <span id="membership-{{ $mship->type }}-type">{{ $mship->type }}</span></div>
                                    <div class="attachment-text"><b>Harga: </b><span id="membership-{{ $mship->mship_id }}-price" data-price="{{ $mship->price }}"><?php echo asRupiah($mship->price); ?></span></div>
                                    <input type="hidden" id="membership-{{ $mship->mship_id }}-category" value="{{ $mship->tMember }}" readonly>
                                </div><?php
                                }?>
                            </div>
                            <input type="hidden" id="cacheMembershipID" name="cacheMembershipID" readonly>
                            <input type="hidden" id="cacheMembership" name="cacheMembership" readonly>
                            <input type="hidden" id="cacheMembershipPrice" name="cacheMembershipPrice" readonly>
                            <input type="hidden" id="cacheMembershipDuration" name="cacheMembershipDuration" readonly>
                            <input type="hidden" id="cacheMembershipType" name="cacheMembershipType" readonly>
                            <input type="hidden" id="cacheMembershipCategory" name="cacheMembershipCategory" readonly>

                            <input type="hidden" id="cacheMembershipAction" name="cacheMembershipAction" readonly>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger w-100" id="changeApprovalBtn" data-target="#approvalModal" data-toggle="modal" disabled="true">
                                    <i class="fas fa-pencil-alt fa-sm mr-1"></i> Pasang Harga
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary w-100" id="payMembershipChange">
                                    <i class="fas fa-arrow-right fa-sm mr-1"></i> Pilih Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

{{--                <input type="hidden" id="cachePaymentModel" name="cachePaymentModel" readonly>--}}
{{--                <input type="hidden" id="cachePaymentType" name="cachepaymentType" readonly>--}}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="approvalModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>Harga By Approval
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="toggleModal('modal-m-change');">
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
                <button type="button" class="btn btn-dark pt-1 pb-1" data-dismiss="modal" onclick="setApprovalPrice();">
                    <i class="fas fa-check fa-sm mr-1"></i> Ok
                </button>
            </div>
        </div>
    </div>
</div>

@if($role == 1)
    <div class="modal fade" id="changeStatusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <i class="fas fa-check fa-sm mr-1"></i>Ubah Status Member
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <form id="statusEditForm" name="statusEditForm" method="POST" action="{{ route('member.forceChangeStatus') }}">
                        {{ csrf_field() }}

                        <input type="hidden" id="memberStatusHiddenID" name="memberStatusHiddenID" value="{{ $data->member_id }}" readonly>

                        <h6><b>Status Member</b></h6>
                        <select class="form-control select2 mb-2" style="width: 100%;" id="dataStatusMember" name="dataStatusMember" @if($data->status != 1) disabled @endif>
                            <option value="1" @if($data->status != 2) selected @endif> Aktif </option>
                            <option value="2" @if($data->status == 2) selected @endif> Non-Aktif </option>
                        </select>

                        <button type="button" class="btn btn-dark w-100 mt-3 mb-0" data-dismiss="modal" @if($data->status == 1) onclick="confirmStatusMemberChange();" @elseif($data->status == 2) disabled @endif>
                            <i class="fas fa-check fa-sm mr-1"></i> Ubah Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeDateModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title text-dark">
                        <i class="fas fa-calendar-alt fa-sm mr-1"></i>Ubah Tanggal Mulai / Berakhir
                    </h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@endif
