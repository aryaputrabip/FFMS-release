<script>
    //GENERATE CHART
    var chart_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var chart_data_pengelauran_total = [3,7,7,8,9,12,20,22,32,38,45,56];
    var chart_data_pengeluaran_membership = [4, 6, 2, 3, 10, 6, 3, 6, 8, 13, 10, 5];
    var chart_data_pengelauran_pt = [4, 6, 2, 3, 10, 6, 3, 6, 8, 13, 10, 5];
    var ctx = document.getElementById('memberHistoryChart').getContext('2d');

    var chartData  = {
        type: 'bar',
        data: {
            labels: chart_labels,
            datasets: [
                {
                    type: 'line',
                    label: 'Pengeluaran (Bulanan)',
                    data: chart_data_pengelauran_total,
                    borderColor: 'rgb(6,173,41)',
                    backgroundColor: 'rgba(252,87,94,0.0)',
                    borderWidth: 2
                },
                {
                    type: 'bar',
                    label: 'Paket Member',
                    data: chart_data_pengeluaran_membership,
                    borderColor: 'rgb(6,115,173)',
                    backgroundColor: 'rgba(23,152,222,0)',
                    borderWidth: 2,
                },
                {
                    type: 'bar',
                    label: 'Paket PT',
                    data: chart_data_pengelauran_pt,
                    borderColor: 'rgb(224,7,7)',
                    backgroundColor: 'rgba(224,13,97,0)',
                    borderWidth: 2,
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

    $("#tableFilterHistoryChartMonth").on("change", function(){
        refreshChart();
    });

    $("#tableFilterHistoryChartYear").on("change", function(){
        refreshChart();
    });

    $("#tableFilterHistoryChartYearDuration").on("change", function(){
        refreshChart();
    });

    $("#tableFilterHistoryChartType").on("change", function(){
        switch($(this).val()){
            case "daily":
                $("#tableFilterHistoryChartMonth").show();
                $("#tableFilterHistoryChartYear").show();
                $("#tableFilterHistoryChartYearDuration").hide();
                refreshChart();
                break;
            case "monthly":
                $("#tableFilterHistoryChartMonth").hide();
                $("#tableFilterHistoryChartYear").show();
                $("#tableFilterHistoryChartYearDuration").hide();
                refreshChart();
                break;
            case "yearly":
                $("#tableFilterHistoryChartMonth").hide();
                $("#tableFilterHistoryChartYear").show();
                $("#tableFilterHistoryChartYearDuration").show();
                refreshChart();
                break;
        }
    });

    function refreshChart(){
        $.ajax({
            type: 'GET',
            url: '{{ route('report.getSpecifyMemberSpending') }}',
            data:{
                member_id: {{ $data->member_id }},
                FILTER_TYPE: $("#tableFilterHistoryChartType").val(),
                FILTER_MONTH: $("#tableFilterHistoryChartMonth").val(),
                FILTER_YEAR: $("#tableFilterHistoryChartYear").val(),
                FILTER_YEAR_DURATION: $("#tableFilterHistoryChartYearDuration").val()
            },
            success: function (data) {
                if(typeof (historyChartMonthly) != "undefined"){
                    historyChartMonthly.destroy();
                }

                var historyContext = setHistoryContextData();
                var historyChartData = setHistoryChartData(data.chart_label, data.chart_dataset, data.chart_dataset_membership, data.chart_dataset_pt);

                historyChartMonthly = new Chart(historyContext, historyChartData);
                historyChartMonthly.update();
            },
            error: function() {
                console.log("error");
            }
        });
    }

    function setHistoryContextData(){
        return document.getElementById('memberHistoryChart').getContext('2d');
    }

    function setHistoryChartData(labels, datasetTotal, datasetMembership, datasetPT){
        var data  = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        type: 'line',
                        label: 'Pengeluaran (Bulanan)',
                        data: datasetTotal,
                        borderColor: 'rgb(6,173,41)',
                        backgroundColor: 'rgba(252,87,94,0.0)',
                        borderWidth: 2
                    },
                    {
                        type: 'bar',
                        label: 'Paket Member',
                        data: datasetMembership,
                        borderColor: 'rgb(6,115,173)',
                        backgroundColor: 'rgba(23,152,222,0)',
                        borderWidth: 2,
                    },
                    {
                        type: 'bar',
                        label: 'Paket PT',
                        data: datasetPT,
                        borderColor: 'rgb(224,7,7)',
                        backgroundColor: 'rgba(224,13,97,0)',
                        borderWidth: 2,
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

    //REFRESH CHART ON PAGE START
    refreshChart();
</script>
