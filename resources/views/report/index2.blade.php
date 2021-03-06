@extends('layouts.app_admin')
@section('style')

@endsection
@section('content')

<div class="row m-3" style="background-color: white;">

    <div class="col-md-3">
        <div class="row">
            <form id="dataReg">
                @csrf
                <div class="col-12 my-2">Tanggal Mulai :</div>
                <div class="col-12 my-2"><input type="date" class="form-control" name="tglMulai" id="tglMulai" style="width: 100%;"></div>

                <div class="col-12 my-2">Tanggal Akhir :</div>
                <div class="col-12 my-2"><input type="date" class="form-control " name="tglAkhir" id="tglAkhir" style="width: 100%;"></div>
            </form>
            <div class="col-12 my-2">Bulan</div>
            <div class="col-12 my-2"> <button class="btn btn-success" onclick="dataReg()" style="width: 100%;"> search </button> </div>
            <div class="col-12 my-2">
                <select id="bulan" name="bulan" onchange="dataRegDay(this.value)" class="custom-select">
                    @foreach ($bulan as $b)
                    <?php $bln = explode(' ', $b) ?>
                    <option value="{{$bln[1]}}"> {{$bln[0]}} </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="container-fluid">
            <div class="tab">
                <button class="tablinks btn btn-info" onclick="openCity(event, 'hariReg', 'reg')">Hari</button>

                <div class="btn-group">
                    <button type="button" class="tablinks btn btn-info" onclick="openCity(event, 'bulanReg', 'reg')">Bulan</button>
                    <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>

                    <div class="dropdown-menu" >
                        @foreach ($bulan as $b)
                        <?php $bln = explode(' ', $b) ?>
                        <a class="dropdown-item" onclick="dataRegDay('{{$bln[1]}}')"> {{$bln[0]}}</a>
                        @endforeach
                    </div>
                </div>

                <button class="tablinks btn btn-info" onclick="openCity(event, 'tahunReg', 'reg')">Tahun</button>
                <button class="tablinks btn btn-info" onclick="openCity(event, 'filterReg', 'reg')">Filter</button>
            </div>

            <div id="bulanReg" class="reg" style="width: 100%; min-height: 300px">
                <canvas id="reg"> </canvas>
            </div>

            <div id="hariReg" class="reg" style="width: 100%; min-height: 300px; display:none">
                <canvas id="regday"> </canvas>
            </div>

            <div id="tahunReg" class="reg" style="width: 100%; min-height: 300px ;display:none">

            </div>

            <div id="filterRegs" class="reg" style="width: 100%; min-height: 300px; display:none;">

            </div>
        </div>
    </div>
</div>
<script>
    //Regis doang
    var sel = document.getElementById('reg').getContext('2d');
    var selday = document.getElementById('regday').getContext('2d');
    var form = $('#dataReg');
    var bulan = new Date().getMonth() + 1;
    var chartMonth;
    var chartDay;
    $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
        var res = JSON.parse(response)
    }).done(function(res) {
        var res = JSON.parse(res)
        console.log(res);
        drawReg(res, bulan);
    });

    function drawReg(res, bln) {
        var form = $('#dataReg');
        //var sel = document.getElementById('reg').getContext('2d');
        //console.log(res);
        bulan = bln
        console.log(res.dataPerDay[bulan])
        chartMonth = new Chart(sel, {
            type: 'bar',
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
        var labels = [];
        for (const i = 1; i <= res.dataPerDay[bulan].length; i++) {
            labels = i;
            i++
        }

        chartDay = new Chart(selday, {
            type: 'bar',
            options: {
                responsive: true,
                maintainAspectRatio: false
            },
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peserta Terdaftar',
                    data: res.dataPerDay[bulan]
                }]
            }
        })
    }

    function dataReg() {
        chartMonth.destroy();
        chartDay.destroy();
        $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
            var res = JSON.parse(response)
        }).done(function(res) {
            var res = JSON.parse(res)
            console.log(res);
            drawReg(res);
        });
    }

    function dataRegDay(bln) {
        chartDay.destroy();
        chartMonth.destroy();
        $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
            var res = JSON.parse(response)
        }).done(function(res) {
            var res = JSON.parse(res)
            console.log(res);
            drawReg(res, bln);
        });
    }
    //sampe sini regis nya

    //buat tabs
    function openCity(evt, tabs, tabconts) {
        // Declare all variables
        var i, tabcontent, tablinks;
        //console.log($('#bulan'))
        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName(tabconts);
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabs).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>

@endsection
