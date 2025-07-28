@extends('backend.layouts.master')

@section('title','E-SHOP || Banner Create')

@section('main-content')
<div class="card">
    <h5 class="card-header">Add Banner</h5>
    <div class="card-body">
      <form method="post" action="{{route('banner.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
        @error('title')
        <span class="text-danger">{{$message}}</span>
        @enderror
        </div>

        <div class="form-group">
            <label for="cat_id">Category <span class="text-danger">*</span></label>
            <select name="cat_id" id="cat_id" class="form-control">
                <option value="">--Select any category--</option>
                @foreach($categories as $key=>$cat_data)
                <option value='{{$cat_data->id}}'>{{$cat_data->title}}</option>
                @endforeach
            </select>
        </div>
        @error('cat_id')
            <span class="text-danger">{{$message}}</span>
            @enderror
        <div class="form-group d-none" id="child_cat_div">
            <label for="child_cat_id">Sub Category</label>
            <select name="child_cat_id" id="child_cat_id" class="form-control">
                <option value="">--Select any category--</option>
            </select>
        </div>
        @error('child_cat_id')
            <span class="text-danger">{{$message}}</span>
            @enderror
        <div class="form-group d-none" id="sub_child_cat_div">
            <label for="sub_child_cat_id">Sub Sub Category</label>
            <select name="sub_child_cat_id" id="sub_child_cat_id" class="form-control">
                <option value="">--Select any category--</option>
            </select>
            @error('sub_child_cat_id')
            <span class="text-danger">{{$message}}</span>
            @enderror
        </div>

        <div class="form-group">
          <label for="inputDesc" class="col-form-label">Description</label>
          <textarea class="form-control" id="description" name="description">{{old('description')}}</textarea>
          @error('description')
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
        $('#description').summernote({
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
</script>

<script>
    var customVariable = "{{ config('app.url') }}";
   $('#cat_id').change(function() {
           var cat_id = $(this).val();
           if (cat_id != null) {
               $.ajax({
                   url: customVariable +"/admin/category/" + cat_id + "/child",
                   data: {
                       _token: "{{csrf_token()}}",
                       id: cat_id
                   },
                   type: "POST",
                   success: function(response) {
                       if (typeof(response) != 'object') {
                           response = $.parseJSON(response)
                       }
                       var html_option = "<option value=''>----Select sub category----</option>"
                       if (response.status) {
                           var data = response.data;
                           if (data) {
                               $('#child_cat_div').removeClass('d-none');
                               $.each(data, function(id, title) {
                                   html_option += "<option value='" + id + "'>" + title + "</option>"
                               });
                           } else {
                               $('#child_cat_div').addClass('d-none');
                           }
                       } else {
                           $('#child_cat_div').addClass('d-none');
                       }
                       $('#child_cat_id').html(html_option);
                   }
               });
           }
       });
   
       $('#child_cat_id').change(function() {
           var child_cat_id = $(this).val();
           if (child_cat_id != null) {
               $.ajax({
                   url: customVariable +"/admin/category/" + child_cat_id + "/subchild",
                   data: {
                       _token: "{{csrf_token()}}",
                       id: child_cat_id
                   },
                   type: "POST",
                   success: function(response) {
                       if (typeof(response) != 'object') {
                           response = $.parseJSON(response)
                       }
                       var html_option = "<option value=''>----Select sub sub category----</option>"
                       if (response.status) {
                           var data = response.data;
                           if (data) {
                               $('#sub_child_cat_div').removeClass('d-none');
                               $.each(data, function(id, title) {
                                   html_option += "<option value='" + id + "'>" + title + "</option>"
                               });
                           } else {
                               $('#sub_child_cat_div').addClass('d-none');
                           }
                       } else {
                           $('#sub_child_cat_div').addClass('d-none');
                       }
                       $('#sub_child_cat_id').html(html_option);
                   }
               });
           }
       });
   </script>
@endpush
