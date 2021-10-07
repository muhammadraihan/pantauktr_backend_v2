@extends('layouts.page')

@section('title', 'Bentuk Pelanggaran Edit')

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Edit <span class="fw-300"><i>{{$bentuk_pelanggaran->bentuk_pelanggaran}}</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('bentuk-pelanggaran.index')}}"><i
                            class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => ['bentuk-pelanggaran.update',$bentuk_pelanggaran->uuid],'method' =>
                    'PUT','class' =>
                    'needs-validation','novalidate']) !!}
                    <div class="form-group col-md-4 mb-3">
                        {{ Form::label('name','Bentuk Pelanggaran',['class' => 'required form-label'])}}
                        {{ Form::text('name',$bentuk_pelanggaran->bentuk_pelanggaran,['placeholder' => 'Bentuk Pelanggaran','class' => 'form-control '.($errors->has('name') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
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