@extends('layouts.app_admin')

<style>
    @section('css')
    .info-box.bg-info:hover{
        background-color: #068599 !important;
        cursor: pointer;
        transition: 0.2s;
    }

    .info-box.bg-success:hover{
        background-color: #1c9037 !important;
        cursor: pointer;
        transition: 0.2s;
    }

    .info-box.bg-danger:hover{
        background-color: #c12232 !important;
        cursor: pointer;
        transition: 0.2s;
    }

    .info-box.bg-warning:hover{
        background-color: #f3ba00 !important;
        cursor: pointer;
        transition: 0.2s;
    }

    @endsection
</style>

@section('content')
    <div class="container-fluid">
        <!-- CARD SECTION -->
        <div class="row">
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="info-box bg-info" onclick="dataToggle('member_total');">
                    <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-users"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text font">Total Member</span>
                        <span class="info-box-value">{{ $jMember }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="info-box bg-success" onclick="dataToggle('member_active');">
                    <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-user-check"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Member Aktif</span>
                        <span class="info-box-value">{{ $memberActive }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="info-box bg-danger" onclick="dataToggle('member_cuti');">
                    <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-calendar-minus"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Member Cuti</span>
                        <span class="info-box-value">{{ $memberCuti }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="info-box bg-warning" onclick="dataToggle('revenue');">
                    <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-dollar-sign"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">Total Sales</span>
                        <span class="info-box-value">{{ $totalSales }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END CARD SECTION -->
    </div>

    <!-- MODAL SECTION -->
    <div class="modal fade" id="modal-chart-member">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h6 class="modal-title font-weight-bold">
                        <i class="fas fa-users fa-sm mr-1"></i> Total Member
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pr-0">
                    <!-- CHART SECTION -->
{{--                    <div class="row">--}}
{{--                        <div style="position:absolute; right: 20px; z-index: 10;">--}}
{{--                            <select class="form-control" id="filterTypeMember" name="filterTypeMember" onchange="changeFilterType('member', this.id)">--}}
{{--                                <option value="day">Filter By (Daily)</option>--}}
{{--                                <option value="month" selected>Filter By (Monthly)</option>--}}
{{--                                <option value="year">Filter By (Yearly)</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberMonthlyFrame" class="col-12">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member monthly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberDailyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member daily&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}

{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberYearlyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member yearly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="row" id="filterContainerMember">
                        @include('chart.filter.filter_group')
                    </div>

                    <div class="row">
                        <div class="col-12">
                            @include('chart.total_member_chart')
                        </div>
                    </div>
                    <!-- END OF CHART SECTION -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-chart-cuti">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h6 class="modal-title font-weight-bold">
                        <i class="fas fa-calendar-minus fa-sm mr-1"></i> Total Member Cuti
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pr-0">
{{--                    <div class="row">--}}
{{--                        <div style="position:absolute; right: 20px; z-index: 10;">--}}
{{--                            <select class="form-control" id="filterTypeCuti" name="filterTypeCuti" onchange="changeFilterType('member_cuti', this.id)">--}}
{{--                                <option value="day">Filter By (Daily)</option>--}}
{{--                                <option value="month" selected>Filter By (Monthly)</option>--}}
{{--                                <option value="year">Filter By (Yearly)</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberCutiMonthlyFrame" class="col-12">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member Cuti monthly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberCutiDailyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member Cuti daily&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}

{{--                        <div style="max-width: 100%; overflow-x: auto;" id="memberCutiYearlyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Member Cuti yearly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="row" id="filterContainerCuti"></div>
                    <div class="row">
                        <div class="col-12">
                            @include('chart.member_cuti_chart')
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
{{--                    <div class="row">--}}
{{--                        <div style="position:absolute; right: 20px; z-index: 10;">--}}
{{--                            <select class="form-control" id="filterTypeRevenue" name="filterTypeRevenue" onchange="changeFilterType('revenue', this.id)">--}}
{{--                                <option value="day">Filter By (Daily)</option>--}}
{{--                                <option value="month" selected>Filter By (Monthly)</option>--}}
{{--                                <option value="year">Filter By (Yearly)</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueMonthlyFrame" class="col-12">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue monthly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueDailyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue daily&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}

{{--                        <div style="max-width: 100%; overflow-x: auto;" id="RevenueYearlyFrame" class="col-12" style="display: none;">--}}
{{--                            <iframe width="1000" height="500" class="w-100"--}}
{{--                                    src="http://localhost:8080/knowage/public/servlet/AdapterHTTP?ACTION_NAME=EXECUTE_DOCUMENT_ACTION&OBJECT_LABEL=Revenue yearly&TOOLBAR_VISIBLE=false&ORGANIZATION=DEMO"--}}
{{--                                    frameborder="0">--}}
{{--                            </iframe>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div class="row" id="filterContainerRevenue"></div>
                    <div class="row">
                        <div class="col-12">
                            @include('chart.revenue_chart')
                        </div>
                    </div>
                    <!-- END OF CHART SECTION -->
                </div>
            </div>
        </div>
    </div>
        <!-- END OF MODAL SECTION -->
@endsection



@section('import_script')
    @include('theme.default.import.modular.datatables.script')
    @include('config.session.request_session')
    @include('config.swal.swal_message')
    @include('config.chart.chart_init')
@endsection

<script>
    @section('script')

    $(function () {
        $("#memberDailyFrame").hide();
        $("#memberMonthlyFrame").show();
        $("#memberYearlyFrame").hide();

        $("#memberCutiDailyFrame").hide();
        $("#memberCutiMonthlyFrame").show();
        $("#memberCutiYearlyFrame").hide();

        $("#RevenueDailyFrame").hide();
        $("#RevenueMonthlyFrame").show();
        $("#RevenueYearlyFrame").hide();
    });

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
            case "member_aktif":

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

    function dataToggle(data){
        switch(data) {
            case "member_total":
                $("#modal-chart-member").modal("toggle");
                $("#chart_filter_group").appendTo("#filterContainerMember");
            break;
            case "member_cuti":
                $("#modal-chart-cuti").modal("toggle");
                $("#chart_filter_group").appendTo("#filterContainerCuti");
            break;
            case "revenue":
                $("#modal-chart-revenue").modal("toggle");
                $("#chart_filter_group").appendTo("#filterContainerRevenue");
            break;
        }
    }

    function refreshChart(){
        refreshMemberChart();
        refreshCutiChart();
        refreshRevenueChart();
    }

    function setChartContextData(id){
        return document.getElementById(id).getContext('2d');
    }

    function setChartData(category, labels, datasetTotal, dataset_2, dataset_3){
        switch(category){
            case "member":
                var data = initMemberChart(labels, datasetTotal, dataset_2, dataset_3);
                break;
            case "cuti":
                var data = initCutiChart(labels, datasetTotal);
                break;
            case "revenue":
                var data = initRevenueChart(labels, datasetTotal, dataset_2, dataset_3);
                break;
        }

        return data;
    }

    //REFRESH CHART ON PAGE START
    refreshChart();
    @endsection
</script>
