@extends('layouts.page')

@section('title', 'Laporan Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
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
                </div>
                <div class="panel-content">
                <a href="{{route('cetak.laporan_pelanggaran')}}" class="btn btn-primary" target="_blank">CETAK PELANGGARAN PDF</a>
                <a href="{{route('cetak.laporan_apresiasi')}}" class="btn btn-primary" target="_blank">CETAK APRESIASI PDF</a>
                </div>
                <div class="panel-content">
                    <!-- datatable start -->
                    <table id="datatable" class="table table-bordered table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Jenis Laporan</th>
                                <th>Jenis Apresiasi</th>
                                <th>Keterangan</th>
                                <th>Photo</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th>Negara</th>
                                <th>Place ID</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Tindak lanjut</th>
                                </tr>
                            </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('js/datagrid/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>

<script src="https://www.gstatic.com/firebasejs/8.2.9/firebase.js"></script>
<script>
   // Your web app's Firebase configuration
//    var firebaseConfig = {

//         apiKey: "AIzaSyC50URuPt2twB2PPnVL9EnNfmTutnfzRz8",
//         authDomain: "beaming-inn-300511.firebaseapp.com",
//         projectId: "beaming-inn-300511",
//         storageBucket: "beaming-inn-300511.appspot.com",
//         messagingSenderId: "1026419611530",
//         appId: "1:1026419611530:web:0d434f6cc08fae077f9a73",
//         measurementId: "G-4S57JLB9YP"
//     };
//     // Initialize Firebase
//     firebase.initializeApp(firebaseConfig);
//     //firebase.analytics();
//     const messaging = firebase.messaging();
    // messaging
    // .requestPermission()
    // .then(function () {
    // //MsgElem.innerHTML = "Notification permission granted." 
    // console.log("Notification permission granted.");

    // // get the token in the form of promise
    // return messaging.getToken()
    // })
    // .then(function(token) {
    // // print the token on the HTML page     
    // console.log('token',token);



    // })
    // .catch(function (err) {
    // console.log(err);
    // });

    // messaging.onMessage(function(payload) {
    // console.log(payload);
    // var notify;
    // notify = new Notification(payload.notification.title,{
    //     body: payload.notification.body,
    //     icon: payload.notification.icon,
    //     tag: "Dummy"
    // });
    // console.log(payload.notification);
    // });

    // self.addEventListener('notificationclick', function(event) {       
    // event.notification.close();
    // });

   $(document).ready(function(){

        $('#tahun').datepicker({
            orientation: "bottom left",
            format: " yyyy", // Notice the Extra space at the beginning
            viewMode: "years",
            minViewMode: "years",
            todayHighlight:'TRUE',
            autoclose: true,
        });

        $('#bulan').datepicker({
            orientation: "bottom left",
            format: " mm", // Notice the Extra space at the beginning
            viewMode: "months",
            minViewMode: "months",
            todayHighlight:'TRUE',
            autoclose: true,
        });

        $('#filter').click(function (e){
            var tahun = $('#tahun').val();
            // console.log(tahun);
            var bulan = $('#bulan').val();
            $('#datatable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                cache:false,
                url:'{{route('get.filter')}}',
                type : "GET",
                data: {bulan: bulan,tahun: tahun},
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
                },
                "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'jenis_laporan', name: 'jenis_laporan'},
                {data: 'jenis_apresiasi', name: 'jenis_apresiasi'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'photo', name: 'photo'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'place_id', name: 'place_id'},
                {data: 'created_by', name: 'created_by'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action',width:'10%',searchable:false}    
            ]
        });
            
        });

        $('#datatable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                cache:false,
                url:'{{route('laporan.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
                },
                "columns": [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'jenis_laporan', name: 'jenis_laporan'},
                {data: 'jenis_apresiasi', name: 'jenis_apresiasi'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'photo', name: 'photo'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'place_id', name: 'place_id'},
                {data: 'created_by', name: 'created_by'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action',width:'10%',searchable:false}    
            ]
        });
    });
</script>
@endsection
