@extends($app_layout)

<style>
    @section('css')
        .form-step{
        list-style: none;
        position: relative;
    }

    .form-step::after{
        content: '';
        position: absolute;
        width: 3px;
        height: 80%;
        top: 10%;
        background: #ccc;
        z-index: -1;
    }

    .form-step li{
        padding: 30px 0;
        padding-left: 20px;
        display: flex;
    }

    .form-step li p{
        position: relative;
    }

    .form-step .step-active{
        font-weight: bold;
        color: #2e3a4d;
    }

    .form-step li p:before{
        content: '';
        background: #f4f6f9;
        position: absolute;
        width: 24px;
        height: 25px;
        top: 50%;
        border-radius: 50%;
        border: 3px solid #ccc;
        transform:translate(-128%, -55%);
    }

    .form-step li i{
        margin-left: 5px;
        margin-right: 5px;
    }

    .form-step li .step-ico{
        position: absolute;
        transform:translate(-220%, 30%);
        color: white;
        z-index: 5;
    }

    .form-step .step-active p:before{
        background: #2e3a4d;
        border-color: #2e3a4d;
    }

    .form-step .step-complete p:before{
        background: #28a745;
        border-color: #28a745;
    }

    .form-step .step-complete{
        color: #28a745;
        font-weight: normal;
    }

    .form-step .step-disable p:before{
        border-color: #ccc;
    }

    .form-step .step-disable{
        color: #ccc;
        font-weight: normal;
    }

    .form-step .step-disable .step-ico{
        color: #ccc;
        transform: translate(-300%, 30%);
    }

    .member-detail-show{
        line-height: 30px;
    }

    .attachment-block-selector:hover{
        background-color: #e8e8e8;
    }

    .block_active:hover{
        background-color: #dcedff !important;
    }

    .block_active{
        background-color: #dcedff;
    }

    .col-photo{
        -ms-flex: 0 0 300px;
        flex: 0 0 300px;
    }
    @endsection
</style>

