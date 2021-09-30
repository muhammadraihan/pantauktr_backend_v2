@extends('layouts.page')

@section('title', 'Laporan Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css')}}">
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
                <div class="form-group col-md-5 mb-3">
                    <label>Tahun</label>
                    <input type="text" class="form-control js-bg-target" placeholder="Tahun"
                            id="tahun" name="tahun" autocomplete="off">
                </div>
                <div id="" class="form-group col-md-5 mb-3">
                    <label>Bulan</label>
                    <input type="text" class="form-control js-bg-target" placeholder="Bulan"
                            id="bulan" name="bulan" autocomplete="off">
                </div>
                <div id="" class="form-group col-md-5 mb-3">
                    <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                    <button type="button" name="resetFilter" id="resetFilter" class="btn btn-primary" onClick="refreshPage()">Reset
                        Filter</button>
                </div>
                <div class="panel-content">
                    <div id="chartPelanggaran"></div>
                    <div id="chartBentukPelanggaran"></div>
                    <div id="chartKawasan"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
                
@section('js')
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="{{asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/dependency/moment/moment.js')}}"></script>

<script>

        $('#tahun').datepicker({
            orientation: "bottom left",
            format: " yyyy", 
            viewMode: "years",
            minViewMode: "years",
            todayHighlight:'TRUE',
            autoclose: true,
        });

        $('#bulan').datepicker({
            orientation: "bottom left",
            format: " mm", 
            viewMode: "months",
            minViewMode: "months",
            todayHighlight:'TRUE',
            autoclose: true,
        });

        $('#filter').click(function (e){
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            $.ajax({
                url: "{{route('get.filters')}}",
                type: 'GET',
                data: {bulan: bulan, tahun: tahun},
                success: function (response) {
                    // var month = moment().month(bulan).format('MMMM');
                    var month = moment(bulan, 'MM').format('MMMM');
                    // filter chart pelanggaran
                    var series = [];
                    var values = [];
                    $.each(response[0], function(key,value){
                        series.push(key);
                        values.push({
                            name: key,
                            data: [value]
                        });
                    });
                    Highcharts.chart('chartPelanggaran', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Chart Pelanggaran'+' '+month+' '+tahun
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
                        series: values,

                    });
                    // filter chart bentuk pelanggaran
                    var seriesBp = [];
                    var valuesBp = [];
                    $.each(response[1], function(key,value){
                        seriesBp.push(key);
                        valuesBp.push({
                            name: key,
                            data: [value]
                        });
                    });
                    Highcharts.chart('chartBentukPelanggaran', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Chart Bentuk Pelanggaran'+' '+month+' '+tahun
                        },
                        xAxis: {
                            categories: seriesBp,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Jumlah Laporan'
                            }
                        },
                        series: valuesBp,

                    });
                    // filter chart kawasan
                    var seriesKawasan = [];
                    var valuesKawasan = [];
                    $.each(response[2], function(key,value){
                        seriesKawasan.push(key);
                        valuesKawasan.push({
                            name: key,
                            data: [value]
                        });
                    });
                    Highcharts.chart('chartKawasan', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Chart Kawasan'+' '+month+' '+tahun
                        },
                        xAxis: {
                            categories: seriesKawasan,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Jumlah Laporan'
                            }
                        },
                        series: valuesKawasan,

                    });
                }
            });
        });
        
        //chart pelanggaran
        var data = {!!json_encode($arrPelanggaran)!!}
        var series = [];
        var values = [];
        for (const [key, value] of Object.entries(data)) {
            series.push(key);
            values.push({
                name: key,
                data: [value]
            });
        }
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
            series: values,

        });
        //chart bentuk pelanggaran
        var data = {!!json_encode($arrBentukPelanggaran)!!}
        var series = [];
        var values = [];
        for (const [key, value] of Object.entries(data)) {
            series.push(key);
            values.push({
                name: key,
                data: [value]
            });
        }
        Highcharts.chart('chartBentukPelanggaran', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Chart Bentuk Pelanggaran' 
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
            series: values,

        });
        //chart kawasan
        var data = {!!json_encode($arrKawasan)!!}
        var series = [];
        var dtx = [];
        for (const [key, value] of Object.entries(data)) {
            series.push(key);
            dtx.push({
                name: key,
                data: [value]
            });
        }
        Highcharts.chart('chartKawasan', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Chart Kawasan' 
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
            series: dtx

        });
        // reset filter
        function refreshPage(){
            window.location.reload();
        } 
</script>
@endsection