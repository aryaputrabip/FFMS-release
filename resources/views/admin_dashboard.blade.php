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
                <div class="modal-header bg-danger">
                    <h6 class="modal-title font-weight-bold">
                            <i class="fas fa-users fa-sm mr-1"></i> Total Member
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- CHART FILTER SECTION -->
                    <div class="row">
                        <div class="col-12">
                            <span class="float-left">
                                <select class="form-control" id="filterTypeMember" name="filterTypeMember">
                                    <option value="day">Filter By (Daily)</option>
                                    <option value="month" selected>Filter By (Monthly)</option>
                                    <option value="year">Filter By (Yearly)</option>
                                </select>
                            </span>
                            <span class="float-right">
                                <select class="form-control" id="filterYearMember" name="filterYearMember">
                                    <option value=""><b>Tahun (ALL)</b></option>
                                    @foreach($yearlist as $year)
                                        <option value="{{ $year }}" @if($year == $current_year) selected @endif>Tahun ({{ $year }})</option>
                                    @endforeach
                                </select>
                            </span>
                            <span class="float-right mr-2">
                                <select class="form-control" id="filterMonthMember" name="filterMonthMember" style="display: none;">
                                    @foreach($monthList as $month)
                                        <option value="{{ $month[1] }}" @if($month[1] == $current_month) selected @endif>Bulan ({{ $month[0] }})</option>
                                    @endforeach
                                </select>
                            </span>
                        </div>
                    </div>
                    <!-- END OF CHART FILTER SECTION -->

                    <!-- CHART SECTION -->
                    <div class="row">
                        <div style="max-width: 100%; overflow-x: auto;" id="memberFrame">
                            {!! $memberChart->render() !!}
                        </div>
                    </div>
                    <!-- END OF CHART SECTION -->

                    <!-- TABLE SECTION -->
                    <button class="btn btn-primary w-100 mt-4 mb-3" disabled>
                        <i class="fas fa-download fa-sm mr-1"></i> Export Data
                    </button>
                    <div class="row">
                        <div class="col-12">
                            <table id="data_member" class="table table-bordered table-hover text-nowrap w-100">
                                <thead>
                                <tr>
                                    <th colspan="5" class="text-center">
                                        <b id="labelMemberParentYear" style="display: none">Total Member - Tahunan</b>
                                        <b id="labelMemberParentNonYear" >Total Member - Tahun (<span id="labelMemberYear">{{ $current_year }}</span>)</b>
                                    </th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th class="font-weight-normal" id="data_member_index_title">Bulan</th>
                                    <th class="font-weight-normal">Member (Laki-laki)</th>
                                    <th class="font-weight-normal">Member (Perempuan)</th>
                                    <th>Total Member</th>
                                </thead>
                            </table>
                    </div>
                    <!-- END OF TABLE SECTION -->
                </div>
            </div>
        </div>
    </div>
    <!-- END OF MODAL SECTION -->
    </div>
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
        const options = {
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            columns: [
                { name: "DT_RowIndex" },
                { name: "bulan" },
                { name: "total_lk" },
                { name: "total_pr" },
                { name: "total" }
            ]
        };

        $('#data_member').DataTable(options);
    });

    $("#modal-chart-member").on('hidden.bs.modal', function () {
        $('#data_member').DataTable().clear().draw();
    });

    $("#filterTypeMember").on("change", function(){
       if($(this).val() == "day"){
           $("#filterMonthMember").show();
           $("#filterYearMember").show();

           $("#labelMemberParentNonYear").show();
           $("#labelMemberParentYear").hide();
       }else if($(this).val() == "month"){
           $("#filterMonthMember").hide();
           $("#filterYearMember").show();

           $("#labelMemberParentNonYear").show();
           $("#labelMemberParentYear").hide();
       }else{
           $("#filterMonthMember").hide();
           $("#filterYearMember").hide();

           $("#labelMemberParentNonYear").hide();
           $("#labelMemberParentYear").show();
       }

        refreshChartTable('member');
    });

    $("#filterMonthMember").on("change", function(){
        refreshChartTable('member');
    });

    $("#filterYearMember").on("change", function(){
        if($(this).val() == ""){
            $("#labelMemberYear").html("ALL");
        }else{
            $("#labelMemberYear").html($(this).val());
        }

        refreshChartTable('member');
    });

    function refreshChartTable(data){
        switch(data){
            case "member":
                $('#data_member').DataTable().clear().draw();
                showChart("member_total", $("#filterTypeMember").val(), $("#filterMonthMember").val(), $("#filterYearMember").val());
                settingDatatables("member", $("#filterTypeMember").val(), $("#filterMonthMember").val(), $("#filterYearMember").val());
                break;
        }
    }

    function settingDatatables(type, filterType, filterMonth, filterYear){
        if(filterType == "day"){
            $("#data_member_index_title").html("Hari");
        }else if(filterType == "month"){
            $("#data_member_index_title").html("Bulan");
        }else{
            $("#data_member_index_title").html("Tahun");
        }

        if(type == "member"){
            $.ajax({
                type: 'GET',
                dataType: 'html',
                url: "{{ route('suadmin.getMemberData') }}",
                data: {
                    filterType: filterType,
                    filterMonth: filterMonth,
                    filterYear: filterYear
                },
                success: function(data){
                    const dataset = JSON.parse(data);
                    console.log(dataset);
                    updateDatatable("data_member", dataset);
                }
            });
        }
    }

    function updateDatatable(table, data){
        switch(table){
            case "data_member":
                var table = $('#data_member').DataTable();
                table.clear();
                table.rows.add(data).draw();
                break;
        }
    }

    function dataToggle(data){
        switch(data) {
            case "member_total":
                $("#modal-chart-member").modal("toggle");
                settingDatatables("member", $("#filterTypeMember").val(), $("#filterMonthMember").val(), $("#filterYearMember").val());

                showChart("member_total", $("#filterTypeMember").val(), $("#filterMonthMember").val(), $("#filterYearMember").val());
            break;
            case "member_aktif":

            break;
            case "member_cuti":

            break;
            case "revenue":

            break;
        }
    }

    function showChart(chart, filterType, filterMonth, filterYear) {
        switch (chart) {
            case "member_total":
                $.ajax({
                    type: 'GET',
                    dataType: 'html',
                    url: "{{ route('report.updateMemberChartData') }}",
                    data: {
                        type: filterType,
                        month: filterMonth,
                        year: filterYear
                    },
                    success: function (data) {
                        var obj = JSON.parse(data);

                        $("#memberFrame").html('<canvas id="memberChart" width="4000" height="1400"></canvas>');

                        var ctxMember = document.getElementById('memberChart').getContext('2d');
                        var chartMember = new Chart(ctxMember, {
                            type: 'line',
                            data: setData("member", [obj.memberData, obj.memberLK, obj.memberPR, obj.memberBaru]),
                            options: setOptions('Member Data', 'top', 0),
                        });
                    }
                });
                break;
            case "member_aktif":

                break;
            case "member_cuti":

                break;
            case "revenue":

                break;
        }
    }
    @endsection
</script>