@section('bg')
    <div style="background-color: #FFFFFF; min-width: 100vw; min-height: 100vh; position:absolute;"></div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- STEP CARD -->
        <div class="card" style="display: none;">
            <div class="card-body pt-2 pb-2 pl-3 pr-3">
                STEP_HERE
            </div>
        </div>

        <form id="registrationForm" action="{{ route('member.registration.store') }}" method="POST">
            {{ csrf_field() }}

            <!-- FORM CARD -->
            <section class="card" id="card-step-1" style="display: block;">
                <div class="card-body pt-2 pb-2 pl-3 pr-3">
                    <h4 class="d-inline">Detail Member</h4><hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserNama" class="col-sm-3 col-form-label">
                                    Nama Lengkap<span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="dataUserNama" name="dataUserNama" placeholder="Nama Lengkap">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserGender" class="col-sm-3 col-form-label">
                                    Jenis Kelamin<span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" style="width: 100%;" id="dataUserGender" name="dataUserGender">
                                        <option value="Laki-laki" selected="selected">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserEmail" class="col-sm-3 col-form-label">
                                    Email<span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" id="dataUserEmail" name="dataUserEmail" placeholder="Email">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserDOB" class="col-sm-3 col-form-label">
                                    Tanggal Lahir
                                </label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="dataUserDOB" name="dataUserDOB">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserPhone" class="col-sm-3 col-form-label">
                                    No Telp.<span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="dataUserPhone" name="dataUserPhone" placeholder="No. Telp.">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserJob" class="col-sm-3 col-form-label">
                                    Pekerjaan
                                </label>
                                <div class="col-sm-9">
                                    <select class="form-control select2" style="width: 100%;" id="dataUserJob" name="dataUserJob">
                                        <option value="" selected="selected"><b>Tidak Bekerja</b></option>
                                        <option value="Karyawan">Karyawan</option>
                                        <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group row">
                                <label for="dataUserCompany" class="col-sm-3 col-form-label">
                                    Perusahaan
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="dataUserCompany" name="dataUserCompany" placeholder="Nama Perusahaan / Instansi Member Bekerja">
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3 mb-3">
                            <button type="button" class="btn btn-secondary w-100" id="nextBTN-1" onclick="continueToNextStep(2, this)">
                                <i class="fas fa-arrow-right fa-sm pr-2"></i> Pilih Membership
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FORM CARD -->
            <section class="card" id="card-step-2" style="display: none;">
                <div class="card-body pt-2 pb-2 pl-3 pr-3">
                    <h4 class="d-inline">Pilih Paket Member</h4><hr>

                    <div class="row">
                        <div class="col-md-6 form-group">
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
                                <div class="card-body pt-0" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;">
                                    <?php
                                    function asRupiah($value) {
                                        if ($value<0) return "-".asRupiah(-$value);
                                        return 'Rp. ' . number_format($value, 0);
                                    }


                                    foreach($membership as $mship){?>
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
                                <input type="hidden" id="cacheMembershipCategory" name="cacheMembershipCategory" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <div class="form-group row mb-0">
                                <label for="dataUserPTToggler" class="col-sm-6 col-form-label">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="dataUserPTToggler">
                                        <label for="dataUserPT">
                                            + Personal Trainer
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-6">
                                    <select class="form-control select2" style="width: 100%;" id="dataUserPT" name="dataUserPT" disabled="true">
                                        <option value="nothing"> - </option>
                                        <?php
                                        foreach($pt as $pt_man){?>
                                        <option value="{{ $pt_man->pt_id }}" data-name="{{ $pt_man->name }}">{{ $pt_man->name }}</option><?php
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <label for="dataUserPTSession" class="col-sm-6 col-form-label"></label>
                                <div class="col-sm-6">
                                    <select class="form-control select2 mb-4" style="width: 100%;" id="dataUserPTSession" name="dataUserPTSession" disabled="true"><?php
                                        foreach($session as $session_list){?>
                                        <option value="{{ $session_list->duration }}" data-price="{{ $session_list->price }}" data-title="{{ $session_list->title }}">@if($session_list->title != null){{ $session_list->title }} - @endif{{ $session_list->duration }} Sesi</option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <label for="dataUserMarketing" class="col-sm-6 col-form-label">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="dataUserMarketingToggler">
                                        <label for="dataUserMarketing">
                                            + Dengan Marketing
                                        </label>
                                    </div>
                                </label>
                                <div class="col-sm-6">
                                    <select class="form-control select2 mb-4" style="width: 100%;" id="dataUserMarketing" name="dataUserMarketing" disabled="true"><?php
                                        foreach($marketing as $marketing_boy){?>
                                        <option value="{{ $marketing_boy->mark_id }}" data-name="{{ $marketing_boy->name }}">{{ $marketing_boy->name }}</option><?php
                                        }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <h6 class="font-weight-bold">+ Harga By Approval</h6>
                            </div>
                            <hr>

                            <div class="form-group row mb-0">
                                <label for="dataMembershipApproval" class="col-sm-6 col-form-label">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="dataMembershipApproval">
                                        <label for="dataMembershipApproval">
                                            + Paket Member By Approval
                                        </label>
                                    </div>
                                </label>

                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-danger w-100" id="changeApprovalBtn" data-target="#approvalModal" data-toggle="modal" disabled="true">
                                    -
                                    </button>
                                </div>
                            </div>
                            <div class="form-group row mb-0" id="sesiApprovalContainer" style="display: none;">
                                <label for="dataSesiApproval" class="col-sm-6 col-form-label">
                                    <div class="icheck-success d-inline">
                                        <input type="checkbox" id="dataSesiApproval">
                                        <label for="dataSesiApproval">
                                            + Paket PT By Approval
                                        </label>
                                    </div>
                                </label>

                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-danger w-100" id="changeApprovalSesiBtn" data-target="#approvalSesiModal" data-toggle="modal" disabled="true">
                                    -
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" id="cacheMemberPT" name="cacheMemberPT" readonly>
                            <input type="hidden" id="cacheMemberSession" name="cacheMemberSession" readonly>
                            <input type="hidden" id="cacheMemberSessionTitle" name="cacheMemberSessionTitle" readonly>
                            <input type="hidden" id="cacheMemberSessionPrice" name="cacheMemberSessionPrice" readonly>
                            <input type="hidden" id="cacheMemberMarketing" name="cacheMemberMarketing" readonly>
                            <input type="hidden" id="cacheMemberApproval" name="cacheMemberApproval" readonly>
                            <input type="hidden" id="cachePTApproval" name="cachePTApproval" readonly>

                            <hr>
                            <div class="form-group row mb-0" id="familyMemberGroup" style="display: none;">
                                <label for="dataUserFamily" class="col-sm-6 col-form-label">
                                    <i class="fas fa-users fa-sm mr-1"></i> Family Member
                                </label>
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-dark w-100" id="dataUserFamily" data-target="#familyModal" data-toggle="modal">
                                        <span class="fas fa-plus fa-sm mr-1"></span> Tambah Family
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 mt-3 mb-3">
                            <button type="button" class="btn btn-secondary w-100" id="prevBTN-1" onclick="continueToPreviousStep(1, this);">
                                <i class="fas fa-arrow-left fa-sm pr-2"></i> Kembali
                            </button>
                        </div>
                        <div class="col-md-4 mt-3 mb-3">
                            <button type="button" class="btn btn-primary w-100" id="nextBTN-2" onclick="continueToNextStep(3, this); refreshConfirm();">
                                <i class="fas fa-arrow-right fa-sm pr-2"></i> Konfirmasi
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FORM CARD -->
            <section class="card" id="card-step-3" style="display: none;">
                    <div class="card-body pt-2 pb-2 pl-3 pr-3">
                        <h4 class="d-inline">Konfirmasi Member Baru</h4><hr>

                        <div class="row">
                            <div class="col col-photo form-group">
                                <i class="fas fa-camera fa-2x text-light" style="position: absolute; left: 50%; top: 125px; transform: translate(-30px, -25px)"></i>
                                <img width="250px" height="250px" data-target="#webcamModal" data-toggle="modal" data-backdrop="static" style="background-color: gray;" onclick="openWebcam();" id="photo">
                                <input type="hidden" id="photoFile" name="photoFile">
                            </div>
                            <div class="col-md-6 col-sm-12 form-group">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="d-inline"><b id="confirm_name">ARYAPUTRA BHADRIKA IHSAN PRATAMA</b></h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>Jenis Kelamin:</b><br> <span id="confirm_gender">Laki-laki</span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>Tanggal Lahir:</b><br> <span id="confirm_dob"> - </span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>Pekerjaan:</b><br> <span id="confirm_job">Mahasiswa</span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>No. Telp:</b><br> <span id="confirm_phone">0895372204160</span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>Perusahaan:</b><br> <span id="confirm_agency">PT Maxxima Innovative Engineering</span></h6>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="member-detail-show"><b>Email:</b><br> <span id="confirm_email">aryaputrabip@gmail.com</span></h6>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 form-group">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="attachment-block clearfix" style="padding: 5px 15px;">
                                            <h5 class="attachment-heading"><span id="confirm_membership_name">Membership A</span></h5>
                                            <div class="attachment-text"><b>Durasi:</b> <span id="confirm_membership_duration"> - </span></div>
                                        </div>
                                        <div class="attachment-block clearfix" id="container_confirm_pt" style="padding: 5px 10px; display: block;">
                                            <h5 class="attachment-heading">Personal Trainer</h5>
                                            <div class="attachment-text"><b>(<span id="confirm_pt_name">Belum Ditentukan</span>)</b></div>
                                            <div class="attachment-text"><b>Jenis Sesi:</b> <span id="confirm_pt_session_title"> - </span></div>
                                            <div class="attachment-text"><b>Total Sesi:</b> <span id="confirm_pt_session"> - </span></div>
                                        </div>
                                        <div class="attachment-block clearfix" id="container_confirm_marketing" style="padding: 5px 10px; display: block;">
                                            <h5 class="attachment-heading">Marketing</h5>
                                            <div class="attachment-text"><b>(<span id="confirm_marketing">Mr. A</span>)</b></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8 mb-2">
                                <button type="button" class="btn btn-secondary w-100" id="prevBTN-2" onclick="continueToPreviousStep(2,this); refreshConfirm();">
                                    <i class="fas fa-arrow-left fa-sm pr-2"></i> Kembali
                                </button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button type="button" class="btn btn-primary w-100" id="nextBTN-3" onclick="continueToNextStep(4,this); refreshCharge();">
                                    <i class="fas fa-arrow-right fa-sm pr-2"></i> Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

            <!-- FORM CARD -->
            <section class="card" id="card-step-4" style="display: none;">
                <div class="card-body pt-2 pb-2 pl-3 pr-3">
                    <h4 class="d-inline">Pembayaran</h4><hr>

                    <div class="row form-group">
                        <div class="col-12">
                            <div class="card">
                                <div class="row p-3">
                                    <label for="paymentGroup" class="col-sm-9 col-form-label">
                                        Pilih Pembayaran<span class="text-danger">*</span>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-caret-up"></i>
                                        </button>
                                    </label>
                                </div>
                                <div class="card-body pt-0" id="paymentGroup" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;"><?php
                                    foreach($payment as $pay){?>
                                    <div class="attachment-block attachment-block-selector clearfix" id="payment-{{ $pay->id }}" style="padding: 15px; cursor: pointer;" data-payment="{{ $pay->payment }}" onclick="selectPaymentModel({{ $pay->id }}, this);">
                                        <h6 class="attachment-heading" id="payment-{{ $pay->id }}-name"><i class="{{ $pay->icon }} mr-2"></i>{{ $pay->payment }}</h6>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <input type="hidden" id="cachePaymentModel" name="cachePaymentModel" readonly>
                                <input type="hidden" id="cachePaymentType" name="cachepaymentType" readonly>
                            </div>

                            <div class="card">
                                <div class="row pt-3 pr-3 pb-2 pl-3">
                                    <label for="paymentMethodGroup" class="col-sm-9 col-form-label">
                                        Metode Pembayaran<span class="text-danger">*</span>
                                    </label>
                                </div>
                                <div class="card-body pt-0" id="paymentMGroup" style="max-height: 225px; overflow-y: auto; overflow-x: hidden;">
                                    <select class="form-control select2 w-auto float-left mr-2" style="min-width: 200px;" id="paymentMethodGroup" name="paymentMethodGroup">
                                        <option value="full" selected>Lunas</option>
                                        <option value="tunda">Tunda Bayar (30 Hari)</option>
                                        <option value="cicilan">Cicilan</option>
                                    </select>
                                    <div id="paymentCicilanDurationContainer" style="display: none;">
                                        <input class="form-control w-auto float-left" type="number" min="2" value="2" style="max-width: 70px;" id="paymentCicilanDuration" name="paymentCicilanDuration">
                                        <h6 class="float-left mt-2 ml-2">Bulan</h6>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6><b>Membership</b></h6>
                                        </div>
                                        <div class="col-md-8">
                                            Rp. <span id="total_membership">0</span>
                                        </div>
                                    </div>
                                    <div class="row" id="totalPTContainer">
                                        <div class="col-md-4">
                                            <h6><b>Personal Trainer</b></h6> <!-- (<span id="total-session">-</span> sesi) -->
                                        </div>
                                        <div class="col-md-8">
                                            Rp. <span id="total_pt">0</span>
                                        </div>
                                    </div>
                                    <div class="row text-right">
                                        <div class="col-12">
                                            <h6 class="font-weight-bold">Total Charge</h6>
                                        </div>
                                    </div>
                                    <div class="row text-right">
                                        <div class="col-12">
                                            <h1><b>Rp. </b><b id="total_price">0</b></h1>
                                            <h4 class="font-weight-bold" id="cicilanChargeContainer" style="display: none;">
                                                <i><b>Rp. </b><b id="cicilan_per_bulan">0</b><small>/bulan</small></i>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-8">
                            <button type="button" class="btn btn-secondary w-100" id="prevBTN-4" onclick="continueToPreviousStep(3,this);">
                                <i class="fas fa-arrow-left fa-sm pr-2"></i> Kembali
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-primary w-100" onclick="continueToNextStep(5,this);">
                                <i class="fas fa-arrow-right fa-sm pr-2"></i> Catatan
                            </button>
                        </div>
                    </div>
                </div>
            </section>

                <!-- FORM CARD -->
                <section class="card" id="card-step-5" style="display: none;">
                    <div class="card-body pt-2 pb-2 pl-3 pr-3">
                        <h4 class="d-inline">Catatan</h4><hr>

                        <div class="row form-group">
                            <div class="col-12 mb-3">
                                <textarea class="form-control w-100" id="dataNote" name="dataNote" rows="6" placeholder="Tambah Catatan (optional)..."></textarea>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-8">
                                <button type="button" class="btn btn-secondary w-100" id="prevBTN-4" onclick="continueToPreviousStep(4,this);">
                                    <i class="fas fa-arrow-left fa-sm pr-2"></i> Kembali
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="continueToNextStep(6,this);">
                                    <i class="fas fa-check fa-sm pr-2"></i> Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

            @include('member.modal.modal_registration')
        </form>
    </div>
@endsection

@section('modal')
    @include('member.plugin.webcam')
@endsection

<script>
    @section('script')
    $(function(){
        $("#dataMembershipApproval").on('change', function(){
            if($(this).prop('checked')){
                $("#changeApprovalBtn").prop('disabled',false);
                $("#cacheMemberApproval").val("usingIt");
                refreshApprovalBtn("#changeApprovalBtn");
            }else{
                $("#changeApprovalBtn").prop('disabled',true);
                $("#cacheMemberApproval").val("");
                refreshApprovalBtn("#changeApprovalBtn");
            }
        });

        $("#dataSesiApproval").on('change', function(){
            if($(this).prop('checked')){
                $("#changeApprovalSesiBtn").prop('disabled',false);
                $("#cachePTApproval").val("usingIt");
                refreshApprovalBtn("#changeApprovalSesiBtn");
            }else{
                $("#changeApprovalSesiBtn").prop('disabled',true);
                $("#cachePTApproval").val("");
                refreshApprovalBtn("#changeApprovalSesiBtn");
            }
        });

        $("#registrationForm").on('keyup keypress keydown', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
            }
        });
    });

    $("#dataUserPTToggler").on('change', function(){
        if($(this).prop('checked')){
            $("#dataUserPT").prop('disabled',false);
            $("#dataUserPTSession").prop('disabled',false);
            $("#cacheMemberPT").val($("#dataUserPT").find(':selected').data('name'));
            $("#cacheMemberSession").val($("#dataUserPTSession").val());
            $("#cacheMemberSessionTitle").val($("#dataUserPTSession").find(':selected').data('title'));
            $("#cacheMemberSessionPrice").val($("#dataUserPTSession").find(':selected').data('price'));

            $("#sesiApprovalContainer").show();

        }else{
            $("#dataUserPT").prop('disabled',true);
            $("#dataUserPTSession").prop('disabled',true);
            $("#cacheMemberPT").val("");
            $("#cacheMemberSession").val("");
            $("#cacheMemberSessionTitle").val("");
            $("#cacheMemberSessionPrice").val("");

            $("#sesiApprovalContainer").hide();
        }

        $("#cachePTApproval").val("");
        $("#approvalSesiPrice").val("");
        refreshApprovalBtn("#changeApprovalSesiBtn");
    });

    $("#membershipFilter").on("change", function(){
        if($(this).val() == ""){
            $(".All-Access").parent().show();
            $(".GYM-Only").parent().show();
        }else if($(this).val() == "All-Access"){
            $(".All-Access").parent().show();
            $(".GYM-Only").parent().hide();
        }else{
            $(".GYM-Only").parent().show();
            $(".All-Access").parent().hide();
        }

        $("#cacheMembership").val("");
        $("#cacheMembershipID").val("");
        $("#cacheMembershipDuration").val("");
        $("#cacheMembershipPrice").val("");
        $("#cacheMembershipCategory").val("");

        if(selectedMembership != null){
            $(selectedMembership).removeClass('block_active');
        }
        selectedMembership = null;
    });

    $("#dataUserMarketingToggler").on('change', function(){
        if($(this).prop('checked')){
            $("#dataUserMarketing").prop('disabled',false);
            $("#cacheMemberMarketing").val($("#dataUserMarketing").find(':selected').data('name'));
        }else{
            $("#dataUserMarketing").prop('disabled',true);
            $("#cacheMemberMarketing").val("");
        }
    });

    $("#paymentMethodGroup").on("change", function(){
       if($(this).val() == "cicilan"){
           $("#paymentCicilanDuration").val(1);
           $("#paymentCicilanDurationContainer").show();

           var tMembership = 0;
           var tSesi = 0;

           if($("#cacheMemberApproval").val() != ""){
               tMembership = $("#approvalPrice").val();
           }else{
               tMembership = $("#cacheMembershipPrice").val();
           }

           if($("#dataUserPTToggler").prop("checked")){
               if($("#cachePTApproval").val() != ""){
                   tSesi = $("#approvalSesiPrice").val();
               }else{
                   tSesi = $("#dataUserPTSession").find(':selected').data('price');
               }
           }

           if($("#paymentCicilanDuration").val() > 0){
               $("#cicilan_per_bulan").html(asRupiah(((parseInt(tMembership) + parseInt(tSesi)) / $("#paymentCicilanDuration").val()).toFixed(0)));
           }

           $("#cicilanChargeContainer").show();
       }else{
           $("#paymentCicilanDuration").val("");
           $("#paymentCicilanDurationContainer").hide();

           $("#cicilanChargeContainer").hide();
       }
    });

    $("#paymentCicilanDuration").on("keyup change", function(){
        if($(this).val() > 0){
            var tMembership = 0;
            var tSesi = 0;

            if($("#cacheMemberApproval").val() != ""){
                tMembership = $("#approvalPrice").val();
            }else{
                tMembership = $("#cacheMembershipPrice").val();
            }

            if($("#dataUserPTToggler").prop("checked")){
                if($("#cachePTApproval").val() != ""){
                    tSesi = $("#approvalSesiPrice").val();
                }else{
                    tSesi = $("#dataUserPTSession").find(':selected').data('price');
                }
            }

            if($("#paymentCicilanDuration").val() > 0){
                $("#cicilan_per_bulan").html(asRupiah(((parseInt(tMembership) + parseInt(tSesi)) / $("#paymentCicilanDuration").val()).toFixed(0)));
            }
        }
    });

    $("#approvalPrice").on('change', function(){
        refreshApprovalBtn("#changeApprovalBtn");
    });

    $("#approvalSesiPrice").on('change', function(){
        refreshApprovalBtn("#changeApprovalSesiBtn");
    });

    $("#dataUserPT").on("change", function(){
        $("#cacheMemberPT").val($("#dataUserPT").find(':selected').data('name'));
    });
    $("#dataUserPTSession").on("change", function(){
        $("#cacheMemberSession").val($("#dataUserPTSession").val());
        $("#cacheMemberSessionTitle").val($("#dataUserPTSession").find(':selected').data('title'));
        $("#cacheMemberSessionPrice").val($("#dataUserPTSession").find(':selected').data('price'));
    });
    $("#dataUserMarketing").on("change", function(){
        $("#cacheMemberMarketing").val($("#dataUserMarketing").find(':selected').data('name'));
    });

    //SWAL INIT
    const Toast = Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000
    });
    function messagingError(){
        Toast.fire({
            icon: 'error',html: 'Data belum lengkap!'
        })
    }
    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',html: message
        })
    }

    //VAR INIT
    var selectedMembership;
    var selectedPayment;


    function continueToNextStep(step, element){
        if(validateInput((step - 1)) == true) {
            $("#" + getCurrentStep(element)).hide();
            $(getNextStep(step)).show();
        }else if(validateInput((step - 1)) == "finish"){
            //DO NOTHING
        }else{
            messagingError();
        }
    }

    function continueToPreviousStep(step, element){
        $("#"+getCurrentStep(element)).hide();
        $(getPreviousStep(step)).show();
    }

    function getCurrentStep(element){
        return $(element).closest('section').attr('id');
    }
    function getNextStep(step){
        return $("#card-step-"+step);
    }
    function getPreviousStep(step){
        return $("#card-step-"+step);
    }

    function selectMembership(selected){
        $("#cacheMembership").val($("#membership-"+selected+"-name").html());
        $("#cacheMembershipID").val(selected);
        $("#cacheMembershipDuration").val($("#membership-"+selected+"-duration").html());
        $("#cacheMembershipPrice").val($("#membership-"+selected+"-price").data('price'));
        $("#cacheMembershipCategory").val($("#membership-"+selected+"-category").val());

        reselectMembershipCard(selected);
        if(asFamilyMember($("#cacheMembershipCategory").val())){
            $("#familyMemberGroup").show();
            refreshFamilyMember($("#cacheMembershipCategory").val());
        }else{
            $("#familyMemberGroup").hide();
        }
    }

    function asFamilyMember(category){
        if(category > 1)
            return true;
            return false;
    }

    function reselectMembershipCard(selected){
        $("#membership-"+selected).addClass('block_active');

        if(selectedMembership != null){
            $(selectedMembership).removeClass('block_active');
        }
        selectedMembership = "#membership-"+selected;
    }

    function refreshApprovalBtn(element){
        if(element == "#changeApprovalBtn"){
            if($("#approvalPrice").val() == "" || $("#approvalPrice").val() == null){
                if($("#dataMembershipApproval").prop('checked')){
                    $(element).html('<i class="fas fa-pencil-alt fa-sm mr-1"></i> Pasang Harga')
                }else{
                    $(element).html(" - ");
                }
            }else{
                $(element).html("Rp. " + asRupiah($("#approvalPrice").val()));
            }
        }else{
            if($("#approvalSesiPrice").val() == "" || $("#approvalSesiPrice").val() == null){
                if($("#dataSesiApproval").prop('checked')){
                    $(element).html('<i class="fas fa-pencil-alt fa-sm mr-1"></i> Pasang Harga')
                }else{
                    $(element).html(" - ");
                }
            }else{
                $(element).html("Rp. " + asRupiah($("#approvalSesiPrice").val()));
            }
        }
    }

    function refreshFamilyMember(){
        $("#family-member-tab").html("");

        if($("#cacheMembershipCategory").val() > 1){
            var active = "active";
            var aria = true;
            var show = "show";

            for(i=1; i<=$("#cacheMembershipCategory").val(); i++){
                if(i > 1){active = ""; aria = false; show = "";}

                addFamilyMemberTab(i, active, aria);
                addFamilyMemberForm(i, active, aria, show);
            }
        }
    }

    function refreshConfirm(){
        $("#confirm_membership_name").html($("#cacheMembership").val());
        $("#confirm_membership_duration").html($("#cacheMembershipDuration").val() + " Bulan");

        if($("#dataUserPTToggler").prop("checked")){
            if($("#dataUserPTSession").find(':selected').data('title') != ""){
                $("#confirm_pt_session_title").html($("#dataUserPTSession").find(':selected').data('title'));
                $("#confirm_pt_session").html($("#dataUserPTSession").val() + " Session");
            }else{
                $("#confirm_pt_session_title").html(" Regular ");
                $("#confirm_pt_session").html($("#dataUserPTSession").val() + " Session");
            }

            if($("#dataUserPT").val() == "nothing"){
                $("#confirm_pt_name").html("Belum Ditentukan");
            }else{
                $("#confirm_pt_name").html($("#dataUserPT").find(':selected').data('name'));
            }

            $("#container_confirm_pt").show();
        }else{
            $("#container_confirm_pt").hide();
        }

        if($("#dataUserMarketingToggler").prop("checked")){
            $("#confirm_marketing").html($("#dataUserMarketing").find(':selected').data('name'));
            $("#container_confirm_marketing").show();
        }else{
            $("#container_confirm_marketing").hide();
        }
    }

    function refreshCharge(){
        if($("#dataMembershipApproval").prop("checked")){
            $("#total_membership").html("<i style='text-decoration: line-through;'>"+asRupiah($("#cacheMembershipPrice").val())+"</i> <b>"+asRupiah($("#approvalPrice").val())+"</b>");
        }else{
            $("#total_membership").html(asRupiah($("#cacheMembershipPrice").val()));
        }

        if($("#dataUserPTToggler").prop("checked")){
            if($("#dataSesiApproval").prop("checked")){
                $("#total_pt").html("<i style='text-decoration: line-through;'>"+asRupiah($("#dataUserPTSession").find(':selected').data('price'))+"</i> <b>"+asRupiah($("#approvalSesiPrice").val())+"</b>");

                generateTotalCharge($("#cacheMembershipPrice").val(), $("#approvalSesiPrice").val());
            }else{
                $("#total_pt").html(asRupiah($("#dataUserPTSession").find(':selected').data('price')));

                generateTotalCharge($("#cacheMembershipPrice").val(), $("#dataUserPTSession").find(':selected').data('price'));
            }

            $("#totalPTContainer").show();
        }else{

            generateTotalCharge($("#cacheMembershipPrice").val(), null);
            $("#totalPTContainer").hide();
        }
    }

    function generateTotalCharge(membership, pt){
        if($("#dataMembershipApproval").prop("checked")){
            if(pt == null){
                $("#total_price").html(asRupiah(parseInt($("#approvalPrice").val())));
            }else{
                $("#total_price").html(asRupiah(parseInt($("#approvalPrice").val()) + parseInt(pt)));
            }
        }else{
            if(pt == null){
                $("#total_price").html(asRupiah(parseInt(membership)));
            }else{
                $("#total_price").html(asRupiah(parseInt(membership) + parseInt(pt)));
            }
        }
    }

    function validateInput(step){
        switch(step){
            case 1:
                if($("#dataUserNama").val() != "" && $("#dataUserGender").val() != "" &&
                    $("#dataUserEmail").val() != "" && $("#dataUserPhone").val() != "")
                    return true;
                    return false;
                break;

            case 2:
                if($("#cacheMembership").val() != ""){
                    if($("#dataUserPTToggler").prop('checked') && $("#dataUserPT").val() == "" ||
                        $("#dataUserPTSession").val() == ""){
                        return false;
                    }
                    if($("#dataUserMarketingToggler").prop('checked') && $("#dataUserMarketing").val() == ""){
                        return false;
                    }
                    if($("#dataMembershipApproval").prop('checked') && $("#approvalPrice").val() == "" ||
                        $("#approvalPrice").val() == null){
                        return false;
                    }

                    if($("#cacheMembershipCategory").val() > 1){
                        for(i=1; i<=$("#cacheMembershipCategory").val(); i++){
                            if($("#family_"+i+"_name").val() == "" || $("#family_"+i+"_gender").val() == "" ||
                            $("#family_"+i+"_email").val() == "" || $("#family_"+i+"_phone").val() == ""){
                                return false;
                            }
                        }
                    }

                    if($("#cachePTApproval").val() != ""){
                        if($("#approvalSesiPrice").val() == ""){
                            return false;
                        }
                    }

                    $("#confirm_name").html($("#dataUserNama").val());
                    $("#confirm_gender").html($("#dataUserGender").val());
                    $("#confirm_phone").html($("#dataUserPhone").val());
                    $("#confirm_email").html($("#dataUserEmail").val());
                    $("#confirm_dob").html($("#dataUserDOB").val());

                    if($("#dataUserCompany").val() == ""){
                        $("#confirm_agency").html("-");
                    }else{
                        $("#confirm_agency").html($("#dataUserCompany").val());
                    }

                    if($("#dataUserJob").val() == ""){
                        $("#confirm_job").html("Tidak Bekerja");
                    }else{
                        $("#confirm_job").html($("#dataUserJob").val());
                    }

                    return true;
                }
                break;

            case 3:
                return true;
                break;

            case 4:
                if($("#cachePaymentModel").val() == ""){
                    messagingErrorCustom("Jenis Pembayaran Belum Dipilih!");
                    return false;
                }else{
                    if($("#cachePaymentModel").val() == "Cash"){
                        if($("#paymentMethodGroup").val() == "cicilan"){
                            if($("#paymentCicilanDuration").val() == ""){
                                messagingErrorCustom("Durasi Cicilan Belum Diisi!");
                                return false;
                            }else{
                                return true;
                            }
                        }else{
                            return true;
                        }
                    }else{
                        if($("#cachePaymentType").val() == ""){
                            messagingErrorCustom("Bank Pembayaran Belum Dipilih!");
                            return false;
                        }else{
                            if($("#paymentMethodGroup").val() == "cicilan"){
                                if($("#paymentCicilanDuration").val() == ""){
                                    messagingErrorCustom("Durasi Cicilan Belum Diisi!");
                                    return false;
                                }else{
                                    return true;
                                }
                            }else{
                                return true;
                            }
                        }
                    }
                }
                break;

            case 5:
                confirmRegister();
                return "finish";
                break;
        }
    }

    function confirmRegister(){
        var token = '{{ csrf_token() }}';

        const ConfirmSwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-primary mr-2',
                cancelButton: 'btn btn-danger mr-2'
            },buttonsStyling: false
        });

        ConfirmSwal.fire({
            icon: 'warning',
            html: 'Apakah Anda Yakin Ingin Menyelesaikan Registrasi ?',
            showCancelButton: true,
            cancelButtonText: `Tidak`,
            confirmButtonText: `<i class="fas fa-check fa-sm"></i> Iya`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.confirm){
                $("#registrationForm").submit();
            }else{
                return false;
            }
        });
    }

    function addFamilyMemberTab(pos, active, aria){
        $("#family-member-tab").append('' +
            '<a class="nav-link '+active+'" id="tabs-family-'+pos+'" data-toggle="pill" ' +
            'href="#family-'+pos+'" role="tab" aria-controls="content-member-'+pos+'" aria-selected="'+aria+'">' +
            'Member '+pos+
            '</a>');
    }

    function addFamilyMemberForm(pos, active, aria, show){
        $("#family-member-content").append('' +
            '<div class="tab-pane text-left fade '+show+' '+active+'" id="family-'+pos+'" role="tabpanel" aria-labelledby="tabs-family-'+pos+'">' +
            '   <div class="row">' +
            '       <div class="col-12">' +
            '           <h4 class="d-inline">Detail Member '+pos+'</h4><hr>' +
            '        </div>' +
            '        <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_name" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   Nama Lengkap<span class="text-danger">*</span>' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <input type="text" class="form-control" id="family_'+pos+'_name" name="family_'+pos+'_name" placeholder="Nama Lengkap">' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '       <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_gender" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   Jenis Kelamin<span class="text-danger">*</span>' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <select class="form-control select2" style="width: 100%;" id="family_'+pos+'_gender" name="family_'+pos+'_gender">' +
            '                       <option value="Laki-laki" selected="selected">Laki-laki</option>' +
            '                       <option value="Perempuan">Perempuan</option>' +
            '                   </select>' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '       <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_email" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   Email<span class="text-danger">*</span>' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <input type="email" class="form-control" id="family_'+pos+'_email" name="family_'+pos+'_email" placeholder="Email">' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '       <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_phone" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   No Telp.<span class="text-danger">*</span>' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <input type="text" class="form-control" id="family_'+pos+'_phone" name="family_'+pos+'_phone" placeholder="No. Telp.">' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '       <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_job" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   Pekerjaan' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <select class="form-control select2" style="width: 100%;" id="family_'+pos+'_job" name="family_'+pos+'_job">' +
            '                       <option value="" selected="selected"><b>Tidak Bekerja</b></option>' +
            '                       <option value="Karyawan">Karyawan</option>' +
            '                       <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>' +
            '                       <option value="Lainnya">Lainnya...</option>' +
            '                   </select>' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '       <div class="col-12">' +
            '           <div class="form-group row">' +
            '               <label for="family_'+pos+'_company" class="col-sm-3 col-form-label font-weight-normal">' +
            '                   Perusahaan' +
            '               </label>' +
            '               <div class="col-sm-9">' +
            '                   <input type="text" class="form-control" id="family_'+pos+'_company" name="family_'+pos+'_company" placeholder="Nama Perusahaan / Instansi Member Bekerja">' +
            '               </div>' +
            '           </div>' +
            '       </div>' +
            '   </div>' +
            '</div>');
    }

    function selectPaymentModel(type, element){
        reselectPaymentCard(type);
        $("#cachePaymentModel").val($(element).data('payment'));
        $("#cachePaymentType").val("");

        switch(type){
            case 2:
                $('#paymentDebitModal').modal('show');
                break;

            case 3:
                $('#paymentCreditModal').modal('show');
                break;
        }
    }

    function selectPaymentType(type, element){
        reselectPaymentBank(type);
        $("#cachePaymentType").val($(element).data('bank'));
    }

    function reselectPaymentCard(selected){
        $("#payment-"+selected).addClass('block_active');

        if(selectedPayment != null){
            $(selectedPayment).removeClass('block_active');
        }
        selectedPayment = "#payment-"+selected;
    }

    function reselectPaymentBank(selected) {
        $("#bank-"+selected).addClass('block_active');

        if(selectedPayment != null){
            $(selectedPayment).removeClass('block_active');
        }
        selectedPayment = "#bank-"+selected;
    }

    function openWebcam(){
        var videocam = document.querySelector("#memberCapture");

        if (navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    videocam.srcObject = stream;
                }).catch(function (error) {
                    messagingErrorCustom("Webcam is not accessible!");
                    console.log("Webcam is not accessible!");
                });
            }
    }

    function closeCam(){
        var videocam = document.querySelector("#memberCapture");
        var stream = videocam.srcObject;
        var tracks = stream.getTracks();

        for (var i = 0; i < tracks.length; i++) {
            var track = tracks[i];
            track.stop();
        }

        videocam.srcObject = null;
    }

    video = document.getElementById('memberCapture');
    canvas = document.getElementById('canvas');
    photo = document.getElementById('photo');
    width = 400;
    height = 400;

    function takePicture() {
        var context = canvas.getContext('2d');
        if (width && height) {
            canvas.width = width;
            canvas.height = height;
            context.drawImage(video, 0, 0, width, height);

            var data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
            closeCam();
            $("#webcamModal").modal('hide');
            $("#photoFile").val(data);
        } else {
            clearPhoto();
            closeCam();
            $("#webcamModal").modal('hide');
        }
    }

    function clearPhoto() {
        var context = canvas.getContext('2d');
        context.fillStyle = "#AAA";
        context.fillRect(0, 0, canvas.width, canvas.height);

        var data = canvas.toDataURL('image/png');
        photo.setAttribute('src', data);
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

    function isEmail(email){
        return /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test( email );
    }

    @endsection
</script>
