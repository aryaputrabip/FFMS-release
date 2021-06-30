<div class="input-group-prepend" id="chart_filter_group">
    <select data-column="3" class="form-control form-control-sm w-auto" id="tableFilterChartType">
        <option value="daily" class="font-weight-bold">Filter By (Daily)</option>
        <option value="monthly" selected>Filter By (Monthly)</option>
        <option value="yearly">Filter By (Yearly)</option>
    </select>

    <select data-column="13" class="form-control form-control-sm w-auto ml-2" id="tableFilterChartMonth" style="display: none;">
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

    <select data-column="3" class="form-control form-control-sm ml-2 w-auto" id="tableFilterChartYear">
        <option value="all" class="font-weight-bold" selected>Tahun (All)</option>
        @foreach($filter_year_available as $FILTER_YEAR)
            <option value="{{ $FILTER_YEAR->date }}">{{ $FILTER_YEAR->date }}</option>
        @endforeach
    </select>

    <select data-column="5" class="form-control form-control-sm ml-2 w-auto" id="tableFilterChartYearDuration" style="display: none">
        <option value="2">2 Years</option>
        <option value="5" selected>5 Years</option>
        <option value="10">10 Years</option>
        <option value="15">15 Years</option>
    </select>
</div>

<script>
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
</script>
