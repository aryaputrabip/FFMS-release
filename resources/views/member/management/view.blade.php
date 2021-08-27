@extends($app_layout)

<style>
    @section('css')
    .col-photo{
        -ms-flex: 0 0 270px;
        flex: 0 0 270px;
    }
    @endsection
</style>

@section('bg')
    <div style="background-color: #FFFFFF; min-width: 100vw; min-height: 100vh; position:absolute;"></div>
@endsection

@section('content')
    <div class="container-fluid ml-2">
        <div class="row">
            <div class="col-photo">
                <img @if(isset($data->photo)) src="{{ $data->photo }}" @endisset width="250" height="250" data-backdrop="static" style="background-color: gray;" id="photo">
                @if($data->status == 1)
                    <button type="button" class="btn btn-primary mb-3 mt-3" style="width: 250px;" id="checkinBtn" onclick="checkinMember('{{ $data->member_id }}');">
                        <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                    </button>
                @elseif($data->status == 2)
                    <button type="button" class="btn btn-success mb-3 mt-3" style="width: 250px;" id="checkinBtn" onclick="activateMember('{{ $data->member_id }}', {{ $membership->duration }});">
                        <i class="fas fa-calendar-check fa-sm mr-1"></i> Aktivasi Member
                    </button>
                @elseif($data->status == 3)
                    <button type="button" class="btn btn-primary mb-3 mt-3 disabled" style="width: 250px;" id="checkinBtn" onclick="checkinFailed('cuti')">
                        <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                    </button>
                @elseif($data->status == 4)
                    <button type="button" class="btn btn-primary mb-3 mt-3 disabled" style="width: 250px;" id="checkinBtn" onclick="checkinFailed('expired')">
                        <i class="fas fa-calendar-check fa-sm mr-1"></i> Check-In
                    </button>
                @endif

                @if($role == 1)
                    <a href="{{ route('suadmin.member.edit', $data->member_id) }}" type="button" class="btn btn-danger mb-2 mt-3" style="width: 250px;" id="editBtn" onclick="">
                        <i class="fas fa-edit fa-sm mr-1"></i> Edit Data
                    </a>
                @else
                    <a href="{{ route('cs.member.edit', $data->member_id) }}" type="button" class="btn btn-danger mb-2 mt-3" style="width: 250px;" id="editBtn" onclick="">
                        <i class="fas fa-edit fa-sm mr-1"></i> Edit Data
                    </a>
                @endif


                <a @if($role == 1) href="{{ route('suadmin.member.index') }}" @elseif($role == 2) href="#"
                   @elseif($role == 3) href="{{ route('cs.member.index') }}" @endif type="button"
                   class="btn btn-outline-dark mb-2" style="width: 250px;">
                    <i class="fas fa-times fa-sm mr-1"></i> Batal
                </a>
                <hr style="width: 250px;" class="ml-0">
                @if(isset($membership_startdate))
                    <div class="card mt-2" style="width: 250px;" id="cardDataMember">
                        <div class="card-body p-2">
                            <h6 class="font-weight-bold">Membership</h6>
                            <h4 class="font-weight-normal">{{ $membership_cache->membership }}</h4>
                            <h6 class="font-weight-bold">{{ $membership_startdate }} - {{ $membership_enddate }}</h6>
                            <h6 class="mt-4 font-weight-normal">Total Kunjungan : <span>{{ $data->visitlog }}</span></h6>
                        </div>
                    </div>
                @endisset

                <?php
                function asRupiah2($value) {
                    if ($value<0) return "-".asRupiah(-$value);
                    return 'Rp. ' . number_format($value, 0);
                }?>

                @if($data->session > 0)
                    <div class="card mt-2" style="width: 250px;" id="cardDataPT">
                        <div class="card-body p-2">
                            <h6 class="font-weight-bold">Personal Trainer</h6>
                            <h4 class="font-weight-normal">@if(isset($pt->name)) {{ $pt->name }} @else - @endisset</h4>
                            <h6 class="font-weight-bold">{{ $data->session_reg }} Sesi</h6>
                            <h6 class="mt-4 font-weight-normal">Sisa Sesi : {{ $data->session }}</h6>
                        </div>
                    </div>
                @endif

                @if(isset($cicilan_member))
                    <hr style="width: 250px;" class="ml-0 mt-3" id="cardDataAndCicilanDivider">

                    @foreach($cicilan_member as $cc)
                        <div class="card bg-danger mt-2" style="width: 250px;">
                            <div class="card-body p-2">
                                <h6 class="font-weight-bold">Cicilan Aktif</h6>
                                <h4 class="font-weight-normal"><?php echo asRupiah2($cc->rest_price); ?></h4>
                                <h6 class="font-weight-bold">{{ $cc->rest_membership }}</h6>
                                <h6 class="mt-4 font-weight-normal">Sisa Tenor : {{ $cc->rest_duration }} Bulan</h6>
                                <h6 class="font-weight-normal">Sisa Cicilan : <?php echo asRupiah2($cc->rest_data); ?></h6>
                            </div>
                        </div>
                    @endforeach
                @endisset
            </div>
            <div class="col-md col-sm-12">
                <div class="card h-100">
                    <ul class="nav nav-tabs" id="member-manage-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="member-manage-detail-tab" data-toggle="pill" href="#member-manage-detail" role="tab" aria-controls="member-manage-detail-home" aria-selected="true">Informasi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="member-manage-membership-tab" data-toggle="pill" href="#member-manage-membership" role="tab" aria-controls="member-manage-membership-profile" aria-selected="false">Paket Member</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="member-manage-accounts-tab" data-toggle="pill" href="#member-manage-accounts" role="tab" aria-controls="member-manage-accounts-messages" aria-selected="false">Akun</a>
                        </li>
                    </ul>
                    <div class="card-body">
                        <div class="tab-content" id="custom-content-below-tabContent">
                            <div class="tab-pane fade show active" id="member-manage-detail" role="tabpanel" aria-labelledby="member-manage-detail-tab">
                                <h3 class="text-left mt-0 mb-2 col-12 mb-2 pl-0">Informasi Member<br><small style="font-size: 18px;">ID Member : <span>{{ $data->member_id }}</span></small></h3>
                                <div class="row">
                                    <div class="col-md-6 mt-4 pr-3">
                                        <h6><b>IDENTITAS</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>Nama Lengkap</b></h6>
                                        <h6 class="mb-3">@if(isset($data->name)) {{ $data->name }} @else - @endisset</h6>

                                        <h6><b>Jenis Kelamin</b></h6>
                                        <h6 class="mb-3">@if(isset($data->gender)) {{ $data->gender }} @else - @endisset</h6>

                                        <h6><b>Pekerjaan</b></h6>
                                        <h6 class="mb-3">@if(isset($data->job)) {{ $data->job }} @else - @endisset</h6>

                                        <h6><b>Perusahaan/Instansi</b></h6>
                                        <h6 class="mb-3">@if(isset($data->company)) {{ $data->company }} @else - @endisset</h6>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <h6><b>KONTAK</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>No. Telp.</b></h6>
                                        <h6 class="mb-3">@if(isset($data->phone)) {{ $data->phone }} @else - @endisset</h6>

                                        <h6><b>Email</b></h6>
                                        <h6 class="mb-3">@if(isset($data->email)) {{ $data->email }} @else - @endisset</h6>

                                        <h6><b>Tanggal Lahir</b></h6>
                                        <h6 class="mb-3">@if(isset($data->dob)) {{ $data->dob }} @else - @endisset</h6>

                                        <h6><b>Catatan</b></h6>
                                        <h6 class="mb-3">@if(isset($data->member_notes)) {{ $data->member_notes }} @else - @endisset</h6>

                                        <hr>
                                        <h6><b>Last Edited By</b></h6>
                                        <h6 class="mb-3">@if(isset($last_edited->name)) {{ $last_edited->name }} @else - @endisset</h6>
                                    </div>
                                    <div class="col-md-6 mt-4">
                                        <h6><b>ADDITIONAL INFO</b></h6>
                                        <hr>
                                        <h6 class="mt-2"><b>Marketing</b></h6>
                                        <h6 class="mb-3">@if(isset($cache->id_marketing)) {{ $marketing->name }} @else - @endisset</h6>

                                        <h6><b>Personal Trainer</b></h6>
                                        <h6 class="mb-3">@if(isset($pt)) {{ $pt->name }} @else - @endisset</h6>
                                    </div>
                                </div>

                                <form id="checkinForm" action="{{ route('member.checkin') }}" method="POST">
                                    {{ csrf_field() }}

                                    <input type="hidden" class="form-control" id="dataIDMemberCheckin" name="dataIDMemberCheckin">
                                    <input type="hidden" class="form-control" id="dataCheckinSource" name="dataCheckinSource" readonly>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="member-manage-membership" role="tabpanel" aria-labelledby="member-manage-membership-tab">
                                <div class="row pb-0">
                                    <div class="col-8">
                                        <h3 class="text-left mt-0 mb-2 col-12 mb-2">Paket Member</h3>
                                    </div>
{{--                                    <div class="col-4 text-right">--}}
{{--                                        <button type="button" class="btn btn-secondary mt-0 mb-2" disabled><i class="fas fa-plus mr-1 fa-xs"></i> Perpanjang</button>--}}
{{--                                    </div>--}}
                                    <div class="col-12">
                                        <hr>
                                        <table id="membershipTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                            <thead>
                                            <tr>
                                                <th class="align-middle">Paket Member</th>
                                                <th class="align-middle">Jenis</th>
                                                <th class="align-middle">Durasi</th>
                                                <th class="align-middle">Tanggal Mulai</th>
                                                <th class="align-middle">Tanggal Berakhir</th>
                                                <!--<th class="align-middle">Total Kunjungan</th>-->
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>

                                    <div class="col-8">
                                        <h3 class="text-left mt-3 mb-2 col-12 mb-2">Personal Trainer & Sesi Latihan</h3>
                                    </div>
{{--                                    <div class="col-4 text-right">--}}
{{--                                        <button type="button" class="btn btn-secondary mt-3 mb-2" disabled><i class="fas fa-plus mr-1 fa-xs"></i> Tambah Sesi</button>--}}
{{--                                    </div>--}}
                                    <div class="col-12">
                                        <hr>
                                        <table id="ptTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                            <thead>
                                            <tr>
                                                <th class="align-middle">Personal Trainer</th>
                                                <th class="align-middle">Jenis Kelamin</th>
                                                <th class="align-middle">Jumlah Sesi</th>
                                                <th class="align-middle">Sisa Sesi</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="member-manage-accounts" role="tabpanel" aria-labelledby="member-manage-accounts-tab">
                                <div class="row pb-0">
                                    <div class="col-12">
                                        <div class="float-right">
                                            <div class="input-group-prepend">
                                                <select data-column="3" class="form-control form-control-sm w-auto" id="tableFilterHistoryChartType">
                                                    <option value="daily" class="font-weight-bold">Filter By (Daily)</option>
                                                    <option value="monthly" selected>Filter By (Monthly)</option>
                                                    <option value="yearly">Filter By (Yearly)</option>
                                                </select>

                                                <select data-column="13" class="form-control form-control-sm w-auto ml-2" id="tableFilterHistoryChartMonth" style="display: none;">
                                                    <option value="all" class="font-weight-bold" selected>Bulan (All)</option>
                                                    <option value="1">Januari</option>
                                                    <option value="2">Februari</option>
                                                    <option value="3">Maret</option>
                                                    <option value="4">April</option>
                                                    <option value="5">Mei</option>
                                                    <option value="6">Juni</option>
                                                    <option value="7">Juli</option>
                                                    <option value="8">Agustus</option>
                                                    <option value="9">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>

                                                <select data-column="3" class="form-control form-control-sm ml-2 w-auto" id="tableFilterHistoryChartYear">
                                                    <option value="all" class="font-weight-bold" selected>Tahun (All)</option>
                                                    @foreach($filter_year_available as $FILTER_YEAR)
                                                        <option value="{{ $FILTER_YEAR->date }}">{{ $FILTER_YEAR->date }}</option>
                                                    @endforeach
                                                </select>

                                                <select data-column="5" class="form-control form-control-sm ml-2 w-auto" id="tableFilterHistoryChartYearDuration" style="display: none">
                                                    <option value="2">2 Years</option>
                                                    <option value="5" selected>5 Years</option>
                                                    <option value="10">10 Years</option>
                                                    <option value="15">15 Years</option>
                                                </select>
                                            </div>
                                        </div>
                                        <h3 class="text-left mt-0 mb-2 mb-2">Pengeluaran</h3>
                                        <hr>
                                        <div class="overflow-auto text-center group-history-chart-monthly" style="max-width: 100%;">
                                            <canvas id="memberHistoryChart" width="100" height="30" style="max-width: 100%;"></canvas>
                                        </div>
                                        <hr>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <h3 class="text-left col-8 mt-0 mb-2 mb-2">Riwayat Member</h3>
                                            <div class="col-4">
                                                <div class="float-right">
                                                    @include('config.filter.filter_log')
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <table id="accountHistoryTable" class="table table-bordered table-striped w-100" style="font-size: 14px;">
                                            <thead>
                                            <tr>
                                                <th class="align-middle">Tanggal</th>
                                                <th class="align-middle">Deskripsi</th>
                                                <th class="align-middle">Kategori</th>
                                                <th class="align-middle">Status</th>
                                                <th class="align-middle">Transaksi</th>
                                                <th class="align-middle">Aksi</th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('member.plugin.webcam')
