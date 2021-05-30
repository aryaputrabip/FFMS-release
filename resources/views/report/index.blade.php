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
                <div class="row">
                    <div class="col-4">
                        <select class="form-control" id="SortTypeFilter" name="SortTypeFilter" disabled>
                            <option value="year" disabled>Sort By (Daily)</option>
                            <option value="month" selected>Sort By (Monthly)</option>
                            <option value="year">Sort By (Yearly)</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-control" id="SortMonthFilter" name="SortMonthFilter" disabled>
                            <option value="1" selected>Bulan (Januari)</option>
                            <option value="2">Bulan (Februari)</option>
                            <option value="3">Bulan (Maret)</option>
                            <option value="4">Bulan (April)</option>
                            <option value="5">Bulan (Mei)</option>
                            <option value="6">Bulan (Juni)</option>
                            <option value="7">Bulan (Juli)</option>
                            <option value="8">Bulan (Agustus)</option>
                            <option value="9">Bulan (September)</option>
                            <option value="10">Bulan(Oktober)</option>
                            <option value="11">Bulan (November</option>
                            <option value="12">Bulan (Desember)</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-control" id="SortYearFilter" name="SortYearFilter">
                            <option class="font-weight-bold" value="">Tahun (All)</option>
                            <option value="2021" selected>Tahun (2021)</option>
                            //<option value="2022">Tahun (2022)</option>
                        </select>
                    </div>
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
                            {!! $revenueChart->render() !!}
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
    $(function (){
        $("#SortTypeFilter").on("change", function () {
            // if($("#SortTypeFilter").val() == "year"){
            //     $("#SortMonthFilter").hide();
            // }else{
            //     $("#SortMonthFilter").show();
            // }
        });
        $("#SortMonthFilter").on("change", function() {
            //getChartData($("#SortTypeFilter").val(), $("#SortMonthFilter").val(), $("#SortYearFilter").val());
        });
        $("#SortYearFilter").on("change", function () {
            getChartData($("#SortTypeFilter").val(), null, $("#SortYearFilter").val());
        });
    });
    function getChartData(typeFilter, monthFilter, yearFilter){
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: "{{ route('report.updateChartData') }}",
            data: {
                type: typeFilter,
                month: monthFilter,
                year: yearFilter
            },
            success: function(data){
                var obj = JSON.parse(data);
                console.log(obj.revenueData);
                $("#revenueFrame").html('<canvas id="profitChart" width="400" height="100"></canvas>');
                var ctxProfit = document.getElementById('profitChart').getContext('2d');
                var chartProfit = new Chart(ctxProfit, {
                    type: 'line',
                    data: setData("revenue", [obj.revenueData, obj.revenueDataMembership, obj.revenueDataSesi]),
                    options: setOptions('Profit Data', 'top', 0),
                });
                //i < jumlah chart
                //console.log(myChart);
                //myChart.update();
            }
        });
    }
    function setData(type, data){
        switch(type){
            case "revenue":
                var dataGenerate = {
                    labels: data[0].labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Total Revenue',
                            data: data[0].dataset,
                            borderColor: 'rgb(7,138,238)',
                            backgroundColor: 'rgba(6,95,173,0.1)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Total Revenue',
                            data: data[1].dataset,
                            borderColor: 'rgb(219,98,6)',
                            backgroundColor: 'rgba(173,64,6,0.1)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'line',
                            label: 'Total Revenue',
                            data: data[2].dataset,
                            borderColor: 'rgb(219,6,6)',
                            backgroundColor: 'rgba(173,6,20,0.1)',
                            borderWidth: 2,
                            hidden: true
                        }
                    ]
                }
                return dataGenerate;
                break;
        }
    }
    function setOptions(title, position, tension){
        var options = {
            responsive: true,
            plugins: {
                legend: {
                    position: position,
                },
                title: {
                    display: true,
                    text: title
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            elements: {
                line: {
                    tension: tension
                }
            }
        }
        return options;
    }
    @endsection
</script>
