@extends('backend.layouts.master')
@section('title','E-SHOP || Brand Create')
@section('main-content')

<div class="card">
    <h5 class="card-header">Add Brand</h5>
    <div class="card-body">
      <form method="post" action="{{route('brand.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
        <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{old('title')}}" class="form-control">
        @error('title')
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
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script>
    $('#lfm').filemanager('image');

    $(document).ready(function() {
        $('#description').summernote({
        placeholder: "Write short description.....",
            tabsize: 2,
            height: 150
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
