<div class="modal fade" id="modal-cuti-entry" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-dark">
                    <span class="fas fa-calendar-minus mr-1"></span> Waktu Cuti
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body" id="modal-pt-content">
                <div class="form-group row">
                    <label for="dataCutiDuration" class="col-sm-3 col-form-label">
                        Cuti Selama<span class="text-danger">*</span>
                    </label>
                    <div class="col-7">
                        <input type="number" min="1" value="1" class="form-control" id="dataCutiDuration" name="dataCutiDuration">
                    </div>
                    <label class="col-2 col-form-label">
                        Bulan
                    </label>
                </div>

                <form id="cutiForm" action="#" method="POST">
                    <input type="hidden" id="activeMemberID" name="activeMemberID" readonly>
                    <input type="hidden" id="activeCutiDuration" name="activeStartDate" readonly>
                    <input type="hidden" id="endCutiDate" name="endCutiDate" readonly>
                    <input type="hidden" id="oldEndDate" name="oldEndDate" readonly>
                    <input type="hidden" id="newMembershipEnd" name="newMembershipEnd" readonly>

                    {{ csrf_field() }}
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark pt-1 pb-1" data-dismiss="modal">
                    <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
                </button>
                <button type="button" class="btn btn-primary pt-1 pb-1" id="confirmPengajuanCuti">
                    <i class="fas fa-check fa-sm mr-1"></i> Ajukan
                </button>
            </div>
        </div>
    </div>
</div>
