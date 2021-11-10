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
        <i class='subheader-icon fal fa-clipboard-list'></i> Module: <span class='fw-300'>Laporan</span>
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
                <div class="panel-content">
                    <div class="form-row">
                        <div class="form-group col-md-3 mb-3">
                            <label>Tahun</label>
                            <input type="text" class="form-control js-bg-target" placeholder="Tahun" id="tahun"
                                name="tahun" autocomplete="off">
                        </div>
                        <div id="" class="form-group col-md-3 mb-3">
                            <label>Bulan</label>
                            <input type="text" class="form-control js-bg-target" placeholder="Bulan" id="bulan"
                                name="bulan" autocomplete="off">
                        </div>
                        @unlessrole('pemda')
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('city', 'Kota/Kab', ['class' => 'form-label']) !!}
                            {!! Form::select('city', $city, '', ['id'=>'city','class' => 'select2
                            form-control city', 'placeholder' => '']) !!}
                        </div>
                        @endunlessrole
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-3 mb-3">
                            {!! Form::label('pelanggaran', 'Jenis Pelanggaran', ['class' => 'form-label']) !!}
                            {!! Form::select('pelanggaran', $pelanggaran, '', ['id'=>'pelanggaran','class' => 'select2
                            form-control pelanggaran', 'placeholder' => '']) !!}
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('bentuk', 'Bentuk Pelanggaran', ['class' => 'form-label']) !!}
                            <select id="bentuk" class="form-control select2" name="bentuk" placeholder="">
                            </select>
                        </div>
                        <div id="kawasan-form" class="form-group col-md-4 mb-3" style="display:none">
                            {!! Form::label('kawasan', 'Kawasan', ['class' => 'form-label']) !!}
                            {!! Form::select('kawasan', $kawasan, '', ['id'=>'kawasan','class' => 'select2
                            form-control kawasan', 'placeholder' => '']) !!}
                        </div>
                    </div>

                    <div id="" class="form-group col-md-5 mb-3">
                        <button type="button" name="filter" id="filter" class="btn btn-primary"
                            disabled="disabled">Filter</button>
                        <button type="button" name="resetFilter" id="resetFilter" class="btn btn-primary"
                            disabled="disabled">Reset</button>
                    </div>
                    <!-- datatable start -->
                    <table id="datatable" class="table table-bordered table-hover table-striped w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Laporan</th>
                                <th>Jenis Pelanggaran</th>
                                <th>Bentuk Pelanggaran</th>
                                <th>Photo</th>
                                <th>Lokasi</th>
                                <th>Kawasan</th>
                                <th>Alamat</th>
                                <th>Tanggal</th>
                                <th>Kelurahan</th>
                                <th>Kecamatan</th>
                                <th>Kota</th>
                                <th>Provinsi</th>
                                <th>Negara</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Status</th>
                                <th>Proses Laporan</th>
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

<script>
    $(document).ready(function(){
        var date = new Date();
        // select
        $('#pelanggaran').select2({
            placeholder: "Pilih Jenis Pelanggaran",
        });
        $('#bentuk').select2({
            placeholder: "Pilih Bentuk Pelanggaran",
        });
        $('#kawasan').select2({
            placeholder: "Pilih Kawasan",
        });
        $('#city').select2({
            placeholder: "Pilih Kota/Kab",
        });

        $('#pelanggaran').change(function(e){
            var uuid = $(this).val();
            var option = $(this).select2('data');
            var optionSelected = option[0].text;
            // request bentuk pelanggaran
            $.ajax({
                url:"{{route('get.bentuk')}}",
                type: 'GET',
                data: {uuid:uuid},
                success: function(e) {
                    $("#bentuk").empty();
                    $("#bentuk").append('<option value="">Select Unit</option>');
                    $.each(e, function(key, value) {
                        $("#bentuk").append('<option value="'+ key +'">'+ value +'</option>');
                    });
                }
            });
            if (optionSelected === "Kawasan Tanpa Rokok") {
                $('#kawasan-form').show();
            }
            else {
                $('#kawasan-form').hide(); 
            }
        });

        $('#tahun').datepicker({
            orientation: "bottom left",
            format: "yyyy",
            startView: 'years',
            minViewMode: "years",
            todayHighlight:'TRUE',
            endDate: date.getFullYear().toString(),
            autoclose: true,
        });

        $('#bulan').datepicker({
            orientation: "bottom left",
            format: "mm",
            startView: 'months',
            minViewMode: "months",
            todayHighlight:'TRUE',
            autoclose: true,
        });

        // enable fitter button
        $('.form-control').change(function(e){
            $('#filter').attr('disabled',false);
            $('#resetFilter').attr('disabled',false);
        });

        $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "searching": false,
            "lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"] ],
            "order": [[ 0, "asc" ]],
            "ajax": {
                url:'{{route('laporan.index')}}',
                type : "GET",
                dataType: 'json',
                error: function(data){
                    console.log(data);
                }
            },
            "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"
            + "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [
                {
                    extend: 'pdfHtml5',
                    text: 'Export PDF',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    titleAttr: 'Generate PDF',
                    className: 'btn-outline-danger btn-sm mr-1',
                    exportOptions: {
                        columns:[0,1,2,3,5,6,7,8,11,12,16]
                    }
                },
                {
                    extend: 'print',
                    text: 'Print',
                    orientation: 'landscape',
                    pageSize: 'LEGAL',
                    titleAttr: 'Print Table',
                    className: 'btn-outline-primary btn-sm',
                    exportOptions: {
                        columns:[0,1,2,3,5,6,7,8,11,12,16]
                    }
                }
            ],
            "columns": [
                {data: 'rownum',searchable:false},
                {data: 'nomor_laporan',searchable:false},
                {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                {data: 'photo', name: 'photo'},
                {data: 'nama_lokasi', name: 'nama_lokasi'},
                {data: 'kawasan', name: 'kawasan'},
                {data: 'alamat', name: 'alamat'},
                {data: 'created_at', name: 'created_at'},
                {data: 'kelurahan', name: 'kelurahan'},
                {data: 'kecamatan', name: 'kecamatan'},
                {data: 'kota', name: 'kota'},
                {data: 'propinsi', name: 'propinsi'},
                {data: 'negara', name: 'negara'},
                {data: 'lat', name: 'lat'},
                {data: 'lng', name: 'lng'},
                {data: 'status', searchable:false},
                {data: 'action',width:'10%',searchable:false}    
            ]
        });

        $('#filter').click(function (e){
            var pelanggaran = $('#pelanggaran').val();
            var bentuk = $('#bentuk').val();
            var kawasan = $('#kawasan').val();
            var tahun = $('#tahun').val();
            var bulan = $('#bulan').val();
            var kota =$('#city').val();
            // load filtered datatable
           $('#datatable').DataTable({
               "destroy": true,
               "processing": true,
               "serverSide": true,
               "responsive": true,
               "searching": false,
               "lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"] ],
               "order": [[ 0, "asc" ]],
               "ajax": {
                   url:'{{route('get.filter')}}',
                   type : "GET",
                   data: {
                       bulan: bulan,
                       tahun: tahun,
                       city: kota,
                       pelanggaran: pelanggaran,
                       bentuk: bentuk,
                       kawasan: kawasan,
                    },
                    dataType: 'json',
                    error: function(data) {
                        console.log(data);
                    }
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"+ "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-danger btn-sm mr-1',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    }
                ],
                "columns": [
                    {data: 'rownum',searchable:false},
                    {data: 'nomor_laporan',searchable:false},
                    {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                    {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                    {data: 'photo', name: 'photo'},
                    {data: 'nama_lokasi', name: 'nama_lokasi'},
                    {data: 'kawasan', name: 'kawasan'},
                    {data: 'alamat', name: 'alamat'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'kelurahan', name: 'kelurahan'},
                    {data: 'kecamatan', name: 'kecamatan'},
                    {data: 'kota', name: 'kota'},
                    {data: 'propinsi', name: 'propinsi'},
                    {data: 'negara', name: 'negara'},
                    {data: 'lat', name: 'lat'},
                    {data: 'lng', name: 'lng'},
                    {data: 'action',width:'10%',searchable:false}    
                ]
            });
        });
        
        // clear filter dataTables
        $('#resetFilter').click(function(e){
            // clear filter fields
            $('#tahun').val('').datepicker("update");
            $('#bulan').val('').datepicker("update");
            $("#city").val(null).trigger('change');
            $("#pelanggaran").val(null).trigger('change');
            $("#bentuk").val(null).trigger('change');
            $("#kawasan").val(null).trigger('change');
            // disable filter function
            $('#filter').attr('disabled',true);
            $('#resetFilter').attr('disabled',true);
            // reload datatable
            $('#datatable').DataTable({
                "destroy" : true,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "searching": false,
                "lengthMenu": [ [10, 25, 50, 100, 200, 500, -1], [10, 25, 50, 100, 200, 500,"All"] ],
                "order": [[ 0, "asc" ]],
                "ajax": {
                    url:'{{route('laporan.index')}}',
                    type : "GET",
                    dataType: 'json',
                    error: function(data){
                        console.log(data);
                    }
                },
                "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>"+ "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    {
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Generate PDF',
                        className: 'btn-outline-danger btn-sm mr-1',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        orientation: 'landscape',
                        pageSize: 'LEGAL',
                        titleAttr: 'Print Table',
                        className: 'btn-outline-primary btn-sm',
                        exportOptions: {
                            columns:[0,1,2,3,5,6,7,8,11,12,16]
                        }
                    }
                ],
                "columns": [
                    {data: 'rownum',searchable:false},
                    {data: 'jenis_pelanggaran', name: 'jenis_pelanggaran'},
                    {data: 'bentuk_pelanggaran', name: 'bentuk_pelanggaran'},
                    {data: 'photo', name: 'photo'},
                    {data: 'nama_lokasi', name: 'nama_lokasi'},
                    {data: 'kawasan', name: 'kawasan'},
                    {data: 'alamat', name: 'alamat'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'kelurahan', name: 'kelurahan'},
                    {data: 'kecamatan', name: 'kecamatan'},
                    {data: 'kota', name: 'kota'},
                    {data: 'propinsi', name: 'propinsi'},
                    {data: 'negara', name: 'negara'},
                    {data: 'lat', name: 'lat'},
                    {data: 'lng', name: 'lng'},
                    {data: 'action',width:'10%',searchable:false}    
                ]
            });
        });
    });
</script>
@endsection