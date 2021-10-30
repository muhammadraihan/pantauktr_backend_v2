@extends('layouts.page')

@section('title', 'Kawasan Edit')

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Edit <span class="fw-300"><i>{{$kawasan->kawasan}}</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('kawasan.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => ['kawasan.update',$kawasan->uuid],'method' => 'PUT','class' =>
                    'needs-validation','novalidate','enctype' => 'multipart/form-data']) !!}
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('kawasan','Kawasan',['class' => 'required form-label'])}}
                            {{ Form::text('kawasan',$kawasan->kawasan,['placeholder' => 'Kawasan Pelanggaran','class' => 'form-control '.($errors->has('kawasan') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                            @if ($errors->has('kawasan'))
                            <div class="invalid-feedback">{{ $errors->first('kawasan') }}</div>
                            @endif
                        </div>
                        <div class="form-group col-md-4 mb-2">
                            {{ Form::label('image','Icon',['class' => 'required form-label'])}}
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input accept="image/*" name="image" type="file" class="custom-file-input @if ($errors->has('image'))
                                    is-invalid
                                @endif" id="image" aria-describedby="image" required>
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                                @if ($errors->has('image'))
                                <div class="text-danger">{{ $errors->first('image') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('keterangan','Keterangan',['class' => 'required form-label'])}}
                            {{ Form::textarea('keterangan',$kawasan->keterangan,['placeholder' => 'Keterangan','class' => 'form-control '.($errors->has('keterangan') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                            @if ($errors->has('keterangan'))
                            <div class="invalid-feedback">{{ $errors->first('keterangan') }}</div>
                            @endif
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <img id="image-preview" src="{{$kawasan->image}}" class="shadow-2 img-thumbnail"
                                alt="No image found">
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
    <script>
        $(document).ready(function(){
        $('#image').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
    });
    </script>
    @endsection