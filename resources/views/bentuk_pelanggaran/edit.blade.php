@extends('layouts.page')

@section('title', 'Bentuk Pelanggaran Edit')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
@endsection

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
                    'needs-validation','novalidate','enctype' => 'multipart/form-data']) !!}
                    <div class="form-row">
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('bentuk_pelanggaran','Bentuk Pelanggaran',['class' => 'required form-label'])}}
                            {{ Form::text('bentuk_pelanggaran',$bentuk_pelanggaran->bentuk_pelanggaran,['placeholder' => 'Bentuk Pelanggaran','class' => 'form-control '.($errors->has('bentuk_pelanggaran') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                            @if ($errors->has('bentuk_pelanggaran'))
                            <div class="invalid-feedback">{{ $errors->first('bentuk_pelanggaran') }}</div>
                            @endif
                        </div>
                        <div class="from-grop col-md-4 mb-3">
                            {!! Form::label('pelanggaran', 'Jenis Pelanggaran', ['class' => 'required form-label']) !!}
                            {!! Form::select('pelanggaran', $pelanggarans,
                            $bentuk_pelanggaran->jenis_pelanggaran,
                            ['class' => 'select2 form-control
                            '.($errors->has('pelanggaran') ? 'is-invalid':''),'placeholder' => 'Select
                            Pelanggaran','required']) !!}
                            @if ($errors->has('pelanggaran'))
                            <div class="invalid-feedback">{{ $errors->first('pelanggaran') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-row">
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
                            <div class="form-group">
                                <img id="image-preview" src="{{$bentuk_pelanggaran->image}}"
                                    class="shadow-2 img-thumbnail" alt="Image Not Found">
                            </div>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            {{ Form::label('keterangan','Keterangan',['class' => 'required form-label'])}}
                            {{ Form::textarea('keterangan',$bentuk_pelanggaran->keterangan,['placeholder' => 'Keterangan','class' => 'form-control '.($errors->has('keterangan') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                            @if ($errors->has('keterangan'))
                            <div class="invalid-feedback">{{ $errors->first('keterangan') }}</div>
                            @endif
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
</div>
@endsection

@section('js')
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#image').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
    });
</script>
@endsection