@extends('layouts.page')

@section('title', 'Berita Detail')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/summernote/summernote.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div id="panel-3" class="panel">
            <div class="panel-hdr">
            <h2>Edit <span class="fw-300"><i>{{$website->slug}}</i></span></h2>
                <div class="panel-toolbar">
                    <a class="nav-link active" href="{{route('website.index')}}"><i class="fal fa-arrow-alt-left">
                        </i>
                        <span class="nav-link-text">Back</span>
                    </a>
                    <button class="btn btn-panel" data-action="panel-fullscreen" data-toggle="tooltip"
                        data-offset="0,10" data-original-title="Fullscreen"></button>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <div class="form-group">
                        {{ Form::label('title','Title',['class' => 'required form-label'])}}
                        {{ Form::text('title',$website->title,['placeholder' => 'Jenis Laporan','class' => 'form-control '.($errors->has('name') ? 'is-invalid':''),'disabled'=> 'disabled'])}}
                        @if ($errors->has('title'))
                        <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                        @endif
                    </div>
                    <div class="form-group ">
                        {{ Form::label('slug','Slug',['class' => 'required form-label'])}}
                        {{ Form::text('slug',$website->slug,['placeholder' => 'Slug','id'=>'slug','class' => 'form-control'.($errors->has('slug') ? 'is-invalid':''),'disabled', 'disabled'])}}
                        @if ($errors->has('slug'))
                        <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                        @endif
                    </div>
                    
                    <div class="form-group ">
                        <img id="image-preview" src="{{$website->photo}}" class="shadow-2 img-thumbnail"
                            alt="">
                    </div>

                    <div class="form-group ">
                        {{ Form::label('description','Description',['class' => 'required form-label'])}}
                        {{ Form::textarea('description',$website->description,['placeholder' => 'Content','id'=>'summernote','class' => 'form-control'.($errors->has('slug') ? 'is-invalid':''),'disabled'=> 'disabled'])}}
                        @if ($errors->has('description'))
                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                        @endif
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{asset('js/formplugins/summernote/summernote.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#photo').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
        $('#summernote').summernote({
            height: 400,
            width:800,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['view', ['fullscreen','help']],
            ]
        });
        $('#summernote').summernote('disable');
    });
</script>
@endsection