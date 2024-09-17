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
    <h5 class="card-header">Edit Product</h5>
    <div class="card-body">
      <form method="post" action="{{ route('product.update', $product->id) }}" enctype="multipart/form-data">
          @csrf
        @method('PATCH')
        <div class="form-group">
          <label for="inputTitle" class="col-form-label">Title <span class="text-danger">*</span></label>
          <input id="inputTitle" type="text" name="title" placeholder="Enter title"  value="{{$product->title}}" class="form-control">
          @error('title')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="description" class="col-form-label">Description</label>
          <textarea class="form-control descriptionclass" id="description" name="description">{{$product->description}}</textarea>
          @error('description')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="is_featured">Is Featured</label><br>
          <input type="checkbox" name='is_featured' id='is_featured' value='{{$product->is_featured}}' {{(($product->is_featured) ? 'checked' : '')}}> Yes
        </div>
          <div class="form-group">
              <label for="inputsku" class="col-form-label">SKU <span class="text-danger">*</span></label>
              <input id="inputsku" type="text" name="sku"  placeholder="Enter SKU" value="{{$product->sku}}" class="form-control">
              @error('sku')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>
              {{-- {{$categories}} --}}

        <div class="form-group">
          <label for="cat_id">Category <span class="text-danger">*</span></label>
          <select name="cat_id" id="cat_id" class="form-control">
              <option value="">--Select any category--</option>
              @foreach($categories as $key=>$cat_data)
                  <option value='{{$cat_data->id}}' {{(($product->cat_id==$cat_data->id)? 'selected' : '')}}>{{$cat_data->title}}</option>
              @endforeach
          </select>
        </div>
        @php
          $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
        // dd($sub_cat_info);

        @endphp
        {{-- {{$product->child_cat_id}} --}}
        <div class="form-group {{(($product->child_cat_id)? '' : 'd-none')}}" id="child_cat_div">
          <label for="child_cat_id">Sub Category</label>
          <select name="child_cat_id" id="child_cat_id" class="form-control">
              <option value="">--Select any sub category--</option>
          </select>
        </div>
          <div class="form-group {{(($product->sub_child_cat_id)? '' : 'd-none')}}" id="sub_child_cat_div">
              <label for="sub_child_cat_id">Sub Sub Category</label>
              <select name="sub_child_cat_id" id="sub_child_cat_id" class="form-control">
                  <option value="">--Select any sub sub category--</option>
              </select>
          </div>
          <div class="form-group">
              <label for="slug" class="col-form-label">Slug<span class="text-danger">*</span> </label>
              <input id="slug" type="text" name="slug" placeholder="Enter slug" value="{{$product->slug}}" class="form-control">
              @error('slug')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>
        <div class="form-group">
          <label for="price" class="col-form-label">Price(NRS) <span class="text-danger">*</span></label>
          <input id="price" type="number" name="price" placeholder="Enter price"  value="{{$product->price}}" class="form-control">
          @error('price')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
          <div class="form-group">
              <label for="brand_id">Brand</label>
              <select name="brand_id" class="form-control">
                  <option value="">--Select Brand--</option>
                  @foreach($brands as $brand)
                      <option value="{{$brand->id}}" {{(($product->brand_id==$brand->id)? 'selected':'')}}>{{$brand->title}}</option>
                  @endforeach
              </select>
          </div>
          <div class="form-group">
              <label for="purchase_price">Purchase price <span class="text-danger">*</span></label>
              <input id="purchase_price" type="number" name="purchase_price" min="0" placeholder="Enter Purchase price" value="{{$product->purchase_price}}" class="form-control">
              @error('purchase_price')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>
          <div class="form-group row">
              <label for="discount" style="margin-left: 15px;">Shipping Cost <span class="text-danger">*</span></label>
              <input id="shipping_cost" type="number" name="shipping_cost" min="0" max="100" placeholder="Enter Shipping Cost" value="{{$product->shipping_cost}}" class="p-2 col-md-5 form-control" style="margin-left: 16px;">
              <select name="shipping_type" id="shipping_type"  class=" p-2 col-md-3 form-control" style="margin-left: 30px;" >
                  <option selected  value="flat">Flat</option>
              </select>
          </div>
          <div class="form-group">
              <label for="tag">Tags</label>
              <input type="text" id="tags" name="tags"  placeholder="Enter Product Tag" value="{{$product->tags}}" class="form-control">
          </div>
          <div class="form-group row ">
              <label for="tax" style="margin-left: 15px;">Tax <span class="text-danger">*</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
              <input id="tax" type="number" name="tax" min="0" placeholder="Enter Tax" value="{{$product->tax}}" class="p-2 col-md-5 form-control" style="margin-left: 16px;">
              <select name="tax_type" id="tax_type" class=" p-2 col-md-3 form-control" style="margin-left: 30px;" >
                  <option value="flat" {{(($product->tax_type=='flat')? 'selected' : '')}} >Flat</option>
                  <option value="percent" {{(($product->tax_type=='percent')? 'selected' : '')}} >Percent</option>
              </select>
          </div>

          <div class="form-group row">
              <label for="discount" style="margin-left: 15px;">Discount(%)</label>
          <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter discount"  value="{{$product->discount}}"  class="p-2 col-md-5 form-control" style="margin-left: 16px;">

            <select  name="discounttype" id="discounttype"  class=" p-2 col-md-3 form-control" style="margin-left: 30px;" >
                <option disabled value="flat" >Flat</option>
                <option selected  value="percent">Percent</option>
            </select>
          @error('discount')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>


        <div class="form-group">
          <label for="stock">Quantity <span class="text-danger">*</span></label>
          <input id="quantity" type="number" name="stock" min="0" placeholder="Enter quantity"  value="{{$product->stock}}" class="form-control">
          @error('stock')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
          <div class="form-group">
              <label for="unit">Unit</label>
              <select name="unit" id="unit" class="form-control" >
                  <option value="Pices" {{(($product->unit=='Pices')? 'selected' : '')}} >Pices</option>
                  <option value="Liters" {{(($product->unit=='Liters')? 'selected' : '')}} >Liters</option>
                  <option value="grams" {{(($product->unit=='grams')? 'selected' : '')}} >Grams</option>
              </select>
          </div>
          <div class="form-group">
              <label for="min_qty">Min Qty <span class="text-danger">*</span></label>
              <input id="min_qty" type="number" name="min_qty" min="0" placeholder="Enter quantity" value="{{$product->min_qty}}" class="form-control">
              @error('min_qty')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>
          <div class="form-group row">
              <label class="form-group" style="margin-left: 15px;" for="video">Product Videos </label>
              <select name="video_provider_id" id="video_provider_id" class="p-2 col-md-4 form-control" style="margin-left: 16px;">
                  <option value="">--Select Video Provider--</option>
                  @foreach($videoproviders as $videoprovider)
                      <option value="{{$videoprovider->id}}" {{(($product->video_provider_id==$videoprovider->id)? 'selected' : '')}}>{{$videoprovider->name}}</option>
                  @endforeach
              </select>
              <label class="form-group" style="margin-left: 15px;" for="video_link"> Video Link </label>
              <input id="video_link" type="text" name="video_link" min="0" placeholder="Enter Video Link" style="margin-left: 16px;" value="{{$product->video_link}}" class="p-2 col-md-4 form-control">
          </div>
          <div class="form-group">
              <label class="form-group" for="todays_deal">Today's Deal </label>
              <select name="todays_deal" id="todays_deal" class="form-control" >
                  <option value="0" {{(($product->todays_deal=='0')? 'selected' : '')}}>No</option>
                  <option value="1" {{(($product->todays_deal=='1')? 'selected' : '')}}>Yes</option>
              </select>
          </div>
        <div class="form-group">
          <label for="inputPhoto" class="col-form-label">Photo <span class="text-danger">*</span></label>
          <div class="input-group">
              <span class="input-group-btn">
                  <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary text-white">
                  <i class="fas fa-image"></i> Choose
                  </a>
              </span>
          <input id="thumbnail" class="form-control" type="text" name="photo" value="{{$product->photo}}">
        </div>
        <div id="holder" style="margin-top:15px;max-height:100px;">
          @error('photo')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
          <div class="form-group">
              <label for="inputMetaTitle" class="col-form-label">Meta Title <span class="text-danger">*</span></label>
              <input id="inputMetaTitle" type="text" name="meta_title" placeholder="Enter Meta Title" value="{{$product->meta_title}}" class="form-control">
              @error('meta_title')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>
          <div class="form-group">
              <label for="metadescription" class="col-form-label">Meta Description <span class="text-danger">*</span></label>
              <textarea class="form-control descriptionclass" id="meta_description" name="meta_description">{{$product->meta_description}}"</textarea>
              @error('meta_description')
              <span class="text-danger">{{$message}}</span>
              @enderror
          </div>

        <div class="form-group">
          <label for="status" class="col-form-label">Status <span class="text-danger">*</span></label>
          <select name="status" class="form-control">
            <option value="active" {{(($product->status=='active')? 'selected' : '')}}>Active</option>
            <option value="inactive" {{(($product->status=='inactive')? 'selected' : '')}}>Inactive</option>
        </select>
          @error('status')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>

