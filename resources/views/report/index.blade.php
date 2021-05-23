<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <!-- INIT STYLE -->
    @include('theme.default.source.css_source')
    @include('theme.default.import.modular.datatables.css')

</head>

<body class="sidebar-mini layout-navbar-fixed sidebar-collapse overflow-hidden">
    <!-- layout-fixed  -->
    <div class="wrapper">

        @include('theme.default.header')

        @include('theme.default.sidenav')

        <div class="main-content bg-default-light">
            @include('theme.default.title')
        </div>

        <section class="main-content container-fluid page-content">

            <!-- <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>{{ $title }}</b>
            </div> -->
{{--            <div class="row m-3" style="background-color: purple;">--}}
{{--                <div class="col-xs-3">--}}
{{--                    <div class="row">--}}
{{--                        <form id="dataReg" class="form-control">--}}
{{--                            @csrf--}}
{{--                            <div class="col-12 my-2">Tanggal Mulai :</div>--}}
{{--                            <div class="col-12 my-2"><input type="date" name="tglMulai" id="tglMulai"></div>--}}

{{--                            <div class="col-12 my-2">Tanggal Akhir :</div>--}}
{{--                            <div class="col-12 my-2"><input type="date" class="input-control" name="tglAkhir" id="tglAkhir"></div>--}}
{{--                        </form>--}}
{{--                        <div class="col-12 my-2"> <button class="btn btn-success" onclick="dataReg()"> search </button> </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xs-9" style=" background-color: red;  width: 100%">--}}
{{--                yo--}}
{{--                    <!-- <div style="width:100%; background-color: yellow">--}}
{{--                        <canvas id="reg"> </canvas>--}}
{{--                    </div> -->--}}
{{--                </div>--}}
{{--            </div>--}}

                <div class="row pt-3">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="SortTypeFilter">Sort By</label>
                            <select class="form-control" id="SortTypeFilter" name="SortTypeFilter">
                                <option value="month" selected>Monthly</option>
                                <option value="year">Yearly</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
{{--                        <div class="form-group">--}}
{{--                            <label for="profitSortMonthFilter">Month</label>--}}
{{--                            <select class="form-control" id="SortMonthFilter" name="SortMonthFilter">--}}
{{--                                <option value="1" selected>Januari</option>--}}
{{--                                <option value="2">Februari</option>--}}
{{--                                <option value="3">Maret</option>--}}
{{--                                <option value="4">April</option>--}}
{{--                                <option value="5">Mei</option>--}}
{{--                                <option value="6">Juni</option>--}}
{{--                                <option value="7">Juli</option>--}}
{{--                                <option value="8">Agustus</option>--}}
{{--                                <option value="9">September</option>--}}
{{--                                <option value="10">Oktober</option>--}}
{{--                                <option value="11">November</option>--}}
{{--                                <option value="12">Desember</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="SortYearFilter">Year</label>
                            <select class="form-control" id="SortYearFilter" name="SortYearFilter">
                                <option value="2021" selected>2021</option>
                                <option value="2022">2022</option>
                            </select>
                        </div>
                    </div>
                </div>

                <h2 class="text-center pt-3">Performa Profit</h2>
                <div style="max-width: 95%; overflow-x: auto;" id="revenueFrame">
                    {!! $revenueChart->render() !!}
                </div>
        </section>
    </div>

    <!-- INIT SCRIPTS -->
    @include('theme.default.source.script_source')
    @include('theme.default.import.modular.datatables.script')

    <!-- page script -->
    <script>

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
    </script>

</body>

</html>
