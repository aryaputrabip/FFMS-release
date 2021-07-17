<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaCheckinChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshCheckinChart() {
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaCheckin') }}',
            data: {
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val()
            },
            success: function (data) {
                if (typeof (ChartCheckin) != "undefined") {
                    ChartCheckin.destroy();
                }

                var newChartContext = setChartContextData('performaCheckinChart');
                var newChartData = setChartData("checkin", data.chart_label, data.chart_dataset, null, null);

                ChartCheckin = new Chart(newChartContext, newChartData);
                ChartCheckin.update();
            },
            error: function () {
                console.log("error");
            }
        });
    }

    function initCheckinChart(labels, datasetTotal){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Member Checkin',
                        data: datasetTotal,
                        borderColor: 'rgb(6,101,173)',
                        backgroundColor: 'rgba(87,128,252,0.0)',
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
