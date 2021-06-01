@extends('layouts.app_cs')

@section('content')

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text font">Total Member</span>
                    <span class="info-box-value">{{ $tMember }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-user-plus"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Member Baru (hari ini)</span>
                    <span class="info-box-value">{{ $tMemberBaru }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-calendar-check"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Aktivitas (hari ini)</span>
                    <span class="info-box-value">{{ $tAktivitas }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="info-box">
                <span class="info-box-icon w-auto pl-3 pr-2"><i class="fas fa-dollar-sign"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Revenue (hari ini)</span>
                    <span class="info-box-value">{{ $tRevenue }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <a href="{{ route('cs.member.checkin') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-calendar-check mr-1"></span> Check-In
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cs.member.registration.index') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-user-plus mr-1"></span> Tambah Member
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('cs.sesi.index') }}" class="btn btn-danger w-100 mb-2">
                <span class="fas fa-user mr-1"></span> Gunakan Sesi
            </a>
        </div>
    </div>
@endsection
