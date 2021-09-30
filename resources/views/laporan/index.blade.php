@extends('layouts.page')

@section('title', 'Laporan Management')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
<link rel="stylesheet" media="screen, print" href="{{asset('css/datagrid/datatables/datatables.bundle.css')}}">
<link rel="stylesheet" media="screen, print"
    href="{{asset('css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css')}}">
@endsection

@section('content')
<div class="subheader">
    <h1 class="subheader-title">
        <i class='subheader-icon fal fa-users'></i> Module: <span class='fw-300'>Laporan</span>
        <small>
            Module for manage laporan.
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
                    <input type="text" class="form-control js-bg-target" placeholder="Tahun" id="tahun" name="tahun"
                        autocomplete="off">
                </div>
                <div id="" class="form-group col-md-5 mb-3">
                    <label>Bulan</label>
                    <input type="text" class="form-control js-bg-target" placeholder="Bulan" id="bulan" name="bulan"
                        autocomplete="off">
                </div>
                <div class="form-group col-md-5 mb-3">
                    <label>Jenis Pelanggaran</label>
                    <select class="js-bg-color custom-select pelanggaran" name="pelanggaran">
                        <option value="">Jenis Pelanggaran</option>
                        @foreach($pelanggaran as $p)
                        <option value="{{$p->uuid}}"> {{$p->name}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5 mb-3">
                    <label>Bentuk Pelanggaran</label>
                    <select class="js-bg-color custom-select bp" name="bp">
                        <option value="">Bentuk Pelanggaran</option>    
                        @foreach($bentuk_pelanggaran as $bp)
                        <option value="{{$bp->uuid}}"> {{$bp->bentuk_pelanggaran}} </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5 mb-3">
                    <label>Kawasan</label>
                    <select class="js-bg-color custom-select kawasan" name="kawasan">
                        <option value="">Kawasan</option>    
                        @foreach($kawasan as $k)
                        <option value="{{$k->uuid}}"> {{$k->kawasan}} </option>
                        @endforeach
                    </select>
                </div>
                
                <div id="" class="form-group col-md-5 mb-3">
                    <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                    <button type="button" name="resetFilter" id="resetFilter" class="btn btn-primary">Reset
                        Filter</button>
                </div>
                <div class="panel-content">
                    <!-- datatable start -->
                    <table id="datatable" class="table table-bordered table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Bentuk Pelanggaran</th>
                                <th>Keterangan</th>
                                <th>Photo</th>
                                <th>Nama Lokasi</th>
                                <th>Kawasan</th>
                                <th>Tanggal</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Alamat</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th>Negara</th>
                                <th>Place ID</th>
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
<script src="{{asset('js/datagrid/datatables/datatables.export.js')}}"></script>
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script src="{{asset('js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
{{-- <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script> --}}

{{-- <script src="https://www.gstatic.com/firebasejs/8.2.9/firebase.js"></script> --}}
<script>
    $(document).ready(function(){
        $('.pelanggaran').select2();
        
        $('.bp').select2();
        
        $('.kawasan').select2();

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

        $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                url:'{{route('laporan.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
                },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        }
            ],
                "columns": [
                {data: 'rownum',searchable:false},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'photo', name: 'photo'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'kawasan', name: 'kawasan'},
                {data: 'created_at', name: 'created_at'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'place_id', name: 'place_id'},
                {data: 'action',width:'10%',searchable:false}    
            ]
        });

        $('#filter').click(function (e){
            var pelanggarans = $('.pelanggaran').val();
            var bentukPelanggaran = $('.bp').val();
            var kawasans = $('.kawasan').val();
            // console.log(pelanggarans,bentukPelanggaran,kawasans);
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            
           $('#datatable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[ 0, "asc" ]],
            "ajax":{
                url:'{{route('get.filter')}}',
                type : "GET",
                data: {pelanggaran: pelanggarans,bentuk_pelanggaran: bentukPelanggaran,kawasan: kawasans,bulan: bulan,tahun: tahun},
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        }
            ],
                "columns": [
                {data: 'rownum',searchable:false},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'photo', name: 'photo'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'kawasan', name: 'kawasan'},
                {data: 'created_at', name: 'created_at'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'place_id', name: 'place_id'},
                {data: 'action',width:'10%',searchable:false}    
            ]
         });
        });
        
        // clear filter dataTables
        $('#resetFilter').click(function(e){
            $('#bulan').val("").trigger('change');
            $('#tahun').val("").trigger('change');
            $('#datatable').DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "lengthChange": false,
            "order": [[ 0, "asc" ]],
            "ajax":{
                url:'{{route('laporan.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                    }
                },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'f><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'lB>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                            extend: 'pdfHtml5',
                            text: 'Export PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            orientation: 'landscape',
                            pageSize: 'LEGAL',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm',
                            exportOptions:{
                                columns:[0, 1, 2, 3,4,6,7,10,11,12,13,14,15]
                            }
                        }
            ],
                "columns": [
                {data: 'rownum',searchable:false},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'photo', name: 'photo'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'kawasan', name: 'kawasan'},
                {data: 'created_at', name: 'created_at'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'alamat', name: 'alamat'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'place_id', name: 'place_id'},
                {data: 'action',width:'10%',searchable:false}   
            ]
            });
        });
    });
</script>
@endsection