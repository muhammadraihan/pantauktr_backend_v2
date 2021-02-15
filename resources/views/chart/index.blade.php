@extends('layouts.page')

@section('title', 'Laporan Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
@endsection

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-users'></i> Module: <span class='fw-300'>Laporan</span>
        <small>
            Module for manage user access.
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>
                    Laporan <span class="fw-300"><i>List</i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div id="chartPelanggaran"></div>
                    <div id="chartApresiasi"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                
@section('js')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>

var data = {!!json_encode($arrPelanggaran)!!}
// console.log(data);
var series = [];
var values = [];
for (const [key, value] of Object.entries(data)) {
    series.push(key);
    values.push(value);
//   console.log(key, value);
}
console.log(series,values);
Highcharts.chart('chartPelanggaran', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Chart Pelanggaran'
    },
    xAxis: {
        categories: series,
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Laporan'
        }
    },
    series: [{
        name: series,
        data: values
    }],
    
});

var data = {!!json_encode($arrApresiasi)!!}
// console.log(data);
var series = [];
var values = [];
for (const [key, value] of Object.entries(data)) {
    series.push(key);
    values.push(value);
//   console.log(key, value);
}
Highcharts.chart('chartApresiasi', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Chart Apresiasi'
    },
    xAxis: {
        categories: series,
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Laporan'
        }
    },
    series: [{
        name: series,
        data: values
    }]
});

</script>
@endsection