@endsection

@section('import_script')
    @include('theme.default.import.modular.datatables.script')
    @include('chart.member_spending_chart')
@endsection

@section('message')
    @if(Session::has('success'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'success',
                button: false,
                html: '{{Session::get("success")}}',
                timer: 1500
            })
        </script>
        <?php Session::forget('success') ?>
    @endif

    @if(Session::has('failed'))
        <script type="text/javascript">
            Swal.fire({
                icon: 'warning',
                button: false,
                html: '{{Session::get("failed")}}',
                timer: 1500
            })
        </script>
        <?php Session::forget('failed') ?>
    @endif
@endsection

<script>
    @section('script')
    $(function () {
        $("#membershipTable").DataTable({
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberMembership', $data->member_id ) }}",
            iDisplayLength: 10,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'duration', name: 'duration' },
                { data: 'start_date', name: 'start_date' },
                { data: 'expired_date', name: 'expired_date' },
            ],
        });

        $("#ptTable").DataTable({
            searching: false,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberPT', $data->member_id ) }}",
            iDisplayLength: 10,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'gender', name: 'gender' },
                { data: 'jsession', name: 'jsession' },
                { data: 'session_left', name: 'session_left' },
            ],
        });

        const log_table =
        $('#accountHistoryTable').DataTable({
            searching: true,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            ajax: "{{ route('member.getMemberLog', $data->member_id ) }}",
            iDisplayLength: 10,
            "order": [[ 0, "desc" ]],
            columns: [
                { data: 'date', name: 'date' },
                { data: 'desc', name: 'desc' },
                { data: 'category', name: 'category' },
                { data: 'status', name: 'status' },
                { data: 'transaction', name: 'transaction' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
        });
        $("#accountHistoryTable_filter").hide();

        $("#tableFilterLogMemberStatus").on("change", function () {
            if (log_table.column(3).search() !== $(this).val()) {
                log_table.column(3).search($(this).val()).draw();
            }
        });

        $("#tableFilterLogMemberKategori").on("change", function () {
            if (log_table.column(2).search() !== $(this).val()) {
                log_table.column(2).search($(this).val()).draw();
            }
        });

        var nextMonth = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".$membership->duration ." month")) }});
        var nextMonth2 = new Date({{ date("Y-m-d",strtotime($data->membership_sdate."+".($membership->duration-1)." month")) }});
        var date = new Date({{ $data->membership_sdate }});
    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    function messagingError(){
        Toast.fire({
            icon: 'error',
            html: 'Data belum lengkap!'
        })
    }

    function messagingErrorCustom(message){
        Toast.fire({
            icon: 'error',
            html: message
        })
    }

    function checkinFailed(status){
        if(status == 'cuti'){
            Swal.fire({
                icon: 'warning',
                button: false,
                html: 'Member ini <b>sedang Cuti</b>!',
                timer: 3000
            });
        }else if(status == 'expired'){
            Swal.fire({
                icon: 'warning',
                button: false,
                html: 'Membership Member ini <b>telah expired</b>!',
                timer: 3000
            });
        }
    }

    @if($data->status == 1)
    function checkinMember(member){
        $("#dataIDMemberCheckin").val(member);

        if($("#dataIDMemberCheckin").val() == ""){
            messagingErrorCustom("Error when Checkin!");
        }else{
            $("#dataCheckinSource").val("view");
            $("#checkinForm").submit();
        }
    }
    @elseif($data->status == 2)
    function activateMember(member, duration){
        var token = '{{ csrf_token() }}';

        const DestroySwal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success mr-2',
                cancelButton: 'btn btn-outline-dark mr-2'
            },
            buttonsStyling: false
        })

        DestroySwal.fire({
            icon: 'warning',
            html: 'Apakah Anda yakin ingin melakukan Aktivasi untuk member ini ?</small>',
            showCancelButton: true,
            cancelButtonText: '<i class="fas fa-times fa-sm mr-1"></i> Batal',
            confirmButtonText: `<i class="fas fa-check fa-sm mr-1"></i> Aktivasi`,
            reverseButtons: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel
            ) {
                return false;
            }else{
                $.post("{{ route('member.aktivasi') }}", { id:member, _token:token, duration:duration}, function(data){
                    location.reload();
                });
            }
        });
    }
    @endif

    function asRupiah(value){
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'IDR',
        });

        var split = formatter.format(value).split(".00");
        var splitCurrency = split[0].split("IDR");

        return splitCurrency[1];
    }
    @endsection
</script>
