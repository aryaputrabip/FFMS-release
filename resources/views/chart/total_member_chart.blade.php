<hr>
<div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
    <canvas id="performaMemberChart" width="100" height="30" style="max-width: 100%;"></canvas>
</div>

<script>
    //GENERATE CHART

    function refreshMemberChart(){
        $.ajax({
            type: 'GET',
            url: '{{ route('report.performaMember') }}',
            data:{
                FILTER_TYPE: $("#tableFilterChartType").val(),
                FILTER_MONTH: $("#tableFilterChartMonth").val(),
                FILTER_YEAR: $("#tableFilterChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterChartYearDuration").val()
            },
            success: function (data) {
                if(typeof (ChartMember) != "undefined"){
                    ChartMember.destroy();
                }

                var newChartContext = setChartContextData('performaMemberChart');
                var newChartData = setChartData("member", data.chart_label, data.chart_dataset, data.chart_dataset_1, data.chart_dataset_2);

                ChartMember = new Chart(newChartContext, newChartData);
                ChartMember.update();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function initMemberChart(labels, datasetTotal, dataset_2, dataset_3){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Total Member Baru',
                        data: datasetTotal,
                        borderColor: 'rgb(196,129,0)',
                        backgroundColor: 'rgba(252,236,87,0)',
                        borderWidth: 2
                    },
                    {
                        type: 'bar',
                        label: 'Member (Laki-laki)',
                        data: dataset_2,
                        borderColor: 'rgb(6,115,173)',
                        backgroundColor: 'rgba(23,152,222,0)',
                        borderWidth: 2,
                        hidden: true,
                    },
                    {
                        type: 'bar',
                        label: 'Member (Perempuan)',
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
