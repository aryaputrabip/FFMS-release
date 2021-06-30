@extends($app_layout)

@section('content')
    <div class="container-fluid">
        <!-- STATISTIC CARD -->
        <div class="card mb-3">
            <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>Filter Data</b>
                <div class="card-tools mr-0">
                    <button type="button" class="btn btn-tool mt-0" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="input-group-prepend" id="chart_filter_group">
                    <select data-column="3" class="form-control w-100" id="tableFilterChartType">
                        <option value="daily" class="font-weight-bold">Filter By (Daily)</option>
                        <option value="monthly" selected>Filter By (Monthly)</option>
                        <option value="yearly">Filter By (Yearly)</option>
                    </select>

                    <select data-column="13" class="form-control w-100 ml-2" id="tableFilterChartMonth" style="display: none;">
                        <option value="all" class="font-weight-bold" selected>Bulan (All)</option>
                        <option value="1">Januari</option>
                        <option value="2">Februari</option>
                        <option value="3">Maret</option>
                        <option value="4">April</option>
                        <option value="5">Mei</option>
                        <option value="6">Juni</option>
                        <option value="7">Juli</option>
                        <option value="8">Agustus</option>
                        <option value="9">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>

                    <select data-column="3" class="form-control ml-2 w-100" id="tableFilterChartYear">
                        <option value="all" class="font-weight-bold" selected>Tahun (All)</option>
                        @foreach($filter_year_available as $FILTER_YEAR)
                            <option value="{{ $FILTER_YEAR->date }}">{{ $FILTER_YEAR->date }}</option>
                        @endforeach
                    </select>

                    <select data-column="5" class="form-control ml-2 w-100" id="tableFilterChartYearDuration" style="display: none">
                        <option value="2">2 Years</option>
                        <option value="5" selected>5 Years</option>
                        <option value="10">10 Years</option>
                        <option value="15">15 Years</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- DATA CARD -->
        <div class="card p-2">
            <div class="card-body p-0 pt-2 mb-2">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-center">Performa Profit</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="revenueFrame">
                            @include('chart.revenue_chart')
                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Performa Aktivitas Member</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="activityFrame">

                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Performa Member</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="memberFrame">
                            @include('chart.total_member_chart')
                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Performa Marketing</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="marketingFrame">

                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Top 10 Marketing</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="topMarketingFrame">

                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Performa Personal Trainer</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="ptFrame">

                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Top 10 Personal Trainer</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="topPTFrame">

                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <hr>
                        <h2 class="text-center">Performa Cuti Member</h2>
                    </div>
                    <div class="col-12">
                        <div style="max-width: 100%; overflow-x: auto;" id="cutiFrame">
                            @include('chart.member_cuti_chart')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- page script -->
<script>
    @section('script')
    $("#tableFilterChartMonth").on("change", function(){
        refreshChart();
    });

    $("#tableFilterChartYear").on("change", function(){
        refreshChart();
    });

    $("#tableFilterChartYearDuration").on("change", function(){
        refreshChart();
    });

    $("#tableFilterChartType").on("change", function(){
        switch($(this).val()){
            case "daily":
                $("#tableFilterChartMonth").show();
                $("#tableFilterChartYear").show();
                $("#tableFilterChartYearDuration").hide();
                refreshChart();
                break;
            case "monthly":
                $("#tableFilterChartMonth").hide();
                $("#tableFilterChartYear").show();
                $("#tableFilterChartYearDuration").hide();
                refreshChart();
                break;
            case "yearly":
                $("#tableFilterChartMonth").hide();
                $("#tableFilterChartYear").show();
                $("#tableFilterChartYearDuration").show();
                refreshChart();
                break;
        }
    });

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
