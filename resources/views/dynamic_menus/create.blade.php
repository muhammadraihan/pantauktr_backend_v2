@extends('layouts.page')

@section('title', 'Menu Create')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Add New <span class="fw-300"><i>Menu</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('dynamic_menu.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => ['dynamic_menu.store'],'method' =>
                    'POST','class' => 'needs-validation','enctype' => 'multipart/form-data','novalidate']) !!}
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('icon','Icon',['class' => 'required form-label'])}}
                        <div class="form-group">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input accept="image/*" name="icon" type="file" class="custom-file-input @if ($errors->has('icon'))
                                    is-invalid
                                @endif" id="icon" aria-describedby="image" required>
                                    <label class="custom-file-label" for="icon">Choose file</label>
                                </div>
                            </div>
                            @if ($errors->has('icon'))
                            <div class="text-danger">{{ $errors->first('icon') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        <img id="image-preview" src="{{asset('img/placeholder.png')}}" class="shadow-2 img-thumbnail"
                            alt="">
                    </div>
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('judul','Judul',['class' => 'required form-label'])}}
                        {{ Form::text('judul',null,['placeholder' => 'Judul','class' => 'form-control '.($errors->has('judul') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}
                        @if ($errors->has('judul'))
                        <div class="invalid-feedback">{{ $errors->first('judul') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-sm-6 col-xl-4">
                        <label>Status</label>
                        <select class="js-bg-color custom-select select2" name="status">
                            <option value=""> Status </option>
                            <option value="0"> Non Aktif</option>
                            <option value="1"> Aktif </option>
                        </select>
                        @if ($errors->has('status'))
                        <div class="help-block text-danger">{{ $errors->first('status') }}</div>
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
<script src="{{asset('js/formplugins/select2/select2.bundle.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
        $('#icon').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
    });
</script>
@endsection