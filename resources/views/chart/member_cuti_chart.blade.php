<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaCutiChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshCutiChart() {
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaCuti') }}',
            data: {
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val()
            },
            success: function (data) {
                if (typeof (ChartCuti) != "undefined") {
                    ChartCuti.destroy();
                }

                var newChartContext = setChartContextData('performaCutiChart');
                var newChartData = setChartData("cuti", data.chart_label, data.chart_dataset, null, null);

                ChartCuti = new Chart(newChartContext, newChartData);
                ChartCuti.update();
            },
            error: function () {
                console.log("error");
            }
        });
    }

    function initCutiChart(labels, datasetTotal){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Member Cuti',
                        data: datasetTotal,
                        borderColor: 'rgb(6,173,41)',
                        backgroundColor: 'rgba(252,87,94,0.0)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        return data;
    }
</script>
