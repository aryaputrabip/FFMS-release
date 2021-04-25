@extends('layouts.app_admin')

@section('content')

    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-box bg-info">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text font">Total Member</span>
                    <span class="info-box-value">{{ $jMember }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-box bg-success">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-user-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Member Aktif</span>
                    <span class="info-box-value">{{ $memberActive }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-calendar-minus"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Member Cuti</span>
                    <span class="info-box-value">{{ $memberCuti }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="info-box bg-warning">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-dollar-sign"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Sales</span>
                    <span class="info-box-value">{{ $totalSales }}</span>
                </div>
            </div>
        </div>
    </div>

{{--    <div class="container">--}}
{{--        <div class="row justify-content-center">--}}
{{--            <div class="col-md-8">--}}
{{--                <div class="card">--}}
{{--                    <div class="card-header">Dashboard</div>--}}

{{--                    <div class="card-body">--}}
{{--                        @if (session('status'))--}}
{{--                            <div class="alert alert-success" role="alert">--}}
{{--                                {{ session('status') }}--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        You are logged in!--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection
