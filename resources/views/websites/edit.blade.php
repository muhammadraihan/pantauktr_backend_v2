@extends('layouts.page')

@section('title', 'Berita Edit')

@section('css')
<link rel="stylesheet" media="screen, print" href="{{asset('css/formplugins/summernote/summernote.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-xl-6">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
            <h2>Edit <span class="fw-300"><i>{{$website->uuid}}</i></span></h2>
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
                    <div class="panel-tag">
                        Form with <code>*</code> can not be empty.
                    </div>
                    {!! Form::open(['route' => ['website.update',$website->uuid],'method' => 'PUT','class' => 'needs-validation','enctype' => 'multipart/form-data','novalidate']) !!}
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('title','Title',['class' => 'required form-label'])}}
                        {{ Form::text('title',$website->title,['placeholder' => 'Jenis Laporan','id'=>'titles','class' => 'form-control '.($errors->has('name') ? 'is-invalid':'')])}}
                        @if ($errors->has('title'))
                        <div class="invalid-feedback">{{ $errors->first('title') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('slug','Slug',['class' => 'required form-label'])}}
                        {{ Form::text('slug',$website->slug,['placeholder' => 'Slug','id'=>'slug','class' => 'form-control'.($errors->has('slug') ? 'is-invalid':'')])}}
                        @if ($errors->has('slug'))
                        <div class="invalid-feedback">{{ $errors->first('slug') }}</div>
                        @endif
                    </div>
                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('photo','Photo',['class' => 'required form-label'])}}
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
                        <img id="image-preview" src="{{$website->photo}}" class="shadow-2 img-thumbnail"
                            alt="">
                    </div>

                    <div class="form-group col-sm-6 col-xl-4">
                        {{ Form::label('description','Description',['class' => 'required form-label'])}}
                        {{ Form::textarea('description',$website->description,['placeholder' => 'Content','id'=>'summernote','class' => 'form-control'.($errors->has('slug') ? 'is-invalid':''),'required', 'autocomplete' => 'off'])}}

                        @if ($errors->has('description'))
                        <div class="invalid-feedback">{{ $errors->first('description') }}</div>
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
<script src="{{asset('js/formplugins/summernote/summernote.js')}}"></script>
<script>
    function slugify(text) {
    return text.toString().toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w\-]+/g, '')
      .replace(/\-\-+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '')
      .replace(/[\s_-]+/g, '-');
    }

    $('#titles').keyup(function(){
        $slug = slugify($(this).val());
        $('#slug').val($slug);
    })
    $(document).ready(function() {
        $('#photo').change(function(){
            let reader = new FileReader();reader.onload = (e) => { 
                $('#image-preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
        });
        $('#summernote').summernote({
            height: 400,
            width:600,
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
    });
</script>
@endsection