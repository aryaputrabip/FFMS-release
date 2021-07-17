<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaPTChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshPTChart(){
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaPT') }}',
            data:{
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val(),
                FILTER_PT: $("#tableFilterPT").val()
            },
            success: function (data) {
                if(typeof (ChartPT) != "undefined"){
                    ChartPT.destroy();
                }

                var newChartContext = setChartContextData('performaPTChart');
                var newChartData = setChartData("pt", data.chart_label, data.chart_dataset, null, null);

                ChartPT = new Chart(newChartContext, newChartData);
                ChartPT.update();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function initPTChart(labels, datasetTotal){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Pemakaian Sesi',
                        data: datasetTotal,
                        borderColor: 'rgb(6,126,173)',
                        backgroundColor: 'rgba(87,252,252,0)',
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
