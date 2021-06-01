<script>
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
            case "activity":
                var dataGenerate = {
                    labels: data[0].labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Check-In',
                            data: data[0].dataset,
                            borderColor: 'rgb(37,147,220)',
                            backgroundColor: 'rgba(0,0,0,0)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Pembelian',
                            data: data[1].dataset,
                            borderColor: 'rgb(9,187,89)',
                            backgroundColor: 'rgba(0,0,0,0)',
                            borderWidth: 2
                        }
                    ]
                }
                return dataGenerate;
                break;
            case "member":
                var dataGenerate = {
                    labels: data[0].labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Total Member',
                            data: data[3].dataset,
                            borderColor: 'rgb(224,7,68)',
                            backgroundColor: 'rgba(224,13,97,0)',
                            borderWidth: 2
                        },
                        {
                            type: 'bar',
                            label: 'Total Member (Laki-laki)',
                            data: data[1].dataset,
                            borderColor: 'rgb(219,98,6)',
                            backgroundColor: 'rgba(23,152,222,0)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'bar',
                            label: 'Total Member (Perempuan)',
                            data: data[2].dataset,
                            borderColor: 'rgb(6,173,41)',
                            backgroundColor: 'rgba(252,87,94,0.0)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'bar',
                            label: 'Member Baru',
                            data: data[0].dataset,
                            borderColor: 'rgb(37,147,220)',
                            backgroundColor: 'rgba(37,147,220,0.2)',
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