@endsection


@push('styles')
<link rel="stylesheet" href="{{asset('backend/summernote/summernote.min.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endpush
@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="{{asset('backend/summernote/summernote.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
<style>
	.note-editable {
		font-family: 'Open Sans', sans-serif !important;
	}
</style>
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
<script>
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
        $('#submit').on('submit', function() {
            var content = $('#summernote').val();
            var cleanContent = content.replace(/<[^>]+style="[^"]*font-family:[^"]*"[^>]*>/g, function(match) {
                return match.replace(/font-family:[^;]+;?/g, '');
            });
            $('#summernote').val(cleanContent);
        });
    });

</script>

<script>
    const appurl = "{{ config('app.url') }}";
  var  child_cat_id='{{$product->child_cat_id}}';
         alert(child_cat_id);
        $('#cat_id').change(function(){
            var cat_id=$(this).val();

            if(cat_id !=null){
                // ajax call
                $.ajax({
                    url: appurl+"/admin/category/"+cat_id+"/child",
                    type:"POST",
                    data:{
                        _token:"{{csrf_token()}}"
                    },
                    success:function(response){
                        if(typeof(response)!='object'){
                            response=$.parseJSON(response);
                        }
                        var html_option1="<option value=''>--Select any one--</option>";
                        if(response.status){
                            var data=response.data;
                            if(response.data){
                                $('#child_cat_div').removeClass('d-none');
                                $.each(data,function(id,title){
                                    html_option1 += "<option value='"+id+"' "+(child_cat_id==id ? 'selected ' : '')+">"+title+"</option>";
                                });
                            }
                            else{
                                console.log('no response data');
                            }
                        }
                        else{
                            $('#child_cat_div').addClass('d-none');
                        }
                        $('#child_cat_id').html(html_option1);

                    }
                });
            }
            else{

            }

        });
        if(child_cat_id!=null){
            $('#cat_id').change();
        }
  var  sub_child_cat_id='{{$product->sub_child_cat_id}}';
  // alert(child_cat_id);
  $('#child_cat_id').change(function(){
      var child_cat_id ='{{$product->child_cat_id}}';
      if(child_cat_id !=null){
          // ajax call
          $.ajax({
              url: appurl+"/admin/category/" + child_cat_id + "/subchild",
              type:"POST",
              data:{
                  _token:"{{csrf_token()}}"
              },
              success:function(response){
                  if(typeof(response)!='object'){
                      response=$.parseJSON(response);
                  }
                  var html_option="<option value=''>----Select sub sub category----</option>";
                  if(response.status){
                      var data=response.data;
                      if(response.data){
                          $('#sub_child_cat_div').removeClass('d-none');
                          $.each(data,function(id,title){
                              html_option += "<option value='"+id+"' "+(sub_child_cat_id==id ? 'selected ' : '')+">"+title+"</option>";
                          });
                      }
                      else{
                          console.log('no response data');
                      }
                  }
                  else{
                      $('#sub_child_cat_div').addClass('d-none');
                  }
                  $('#sub_child_cat_id').html(html_option);

              }
          });
      }
      else{

      }

  });
  if(sub_child_cat_id!=null){
      $('#child_cat_id').change();
  }
</script>
@endpush
