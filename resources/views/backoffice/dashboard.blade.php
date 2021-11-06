@extends('layouts.page')

@section('title','Dashboard')

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='fal fa-desktop'></i> Dashboard
        <small>
            Informasi Singkat {{env('APP_NAME')}}
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$total_laporan}}
                    <small class="m-0 l-h-n">Total Laporan</small>
                </h3>
            </div>
            <i class="fal fa-ticket position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n1"
                style="font-size:6rem"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-danger-300 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$laporan_today}}
                    <small class="m-0 l-h-n">Laporan Hari Ini</small>
                </h3>
            </div>
            <i class="fal fa-ticket position-absolute pos-right pos-bottom opacity-15  mb-n1 mr-n4"
                style="font-size: 6rem;"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-info-200 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$laporan_proses}}
                    <small class="m-0 l-h-n">Laporan di Proses</small>
                </h3>
            </div>
            <i class="fal fa-ticket position-absolute pos-right pos-bottom opacity-15 mb-n5 mr-n6"
                style="font-size: 8rem;"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-success-300 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$laporan_selesai}}
                    <small class="m-0 l-h-n">Laporan Selesai</small>
                </h3>
            </div>
            <i class="fal fa-ticket position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                style="font-size: 6rem;"></i>
        </div>
    </div>
    @unlessrole('pemda')
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-primary-300 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$total_pengguna}}
                    <small class="m-0 l-h-n">Total Pelapor</small>
                </h3>
            </div>
            <i class="fal fa-users position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                style="font-size: 6rem;"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="p-3 bg-info-300 rounded overflow-hidden position-relative text-white mb-g">
            <div class="">
                <h3 class="display-4 d-block l-h-n m-0 fw-500">
                    {{$pengguna_month}}
                    <small class="m-0 l-h-n">Pengguna Baru Bulan Ini</small>
                </h3>
            </div>
            <i class="fal fa-users position-absolute pos-right pos-bottom opacity-15 mb-n1 mr-n4"
                style="font-size: 6rem;"></i>
        </div>
    </div>
    @endunlessrole
</div>

@endsection