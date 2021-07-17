<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaMarketingChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshMarketingChart(){
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaMarketing') }}',
            data:{
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val(),
                FILTER_MARKETING: $("#tableFilterMarketing").val()
            },
            success: function (data) {
                if(typeof (ChartMarketing) != "undefined"){
                    ChartMarketing.destroy();
                }

                var newChartContext = setChartContextData('performaMarketingChart');
                var newChartData = setChartData("marketing", data.chart_label, data.chart_dataset, null, null);

                ChartMarketing = new Chart(newChartContext, newChartData);
                ChartMarketing.update();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function initMarketingChart(labels, datasetTotal){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Revenue (Marketing)',
                        data: datasetTotal,
                        borderColor: 'rgb(6,173,101)',
                        backgroundColor: 'rgba(92,252,87,0)',
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
