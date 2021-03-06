@extends('layouts.page')

@section('title', 'Tindak Lanjut')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Proses Laporan No. {{$laporan->nomor_laporan}}</h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('laporan.index')}}"><i class="fal fa-arrow-alt-left">
                        </i>
                        <span class="nav-link-text">Back</span>
                    </a>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="panel-tag">
                        Form with <code>*</code> can not be empty.
                    </div>
                    {!! Form::open(['route' => ['tindak-lanjut.store'],'method' => 'POST','class' =>
                    'needs-validation','novalidate']) !!}
                    {!! Form::hidden('laporan_id', $laporan->uuid) !!}
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('status', 'Status', ['class' => 'required form-label']) !!}
                            {!! Form::select('status', [1 => 'Ditindak lanjuti',2=>'Selesai'], '', ['class' =>
                            'select2 form-control'.($errors->has('status') ? 'is-invalid':''), 'required'
                            => '', 'placeholder' => 'Select status ...']) !!}
                            @if ($errors->has('status'))
                            <div class="help-block text-danger">{{ $errors->first('status') }}</div>
                            @endif
                        </div>
                        @isset($tindak_lanjut)
                        @php
                        switch ($tindak_lanjut->status) {
                        case 1:
                        $status_proses = 'Ditindak lanjuti';
                        break;
                        case 2:
                        $status_proses = 'Selesai';
                        break;
                        default:
                        $status_proses = 'Diterima';
                        break;
                        }
                        @endphp
                        <div class="form-group col-md-4 mb-3">
                            {!! Form::label('status_proses', 'Status Terakhir', ['class' => 'required form-label']) !!}
                            {!! Form::text('status_proses', $status_proses, ['class' =>
                            'form-control','disabled']) !!}
                        </div>
                        @endisset
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('keterangan','Keterangan',['class' => 'required form-label'])}}
                            {{ Form::textarea('keterangan',null,['placeholder' => 'Keterangan','class' => 'form-control
                            '.($errors->has('keterangan') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                            @if ($errors->has('keterangan'))
                            <div class="invalid-feedback">{{ $errors->first('keterangan') }}</div>
                            @endif
                        </div>
                        @isset($tindak_lanjut)
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('keterangan_proses','Keterangan Terakhir',['class' => 'required
                            form-label'])}}
                            {{ Form::textarea('keterangan_proses',$tindak_lanjut->keterangan,['placeholder' =>
                            'Keterangan','class' =>
                            'form-control','disabled'])}}
                        </div>
                        @endisset
                    </div>
                </div>
                <div
                    class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row align-items-center">
                    <button class="btn btn-primary ml-auto" type="submit">Submit</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>

<script>
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>
@endsection