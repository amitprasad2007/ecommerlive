@extends('backend.layouts.master')

@section('main-content')
<style>
    #image-preview {
        max-width: 300px;
        max-height: 300px;
        display: none;
    }
    .bootstrap-tagsinput .tag {
        margin-right: 2px;
        color: white;
        background-color: #007bff;
        padding: 5px;
        border-radius: 3px;
    }

    .bootstrap-tagsinput input {
        width: 2200px !important;
        height: 30px !important;
    }

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<div class="card">
<h5 class="card-header">Add Product</h5>

@if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card-body">
    <form method="post" action="{{route('product.store')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="form-group">
                <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{old('title')}}" class="form-control">
                @error('title')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="description" class="col-form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control descriptionclass" id="description" name="description">{{old('description')}}</textarea>
                @error('description')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="is_featured">Is Featured</label><br>
                <input type="checkbox" name='is_featured' id='is_featured' value='1' checked> Yes
            </div>
            <div class="form-group">
                <label for="inputsku" class="col-form-label">SKU <span class="text-danger">*</span></label>
                <input id="inputsku" type="text" name="sku"  placeholder="Enter SKU" value="{{old('sku')}}" class="form-control">
                @error('sku')
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
                <label for="slug" class="col-form-label">Slug<span class="text-danger">*</span> </label>
                <input id="slug" type="text" name="slug" placeholder="Enter slug" value="{{old('slug')}}" class="form-control">
                @error('slug')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
                <input id="price" type="number" name="price" placeholder="Enter price" value="{{old('price')}}" class="form-control">
                @error('price')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="brand_id">Brand</label>
                <select name="brand_id" class="form-control">
                    <option value="">--Select Brand--</option>
                    @foreach($brands as $brand)
                    <option value="{{$brand->id}}">{{$brand->title}}</option>
                    @endforeach
                </select>
                @error('brand_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="purchase_price">MRP <span class="text-danger">*</span></label>
                <input id="purchase_price" type="number" name="purchase_price" min="0" placeholder="Enter Purchase price" value="{{old('purchase_price')}}" class="form-control">
                @error('purchase_price')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group row">
                <label for="shipping_cost" style="margin-left: 15px;">Shipping Cost (Optional)</span></label>
                <input id="shipping_cost" type="number" name="shipping_cost" min="0" max="100" placeholder="Enter Shipping Cost" value="{{old('shipping_cost')}}" class="p-2 col-md-5 form-control" style="margin-left: 16px;">
                <select name="shipping_type" id="shipping_type"  class=" p-2 col-md-3 form-control" style="margin-left: 30px;" >
                    <option selected  value="flat">Flat</option>
                </select>
                @error('shipping_cost')
                <span class="text-danger">{{$message}}</span>
                @enderror
                @error('shipping_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="tag">Tags</label>
                <div class="col-lg-12">
                    <input type="text" id="tags" class="form-control p-2 col-md-5" style="width: fit-content" name="tags[]" placeholder="Type to add a tag" data-role="tagsinput" value="{{ old('tags') ? implode(',', old('tags')) : '' }}">
                </div>

                @error('tags.0')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group row ">
                <label for="tax" style="margin-left: 15px;">Tax (Optional) </label>
                <input id="tax" type="number" name="tax" min="0" placeholder="Enter Tax" value="{{old('tax')}}" class="p-2 col-md-5 form-control" style="margin-left: 16px;">
                <select name="tax_type" id="tax_type" class=" p-2 col-md-3 form-control" style="margin-left: 30px;" >
                    <option value="flat">Flat</option>
                    <option value="percent">Percent</option>
                </select>
                @error('tax')
                <span class="text-danger">{{$message}}</span>
                @enderror
                @error('tax_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group row">
                <label for="discount" style="margin-left: 15px;">Discount(%) (Optional)</label>
                <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount" value="{{old('discount')}}" class="p-2 col-md-5 form-control" style="margin-left: 16px;">
                <select name="discount_type" id="discount_type" class="p-2 col-md-3 form-control" style="margin-left: 30px;">
                    <option disabled value="flat">Flat</option>
                    <option selected value="percent">Percent</option>
                </select>
                @error('discount')
                <span class="text-danger">{{$message}}</span>
                @enderror
                @error('discount_type')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="stock">Quantity <span class="text-danger">*</span></label>
                <input id="stock" type="number" name="stock" min="0" placeholder="Enter quantity" value="{{old('stock')}}" class="form-control">
                @error('stock')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="unit">Unit</label>
                <select name="unit" id="unit" class="form-control" >
                    <option value="Pices">Pices</option>
                    <option value="Liters">Liters</option>
                    <option value="grams">Grams</option>
                </select>
                @error('unit')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="min_qty">Min Qty <span class="text-danger">*</span></label>
                <input id="min_qty" type="number" name="min_qty" min="0" placeholder="Enter quantity" value="{{old('min_qty')}}" class="form-control">
                @error('min_qty')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="form-group row">
                <label class="form-group" style="margin-left: 15px;" for="video">Product Videos </label>
                <select name="video_provider_id" id="video_provider_id" class="p-2 col-md-4 form-control" style="margin-left: 16px;">
                    <option value="">--Select Video Provider--</option>
                    @foreach($videoproviders as $videoprovider)
                        <option value="{{$videoprovider->id}}">{{$videoprovider->name}}</option>
                    @endforeach
                </select>
                @error('video_provider_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
                <label class="form-group" style="margin-left: 15px;" for="video_link"> Video Link </label>
                <input id="video_link" type="text" name="video_link" placeholder="Enter Video Link" style="margin-left: 16px;" value="{{old('video_link')}}" class="p-2 col-md-4 form-control">
                @error('video_link')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-group" for="todays_deal">Today's Deal </label>
                <select name="todays_deal" id="todays_deal" class="form-control" >
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            @error('todays_deal')
                <span class="text-danger">{{$message}}</span>
                @enderror

        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo</label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                  <i class="fa fa-picture-o"></i> Choose
                  </a>
              </span>
            <input id="thumbnail" class="form-control" type="text" name="photo" value="{{old('photo')}}">
          </div>
          <div id="holder" style="margin-top:15px;max-height:100px;"></div>
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
                <label for="inputMetaTitle" class="col-form-label">Meta Title <span class="text-danger">*</span></label>
                <input id="inputMetaTitle" type="text" name="meta_title" placeholder="Enter Meta Title" value="{{old('meta_title')}}" class="form-control">
                @error('meta_title')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="metadescription" class="col-form-label">Meta Description <span class="text-danger">*</span></label>
                <textarea class="form-control descriptionclass" id="meta_description" name="meta_description">{{old('meta_description')}}</textarea>
                @error('meta_description')
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
