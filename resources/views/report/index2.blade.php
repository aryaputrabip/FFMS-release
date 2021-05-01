@extends('layouts.app_admin')

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
            <div class="col-12 my-2"> <button class="btn btn-success" onclick="dataReg()" style="width: 100%;"> search </button> </div>
        </div>
    </div>
    <div class="col-md-9">
        <div style="width: 100%; min-height: 300px">
            <canvas id="reg"> </canvas>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script>
var sel = document.getElementById('reg').getContext('2d');
var form = $('#dataReg');
    $(document).ready(function() {
                $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
                    var res = JSON.parse(response)
                }).done(function(res) {
                    var res = JSON.parse(res)
                    console.log(res);

                    var chartMonth = new Chart(sel, {
                        type: 'bar',
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        },
                        data: {
                            labels: res.month,
                            datasets: [{
                                label: 'Data Peserta Terdaftar Per Bulan',
                                data: res.dataMonth
                            }]
                        }
                    })
                });
            });
                function dataReg() {
                    var form = $('#dataReg');
                    $.post("{{route('report.dataReg')}}", form.serialize(), function(response) {
                        var res = JSON.parse(response)
                    }).done(function(res) {
                        var res = JSON.parse(res)
                        console.log(res);

                        var chart = new Chart(sel, {
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
                        chart.clear();

                    })
                }
</script>

@endsection