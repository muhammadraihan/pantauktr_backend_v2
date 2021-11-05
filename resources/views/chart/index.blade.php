@extends('layouts.page')

@section('title', 'Laporan Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print"
    href="{{asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css')}}">
@endsection

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-chart-bar'></i> Module: <span class='fw-300'>Statistic</span>
        <small>
            Module for report statistic
        </small>
    </h1>
</div>
<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    Statistik <span class="fw-300"><i>Laporan</i></span>
                </h2>
                <div class="panel-toolbar">
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div id="filter-form" class="form-row">
                        <div class="form-group col-md-3 mb-3">
                            <label>Tahun</label>
                            <input type="text" class="form-control js-bg-target" placeholder="Pilih Tahun" id="year"
                                name="year" autocomplete="off">
                        </div>
                        @unlessrole('pemda')
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('city', 'Kota/Kab', ['class' => 'form-label']) !!}
                            {!! Form::select('city', $city, '', ['id'=>'city','class' => 'select2
                            form-control', 'placeholder' => '']) !!}
                        </div>
                        @endunlessrole
                    </div>
                    <div id="" class="form-group col-md-4 mb-3">
                        <button type="button" name="filter" id="filter" class="btn btn-primary"
                            disabled="disabled">Filter</button>
                        <button type="button" name="resetFilter" id="resetFilter" class="btn btn-primary"
                            disabled="disabled">Reset</button>
                    </div>
                    <div id="pelanggaran" class="form-group mb-3"></div>
                    <div id="bentuk" class="form-group mb-3"></div>
                    <div id="kawasan" class="form-group mb-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="{{asset('js/statistics/highchart/highcharts.js')}}"></script>
<script src="{{asset('js/statistics/highchart/data.js')}}"></script>
<script src="{{asset('js/statistics/highchart/drilldown.js')}}"></script>
<script src="{{asset('js/statistics/highchart/exporting.js')}}"></script>
<script src="{{asset('js/statistics/highchart/export-data.js')}}"></script>
<script src="{{asset('js/statistics/highchart/accessibility.js')}}"></script>
<script src="{{asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

<script>
    $(document).ready(function(){
        var date = new Date();
        var yearRange = {!!json_encode($year_range)!!};
        $('.select2').select2({
            placeholder: "Pilih Kota/Kab",
        });

        $('#year').datepicker({
            orientation: "bottom left",
            format: "yyyy", 
            viewMode: "years",
            minViewMode: "years",
            todayHighlight:'TRUE',
            endDate: date.getFullYear().toString(),
            autoclose: true,
        });
        /* Enable filter button */
        $('#year').change(function (e) {
            $('#filter').attr('disabled',false);
            $('#resetFilter').attr('disabled',false);
        })
        $('#city').change(function (e){
            $('#filter').attr('disabled',false);
            $('#resetFilter').attr('disabled',false);
        })
        /* Reset filter value and disable filter button */
        $('#resetFilter').click(function(e) {
            $('#year').val('').datepicker("update");;
            $("#city").val(null).trigger('change');
            $('#filter').attr('disabled',true);
            $('#resetFilter').attr('disabled',true);
        });
        /* chart pelanggaran */
        var pelanggaranSeries = {!!json_encode($jenis_series)!!};
        var pelanggaranChart = Highcharts.chart('pelanggaran', {
                chart: {
                    type: 'column',
                    events: {
                        redraw: function() {
                            const label = this.renderer.label('Data Filtered', 100, 120).attr({
                                fill: Highcharts.getOptions().colors[0],
                                padding: 10,
                                r: 5,
                                zIndex: 20
                            }).css({
                                color: '#FFFFFF'
                            }).add();
                            setTimeout(() => {
                                label.fadeOut();
                            }, 1000);
                        },
                    },
                },
                title: {
                    text: 'Grafik Laporan Jenis Pelanggaran Tahun' + ' ' + yearRange
                },
                subtitle: {
                    text: 'Data ditampilkan per bulan'
                },
                xAxis: {
                    categories:['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Jumlah'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y}</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 2
                    },
                },
                series: pelanggaranSeries,
            });
        /* end chart pelanggaran */
        /* chart bentuk */
        var bentukSeries = {!!json_encode($bentuk_series)!!};
        var bentukDrillDown = {!!json_encode($bentuk_drilldown)!!};
        var bentukDrillDownSeries = [];
        for(var [key,value] of Object.entries(bentukDrillDown)){
            var arrayData = Object.keys(value.data).map((k) => value.data[k]);
            bentukDrillDownSeries.push({
                name: value.name,
                id: value.id,
                data : arrayData,
            });
        };
        var bentukChart = Highcharts.chart('bentuk', {
                chart: {
                    type: 'column',
                    events: {
                        redraw: function() {
                            const label = this.renderer.label('Data Filtered', 100, 120).attr({
                                fill: Highcharts.getOptions().colors[0],
                                padding: 10,
                                r: 5,
                                zIndex: 20
                            }).css({
                                color: '#FFFFFF'
                            }).add();
                            setTimeout(() => {
                                label.fadeOut();
                            }, 1000);
                        },
                    },
                },
                title: {
                    text: 'Grafik Laporan Bentuk Pelanggaran Tahun' + ' ' + yearRange
                },
                subtitle: {
                    text: 'Klik pada grafik untuk melihat data per bulan'
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
                },
                series : [
                    {
                        name: "Bentuk Pelanggaran",
                        colorByPoint: true,
                        data: bentukSeries,
                    }
                ],
                drilldown: {
                    series : bentukDrillDownSeries
                }
            });
        /* end chart bentuk */
        /* chart kawasan */
        var kawasanSeries = {!!json_encode($kawasan_series)!!};
        var kawasanDrillDown = {!!json_encode($kawasan_drilldown)!!};
        var kawasanDrillDownSeries = [];
        for(var [key,value] of Object.entries(kawasanDrillDown)){
            var arrayData = Object.keys(value.data).map((k) => value.data[k]);
            kawasanDrillDownSeries.push({
                name: value.name,
                id: value.id,
                data : arrayData,
            });
        };
        var kawasanChart = Highcharts.chart('kawasan', {
                chart: {
                    type: 'column',
                    events: {
                        redraw: function() {
                            const label = this.renderer.label('Data Filtered', 100, 120).attr({
                                fill: Highcharts.getOptions().colors[0],
                                padding: 10,
                                r: 5,
                                zIndex: 20
                            }).css({
                                color: '#FFFFFF'
                            }).add();
                            setTimeout(() => {
                                label.fadeOut();
                            }, 1000);
                        },
                    },
                },
                title: {
                    text: 'Grafik Kawasan Pelanggaran Tahun' + ' ' + yearRange
                },
                subtitle: {
                    text: 'Klik pada grafik untuk melihat data per bulan'
                },
                accessibility: {
                    announceNewData: {
                        enabled: true
                    }
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
                },
                series : [
                    {
                        name: "Kawasan",
                        colorByPoint: true,
                        data: kawasanSeries,
                    }
                ],
                drilldown: {
                    series : kawasanDrillDownSeries
                }
            });
        /* end chart kawasan */
        /* filter chart */
        $('#filter').click(function(e){
            var year = $('#year').val();
            var city = $('#city').val();
            var yearTitle = year ? 'Tahun ' + year : 'Tahun ' + yearRange;
            var cityTitle = city ? city: '';
            $.ajax({
                url: "{{route('get.filter-chart')}}",
                type: 'GET',
                dataType: 'JSON',
                data: { year:year, city:city },
                success: function(res) {
                    var resJenis = res.jenis_series;
                    var resBentuk = res.bentuk_series;
                    var resBentukDrill = res.bentuk_drilldown;
                    var resKawasan = res.kawasan_series;
                    var resKawasanDrill = res.kawasan_drilldown;

                    /* update jenis pelanggaran chart */
                    const arrJenis = resJenis.map((itemJenis,keyJenis) => {
                        const containerJenis = {};
                        containerJenis[keyJenis] = itemJenis.data;
                        return containerJenis;
                    })
                    // update subtitle
                    pelanggaranChart.setTitle({
                        text: 'Grafik Laporan Jenis Pelanggaran' +' '+ cityTitle +' '+ yearTitle
                    });
                    // update series
                    for (let i = 0; i < pelanggaranChart.series.length; i++) {
                        pelanggaranChart.series[i].setData(arrJenis[i][i]);
                    }
                    /* end update jenis pelanggaran chart */

                    /* update bentuk pelanggaran chart */
                    // update subtitle
                    bentukChart.setTitle({
                        text: 'Grafik Laporan Bentuk Pelanggaran' +' '+ cityTitle +' '+ yearTitle
                    });
                    // update series
                    bentukChart.series[0].setData(resBentuk);
                    
                    var bentukDrillDownSeriesUpdated = [];

                    for(var [keyDrillBentuk,valueDrillBentuk] of Object.entries(resBentukDrill)){
                        var arrayBentukDrillData = Object.keys(valueDrillBentuk.data).map((k) => valueDrillBentuk.data[k]);
                        bentukDrillDownSeriesUpdated.push({
                            name: valueDrillBentuk.name,
                            id: valueDrillBentuk.id,
                            data : arrayBentukDrillData,
                        });
                    };
                    // update drilldown series
                    bentukChart.update({
                        drilldown:{
                            series: bentukDrillDownSeriesUpdated
                        },
                    });
                    /* end update bentuk pelanggaran chart */

                    /* update bentuk pelanggaran chart */
                    // update subtitle
                    kawasanChart.setTitle({
                        text: 'Grafik Kawasan Pelanggaran Tahun' +' '+ cityTitle +' '+ yearTitle
                    });
                    kawasanChart.series[0].setData(resKawasan);
                    // update series
                    var kawasanDrillDownSeriesUpdated = [];
                    for(var [keyDrillKawasan,valueDrillKawasan] of Object.entries(resKawasanDrill)){
                        var arrayKawasanDrillData = Object.keys(valueDrillKawasan.data).map((k) => valueDrillKawasan.data[k]);
                        kawasanDrillDownSeriesUpdated.push({
                            name: valueDrillKawasan.name,
                            id: valueDrillKawasan.id,
                            data : arrayKawasanDrillData,
                        });
                    };
                    // update drilldown series
                    kawasanChart.update({
                        drilldown:{
                            series: kawasanDrillDownSeriesUpdated
                        },
                    });
                },
            });            
        });
    });
</script>
@endsection