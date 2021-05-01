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

        <section class="main-content container-fluid page-content mt-3 mb-3" style="background-color: aqua;">

            <!-- <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>{{ $title }}</b>
            </div> -->
            <div class="row m-3" style="background-color: purple;">
                <div class="col-xs-3">
                    <div class="row">
                        <form id="dataReg" class="form-control">
                            @csrf
                            <div class="col-12 my-2">Tanggal Mulai :</div>
                            <div class="col-12 my-2"><input type="date" name="tglMulai" id="tglMulai"></div>

                            <div class="col-12 my-2">Tanggal Akhir :</div>
                            <div class="col-12 my-2"><input type="date" class="input-control" name="tglAkhir" id="tglAkhir"></div>
                        </form>
                        <div class="col-12 my-2"> <button class="btn btn-success" onclick="dataReg()"> search </button> </div>
                    </div>
                </div>
                <div class="col-xs-9" style=" background-color: red;  width: 100%">
                yo
                    <!-- <div style="width:100%; background-color: yellow">
                        <canvas id="reg"> </canvas>
                    </div> -->
                </div>
            </div>
        </section>
    </div>

    <!-- INIT SCRIPTS -->
    @include('theme.default.source.script_source')
    @include('theme.default.import.modular.datatables.script')

    <!-- page script -->
    <script>
        var sel = document.getElementById('reg').getContext('2d');

        function dataReg() {
            var form = $('#dataReg');
            $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
                var res = JSON.parse(response)
            }).done(function(res) {
                var res = JSON.parse(res)
                console.log(res);
                chart.destroy();
                var chart = new Chart(sel, {
                    type: 'bar',
                    title: 'Data tahun ini',
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    },
                    data: {
                        labels: res.month,
                        datasets: [{
                            label: 'Peserta Terdaftar',
                            data: res.dataMonth
                        }]
                    }
                })
                
            })
        }
    </script>

</body>

</html>