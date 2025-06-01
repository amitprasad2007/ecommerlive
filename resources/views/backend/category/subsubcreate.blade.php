@extends('backend.layouts.master')

@section('main-content')

<div class="card">
    <h5 class="card-header">Add Sub SubCategory</h5>
    <div class="card-body">
      <form method="post" action="{{route('category.store')}}" enctype="multipart/form-data" >
        <input type="hidden" name="subsubcat" value="1">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="summary" class="col-form-label">Summary</label>
          <textarea class="form-control descriptionclass" id="summary" name="summary">{{old('summary')}}</textarea>
          @error('summary')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group " id='parent_cat_div'>
          <label for="parent_id">Parent Category <span class="text-danger">*</span></label>
          <select name="parent_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($parent_cats as $key=>$parent_cat)
                  <option value='{{$parent_cat->id}}'>{{$parent_cat->title}}</option>
              @endforeach
          </select>
          @error('parent_id')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group  " id='sub_cat_div'>
          <label for="sub_cat_id">Sub Category <span class="text-danger">*</span></label>
          <select name="sub_cat_id" class="form-control">
              <option value="">--Select any subcategory--</option>
              @foreach($sub_cats as $key=>$sub_cat)
                  <option value='{{$sub_cat->id}}' {{($sub_cat->id == old('sub_cat_id', $sub_cat->sub_cat_id)) ? 'selected' : ''}}>{{$sub_cat->title}}</option>
              @endforeach
          </select>
          @error('sub_cat_id')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
            <label for="inputPhoto" class="col-form-label">Photo</label>
            <div class="input-group">
              <span class="input-group-btn">
              <input type="file" name="photo" id="photo" class="form-control" onchange="previewImages(event)" >
              </span>
            </div>
            <div id="image-previews"></div>
            <div id="holder-photo" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group">
            <label for="inputIcon" class="col-form-label">Icons</label>
            <div class="input-group">
                <span class="input-group-btn">
                    <input type="file" name="icon_path" id="icon_path" class="form-control" onchange="previewImagesicon(event)" >
                </span>
            </div>
            <div id="image-previewsicon"></div>
            @error('icon')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
          </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
          <button type="reset" class="btn btn-warning">Reset</button>
           <button class="btn btn-success" type="submit">Submit</button>
        </div>
      </form>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
@endpush
@push('scripts')
<script src="{{asset('vendor/laravel-filemanager/js/stand-alone-button.js')}}"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<style>
    .note-editable {
        font-family: 'Open Sans', sans-serif !important;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script>
    $('#lfm').filemanager('image');
    $(document).ready(function() {
        $('.descriptionclass').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontsize', ['fontsize']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
        });
    });
    $('#parent_cat_div select').change(function() {
        var parent_id = $(this).val();
        if (parent_id != '') {
            $.ajax({
                url: '{{ url("admin/category/get-subcategories") }}/' + parent_id,
                type: 'GET',
                success: function(response) {
                    $('#sub_cat_div select').empty();
                    $('#sub_cat_div select').append('<option value="">--Select any subcategory--</option>');
                    $.each(response, function(key, subcat) {
                        $('#sub_cat_div select').append('<option value="' + subcat.id + '">' + subcat.title + '</option>');
                    });
                    $('#sub_cat_div').removeClass('d-none');
                }
            });
        } else {
            $('#sub_cat_div').addClass('d-none');
            $('#sub_cat_div select').empty();
            $('#sub_cat_div select').append('<option value="">--Select any subcategory--</option>');
        }
    });
    function previewImages(event) {
        const previewContainer = document.getElementById('image-previews');
        previewContainer.innerHTML = '';
        const files = event.target.files;

        Array.from(files).forEach(file => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.margin = '5px';
                previewContainer.appendChild(img);
            }

            reader.readAsDataURL(file);
        });
    }
    function previewImagesicon(event) {
        const previewContainer = document.getElementById('image-previewsicon');
        previewContainer.innerHTML = '';
        const files = event.target.files;
        Array.from(files).forEach(file => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '150px';
                img.style.margin = '5px';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
</script>
@endpush
