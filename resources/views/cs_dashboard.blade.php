@extends('layouts.app_cs')

<style>
    @section('css')
    .info-box:hover{
        background-color: #ececec !important;
        cursor: pointer;
        transition: 0.2s;
    }
    @endsection
</style>

@section('content')

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box" onclick="dataToggle('member_total');">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text font">Total Member</span>
                    <span class="info-box-value">{{ $tMember }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box" onclick="dataToggle('member_new');">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-user-plus"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Member Baru (hari ini)</span>
                    <span class="info-box-value">{{ $tMemberBaru }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box" onclick="dataToggle('member_activity');">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-calendar-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Aktivitas (hari ini)</span>
                    <span class="info-box-value">{{ $tAktivitas }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box" onclick="dataToggle('revenue');">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-dollar-sign"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Revenue (hari ini)</span>
                    <span class="info-box-value">{{ $tRevenue }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('cs.member.checkin') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-calendar-check mr-1"></span> Check-In
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cs.member.registration.index') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-user-plus mr-1"></span> Tambah Member
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cs.sesi.index') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-user mr-1"></span> Gunakan Sesi
            </a>
        </div>
    </div>


    <!-- MODAL SECTION -->
    <div class="modal fade" id="modal-chart-member-new">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h6 class="modal-title font-weight-bold">
                        <i class="fas fa-users fa-sm mr-1"></i> Member Baru
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pr-0">
                    <!-- CHART SECTION -->
                    <div class="row">
                        <div style="position:absolute; right: 20px; z-index: 10;">
                            <select class="form-control" id="filterTypeMemberNew" name="filterTypeMemberNew" onchange="changeFilterType('memberNew', this.id)">
                                <option value="day">Filter By (Daily)</option>
                                <option value="month" selected>Filter By (Monthly)</option>
                                <option value="year">Filter By (Yearly)</option>
                            </select>
                        </div>
                        <div style="max-width: 100%; overflow-x: auto;" id="memberNewMonthlyFrame" class="col-12">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member monthly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>
                        <div style="max-width: 100%; overflow-x: auto;" id="memberNewDailyFrame" class="col-12" style="display: none;">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member daily&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>

                        <div style="max-width: 100%; overflow-x: auto;" id="memberNewYearlyFrame" class="col-12" style="display: none;">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member yearly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>
                    </div>
                    <!-- END OF CHART SECTION -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-chart-revenue">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h6 class="modal-title font-weight-bold">
                        <i class="fas fa-chart-line fa-sm mr-1"></i> Total Revenue
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pr-0">
                    <div class="row">
                        <div style="position:absolute; right: 20px; z-index: 10;">
                            <select class="form-control" id="filterTypeRevenue" name="filterTypeRevenue" onchange="changeFilterType('revenue', this.id)">
                                <option value="day">Filter By (Daily)</option>
                                <option value="month" selected>Filter By (Monthly)</option>
                                <option value="year">Filter By (Yearly)</option>
                            </select>
                        </div>
                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueMonthlyFrame" class="col-12">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue monthly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>
                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueDailyFrame" class="col-12" style="display: none;">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue daily&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>

                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueYearlyFrame" class="col-12" style="display: none;">
                            <iframe width="1000" height="500" class="w-100"
                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue yearly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"
                                    frameborder="0">
                            </iframe>
                        </div>
                    </div>
                    <!-- END OF CHART SECTION -->
                </div>
            </div>
        </div>
    </div>
    <!-- END OF MODAL SECTION -->
@endsection

<script>
    @section('script')
    $(function () {
        $("#memberNewDailyFrame").hide();
        $("#memberNewMonthlyFrame").show();
        $("#memberNewYearlyFrame").hide();

        $("#RevenueDailyFrame").hide();
        $("#RevenueMonthlyFrame").show();
        $("#RevenueYearlyFrame").hide();
    });

    function dataToggle(data){
        switch(data) {
            case "member_total":
                $("#modal-chart-member").modal("toggle");
                break;
            case "member_new":
                $("#modal-chart-member-new").modal("toggle");
                break;
            case "member_activity":
                $("#modal-chart-member-activity").modal("toggle");
                break;
            case "revenue":
                $("#modal-chart-revenue").modal("toggle");
                break;
        }
    }

    function changeFilterType(filterType, action){
        var actionData = $("#" + action).val();

        switch(filterType) {
            case "member":
                if(actionData == "day"){
                    $("#memberDailyFrame").show();
                    $("#memberMonthlyFrame").hide();
                    $("#memberYearlyFrame").hide();
                }else if(actionData == "month"){
                    $("#memberDailyFrame").hide();
                    $("#memberMonthlyFrame").show();
                    $("#memberYearlyFrame").hide();
                }else{
                    $("#memberDailyFrame").hide();
                    $("#memberMonthlyFrame").hide();
                    $("#memberYearlyFrame").show();
                }
                break;
            case "memberNew":
                if(actionData == "day"){
                    $("#memberNewDailyFrame").show();
                    $("#memberNewMonthlyFrame").hide();
                    $("#memberNewYearlyFrame").hide();
                }else if(actionData == "month"){
                    $("#memberNewDailyFrame").hide();
                    $("#memberNewMonthlyFrame").show();
                    $("#memberNewYearlyFrame").hide();
                }else{
                    $("#memberNewDailyFrame").hide();
                    $("#memberNewMonthlyFrame").hide();
                    $("#memberNewYearlyFrame").show();
                }
                break;
            case "member_cuti":
                if(actionData == "day"){
                    $("#memberCutiDailyFrame").show();
                    $("#memberCutiMonthlyFrame").hide();
                    $("#memberCutiYearlyFrame").hide();
                }else if(actionData == "month"){
                    $("#memberCutiDailyFrame").hide();
                    $("#memberCutiMonthlyFrame").show();
                    $("#memberCutiYearlyFrame").hide();
                }else{
                    $("#memberCutiDailyFrame").hide();
                    $("#memberCutiMonthlyFrame").hide();
                    $("#memberCutiYearlyFrame").show();
                }
                break;
            case "revenue":
                if(actionData == "day"){
                    $("#RevenueDailyFrame").show();
                    $("#RevenueMonthlyFrame").hide();
                    $("#RevenueYearlyFrame").hide();
                }else if(actionData == "month"){
                    $("#RevenueDailyFrame").hide();
                    $("#RevenueMonthlyFrame").show();
                    $("#RevenueYearlyFrame").hide();
                }else{
                    $("#RevenueDailyFrame").hide();
                    $("#RevenueMonthlyFrame").hide();
                    $("#RevenueYearlyFrame").show();
                }
                break;
        }
    }
    @endsection
</script>
