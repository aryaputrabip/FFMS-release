<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaRevenueChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshRevenueChart(){
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaRevenue') }}',
            data:{
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val()
            },
            success: function (data) {
                if(typeof (ChartRevenue) != "undefined"){
                    ChartRevenue.destroy();
                }

                var newChartContext = setChartContextData('performaRevenueChart');
                var newChartData = setChartData("revenue", data.chart_label, data.chart_dataset, data.chart_dataset_1, data.chart_dataset_2);

                ChartRevenue = new Chart(newChartContext, newChartData);
                ChartRevenue.update();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function initRevenueChart(labels, datasetTotal, dataset_2, dataset_3){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Revenue',
                        data: datasetTotal,
                        borderColor: 'rgb(6,173,41)',
                        backgroundColor: 'rgba(252,87,94,0.0)',
                        borderWidth: 2
                    },
                    {
                        type: 'line',
                        label: 'Revenue (Membership)',
                        data: dataset_2,
                        borderColor: 'rgb(6,115,173)',
                        backgroundColor: 'rgba(23,152,222,0)',
                        borderWidth: 2,
                        hidden: true,
                    },
                    {
                        type: 'line',
                        label: 'Revenue (Sesi)',
                        data: dataset_3,
                        borderColor: 'rgb(224,7,7)',
                        backgroundColor: 'rgba(224,13,97,0)',
                        borderWidth: 2,
                        hidden: true,
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
