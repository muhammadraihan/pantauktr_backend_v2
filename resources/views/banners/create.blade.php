@extends('layouts.page')

@section('title', 'Banner Create')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/select2/select2.bundle.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>Add New <span class="fw-300"><i>Banner</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('banner.index')}}"><i class="fal fa-arrow-alt-left">
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
                    {!! Form::open(['route' => ['banner.store'],'method' =>
                    'POST','class' => 'needs-validation','enctype' => 'multipart/form-data','novalidate']) !!}
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('photo','Banner',['class' => 'required form-label'])}}
                        <div class="form-group">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input accept="image/*" name="photo" type="file" class="custom-file-input @if ($errors->has('photo'))
                                    is-invalid
                                @endif" id="photo" aria-describedby="image" required>
                                    <label class="custom-file-label" for="photo">Choose file</label>
                                </div>
                            </div>
                            @if ($errors->has('photo'))
                            <div class="text-danger">{{ $errors->first('photo') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-3">
                        <img id="image-preview" src="{{asset('img/placeholder.png')}}" class="shadow-2 img-thumbnail"
                            alt="">
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
        $('#photo').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
    });
</script>
@endsection