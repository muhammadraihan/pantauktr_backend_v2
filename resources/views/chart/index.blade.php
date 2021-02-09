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
// Highcharts.chart('chartPelanggaran', {
//     chart: {
//         type: 'area'
//     },
//     title: {
//         text: 'Chart Pelanggaran'
//     },
//     xAxis: {
//         categories: ['a', 'b', 'c'],
//         tickmarkPlacement: 'on',
//         title: {
//             enabled: false
//         }
//     },
//     yAxis: {
//         title: {
//             text: 'Jumlah Laporan'
//         },
//         labels: {
//             formatter: function () {
//                 return this.value ;
//             }
//         }
//     },
//     tooltip: {
//         split: true,
//         valueSuffix: ''
//     },
//     plotOptions: {
//         area: {
//             stacking: 'normal',
//             lineColor: '#666666',
//             lineWidth: 1,
//             marker: {
//                 lineWidth: 1,
//                 lineColor: '#666666'
//             }
//         }
//     },
//     series: [{
//         name: 'KTR',
//         data: [3,10,2]
//     },{
//         name: 'TAPSBan',
//         data: [5,5,9]
//     },{
//         name: 'POS',
//         data: [1,2]
//     }]
// });

Highcharts.chart('chartPelanggaran', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Chart Pelanggaran'
    },
    xAxis: {
        categories: {!!json_encode($jenis_pelanggaran)!!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Laporan'
        }
    },
    series: [{
        name: 'KTR',
        data: {!!json_encode($data_ktr)!!}
    },{
        name: 'TAPSBan',
        data: {!!json_encode($data_tapsban)!!}
    },{
        name: 'POS',
        data: {!!json_encode($data_pos)!!}
    }],
    
});

Highcharts.chart('chartApresiasi', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'Chart Apresiasi'
    },
    xAxis: {
        categories: {!!json_encode($jenis_apresiasi)!!},
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Jumlah Laporan'
        }
    },
    series: [{
        name: 'Apresiasi',
        data: {!!json_encode($data_apresiasi)!!}
    },{
        name: 'Masukan',
        data: {!!json_encode($data_masukan)!!}
    }
    ]
});
</script>
@endsection