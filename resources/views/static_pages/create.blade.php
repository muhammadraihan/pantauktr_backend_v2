@extends('layouts.page')

@section('title', 'Static Page Create')

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Add New <span class="fw-300"><i>Static Page</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('static-page.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => 'static-page.store','method' => 'POST','class' =>
                    'needs-validation','novalidate']) !!}
                    <div class="form-group col-md-6 mb-3">
                        {{ Form::label('menu_name','Menu Name',['class' => 'required form-label'])}}
                        {{ Form::text('menu_name',null,['placeholder' => 'Menu Name','class' => 'form-control '.($errors->has('menu_name') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('menu_name'))
                        <div class="invalid-feedback">{{ $errors->first('menu_name') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        {{ Form::label('url','Url',['class' => 'required form-label'])}}
                        {{ Form::text('url',null,['placeholder' => 'Url','class' => 'form-control '.($errors->has('url') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('url'))
                        <div class="invalid-feedback">{{ $errors->first('url') }}</div>
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

    @section('js')
    @endsection