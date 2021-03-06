@extends('layouts.app_admin')
@section('style')
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
                <div class="col-12 my-2">
                    <select id="bulan" name="bulan" onchange="dataRegDay(this.value)" class="custom-select">
                        @foreach ($bulan as $b)
                            <?php $bln = explode(' ', $b) ?>
                            <option value="{{$bln[1]}}"> {{$bln[0]}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 my-2"> <button class="btn btn-success" onclick="dataReg()" style="width: 100%;"> search </button> </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="container-fluid">
                <div class="tab">
                    <button class="tablinks btn btn-info" onclick="openCity(event, 'hari', 'reg')">Hari</button>

                    <div class="btn-group">
                        <button type="button" class="tablinks btn btn-info" onclick="openCity(event, 'bulan', 'reg')">Bulan</button>
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

                    <div class="btn-group">
                        <button class="tablinks btn btn-info" onclick="openCity(event, 'tahun', 'reg')">Tahun</button>
                        <button type="button" class="btn btn-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu" >
                            @foreach ($tahun as $t)
                                <a class="dropdown-item" onclick="dataRegYear('{{$t->year}}')"> {{$t->year}}</a>
                            @endforeach
                        </div>
                    </div>

                    <!-- <button class="tablinks btn btn-info" onclick="openCity(event, 'filterReg', 'reg')">Filter</button> -->
                </div>

                <!-- Registrasi -->
                <div id="hariReg" class="hari reg" style="width: 100%; min-height: 300px; display:none">
                    <canvas id="regday"> </canvas>
                </div>

                <div id="bulanReg" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="reg"> </canvas>
                </div>

                <div id="tahunReg" class="tahun reg" style="width: 100%; min-height: 300px">
                    <canvas id="regyear"></canvas>
                </div>

                <!-- Profit -->
                <div id="hariProf" class="hari reg" style="width: 100%; min-height: 300px; display:none">
                    <canvas id="profday"> </canvas>
                </div>

                <div id="bulanProf" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="prof"> </canvas>
                </div>

                <div id="tahunProf" class="tahun reg" style="width: 100%; min-height: 300px">
                    <canvas id="profyear"> </canvas>
                </div>

                <!-- Aktivitas Member -->
                <div id="bulanAktiv" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="aktiv"> </canvas>
                </div>

                <!-- Performa Member -->
                <div id="bulanPerforma" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="performa"> </canvas>
                </div>

                <!-- Top 10 -->
                <div id="" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="topMarketing"> </canvas>
                </div>

                <div id="" class="bulan reg" style="width: 100%; min-height: 300px ;display:none">
                    <canvas id="topPT"> </canvas>
                </div>

                <div id="filterRegs" class="reg" style="width: 100%; min-height: 300px; display:none;">

                </div>
            </div>
        </div>
    </div>
    <script>
        // Chart Profit
        var prof = document.getElementById('prof').getContext('2d');
        var profday = document.getElementById('profday').getContext('2d');
        var profyear = document.getElementById('profyear').getContext('2d');
        //Regis doang
        var sel = document.getElementById('reg').getContext('2d');
        var selday = document.getElementById('regday').getContext('2d');
        var selyear = document.getElementById('regyear').getContext('2d');
        // Chart Aktivitas
        var aktiv = document.getElementById('aktiv').getContext('2d');
        // Chart Performa Meber
        var performa = document.getElementById('performa').getContext('2d');
        // Chart Top 10
        var topMarketing = document.getElementById('topMarketing').getContext('2d');
        var topPT = document.getElementById('topPT').getContext('2d');
        var form = $('#dataReg');
        var bulan = new Date().getMonth() + 1;
        var tahun = "{{ date('Y') }}";

        var chartMonth;
        var chartProfitMonth;
        var chartProfitDay;
        $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
            var res = JSON.parse(response)
        }).done(function(res) {
            var res = JSON.parse(res)
            console.log(res);
            drawReg(res, bulan);
            GrafikTahunan(res);
            dataRegYear(tahun)
        });
        function GrafikTahunan(res) {

            chartYear = new Chart(selyear, {
                type: 'bar',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Member',
                            padding: {
                                top: 30,
                                bottom: 10
                            }

                        }
                    }
                },
                data: {
                    labels: res.year,
                    datasets: [{
                        label: 'Peserta Terdaftar',
                        data: res.dataPerYear
                    }]
                }
            })
            chartProfitYear = new Chart(profyear, {
                type: 'bar',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Profit',
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.year,
                    datasets: [
                        {
                            label: 'Total Revenue',
                            data: res.profitPerYear,
                            borderColor: 'rgb(7,138,238)',
                            backgroundColor: 'rgba(6,95,173,0.1)',
                            borderWidth: 2
                        }
                    ]
                }
            })
        }
        function drawReg(res, bln) {
            var form = $('#dataReg');
            //var sel = document.getElementById('reg').getContext('2d');
            console.log(res.profitPerMonth);
            bulan = bln
            // console.log(res.dataPerDay[bulan])
            chartMonth = new Chart(sel, {
                type: 'bar',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Member Tahun ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.month,
                    datasets: [{
                        label: 'Peserta Terdaftar Tahun ' + tahun,
                        data: res.dataMonth
                    }]
                }
            })
            // Chart Profit Per Bulan
            chartProfitMonth = new Chart(prof, {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Profit Tahun ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.month,
                    datasets: [
                        {
                            label: 'Total Revenue',
                            data: res.profitPerMonth,
                            borderColor: 'rgb(7,138,238)',
                            backgroundColor: 'rgba(6,95,173,0.1)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Revenue (Membership)',
                            data: res.profitMemberPerMonth,
                            borderColor: 'rgb(219,98,6)',
                            backgroundColor: 'rgba(173,64,6,0.1)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'line',
                            label: 'Revenue (Sesi)',
                            data: res.profitSesiPerMonth,
                            borderColor: 'rgb(219,6,6)',
                            backgroundColor: 'rgba(173,6,20,0.1)',
                            borderWidth: 2,
                            hidden: true
                        }
                    ]
                }
            })
            // Chart Aktivitas Per Bulan
            chartAktivMonth = new Chart(aktiv, {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Aktivitas Member Tahun ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.month,
                    datasets: [
                        {
                            label: 'Pembelian',
                            data: res.pembelianPerMonth,
                            borderColor: 'rgb(9,187,89)',
                            backgroundColor: 'rgba(6,95,173,0.1)',
                            borderWidth: 2
                        }
                    ]
                }
            })
            // Chart Performa Member Perbulan
            chartPeformaMonth = new Chart(performa, {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performa Member Tahun ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.month,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Total Member',
                            data: res.memberPerMonth,
                            borderColor: 'rgb(6,173,41)',
                            backgroundColor: 'rgba(252,87,94,0.0)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Total Member (Laki-laki)',
                            data: res.memberLakiPerMonth,
                            borderColor: 'rgb(6,115,173)',
                            backgroundColor: 'rgba(23,152,222,0)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'line',
                            label: 'Total Member (Perempuan)',
                            data: res.memberPerempuanPerMonth,
                            borderColor: 'rgb(224,7,68)',
                            backgroundColor: 'rgba(224,13,97,0)',
                            borderWidth: 2,
                            hidden: true
                        },
                        {
                            type: 'bar',
                            label: 'Member Baru',
                            data: res.dataMonth,
                            borderColor: 'rgb(37,147,220)',
                            backgroundColor: 'rgba(37,147,220,0.2)',
                            borderWidth: 2
                        }
                    ]
                }
            })
            // Chart Top 10 Marketing
            chartTopMarketing = new Chart(topMarketing, {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 10 Marketing ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.topMarketing,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Total Member',
                            data: res.topMarketingCount,
                            borderColor: 'rgb(6,173,41)',
                            backgroundColor: 'rgba(252,87,94,0.0)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Profit Diperoleh',
                            data: res.topMarketingProfit,
                            borderColor: 'rgb(208,12,12)',
                            backgroundColor: 'rgba(37,147,220,0.0)',
                            borderWidth: 2,
                            hidden: true
                        }
                    ]
                }
            })
            // Chart Top 10 PT
            chartTopPT = new Chart(topPT, {
                type: 'line',
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Top 10 PT ' + tahun,
                            padding: {
                                top: 30,
                                bottom: 10
                            }
                        }
                    }
                },
                data: {
                    labels: res.topPT,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'Total Member',
                            data: res.topPTCount,
                            borderColor: 'rgb(6,173,41)',
                            backgroundColor: 'rgba(252,87,94,0.0)',
                            borderWidth: 2
                        },
                        {
                            type: 'line',
                            label: 'Profit Diperoleh',
                            data: res.topPTProfit,
                            borderColor: 'rgb(208,12,12)',
                            backgroundColor: 'rgba(37,147,220,0.0)',
                            borderWidth: 2,
                            hidden: true
                        }
                    ]
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
                        label: 'Peserta Terdaftar ' + res.month[bulan-1] + ' ' + tahun,
                        data: res.dataPerDay[bulan]
                    }]
                }
            })
        }
        function dataReg() {
            chartMonth.destroy();
            chartDay.destroy();
            chartProfitMonth.destroy();

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
            chartProfitMonth.destroy();
            formData = form.serialize()+ "&tahun=" + tahun;
            $.post("{{route('report.dataReg')}}", formData, function(response) {
                var res = JSON.parse(response)
            }).done(function(res) {
                var res = JSON.parse(res)
                console.log(res);
                drawReg(res, bln);
            });
        }

        function dataRegYear(year) {
            tahun = year;
            chartDay.destroy();
            chartMonth.destroy();
            chartProfitMonth.destroy();
            formData = form.serialize()+ "&tahun=" + year;
            console.log(formData);
            $.post("{{route('report.dataReg')}}", formData, function(response) {
                var res = JSON.parse(response)
            }).done(function(res) {
                var res = JSON.parse(res)
                console.log(res);
                drawReg(res);
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
            tabclass = document.getElementsByClassName(tabs)
            for (i = 0; i < tabclass.length; i++) {
                tabclass[i].style["display"] = 'block';
            }
            evt.currentTarget.className += " active";
        }
    </script>

@endsection
