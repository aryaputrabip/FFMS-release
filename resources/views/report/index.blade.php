<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
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

    <section class="main-content page-content mt-3 mb-3">
        <div class="card mb-3">
            <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>Statistik</b>
                <div class="card-tools mr-0">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card">
            <div class="card-header pl-2 pr-2 pt-1 pb-1">
                <b>{{ $title }}</b>
            </div>
            <div class="card-body p-0">
                <form action="{{route('report.dataReg')}}">
                    
                </form>
                <canvas id="reg"> </canvas>
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
    
    var chart = new Chart(sel,{
        type: 'bar',
        data : {
            labels : ['yes','no'],
            datasets:[{
                label:'yess',
                data: ['20','10'],
            }]
        }
    })
</script>

</body>
</html>