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

    .card {
        border: 1px solid #ddd;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .card-img-top {
        max-height: 150px;
        object-fit: cover;
    }

    .row {
        justify-content: start;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<div class="card">
    <h5 class="card-header">Edit Product</h5>

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
        <form method="post" action="{{ $product ? route('product.manage', $product->id) : route('product.manage') }}" enctype="multipart/form-data">
            {{csrf_field()}}


            <div class="form-group">
                <label for="inputTitle" class="font-weight-bold">Product Name <span class="text-danger">*</span></label>
                <input id="inputTitle" type="text" name="title" placeholder="Enter title" value="{{ old('title', $product->title ?? '') }}" class="form-control">
                @error('title')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="slug" class="font-weight-bold">Slug<span class="text-danger">*</span> </label>
                <input id="slug" type="text" name="slug" placeholder="Enter slug" value="{{ old('price', $product->slug ?? '') }}" class="form-control">
                @error('slug')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="description" class="font-weight-bold">Description <span class="text-danger">*</span></label>
                <textarea class="form-control descriptionclass" id="description" name="description">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                <div class="form-group">
                    <label for="is_featured" class="font-weight-bold">Is Featured</label><br>
                    <input type="checkbox" name='is_featured' id='is_featured' value='1' {{ old('is_featured', $product->is_featured ?? 0) ? 'checked' : '' }}> Yes
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-group">
                    <label class="font-weight-bold" for="todays_deal" >Today's Deal </label>
                    <select name="todays_deal" id="todays_deal" class="form-control">
                        <option value="0" {{ old('todays_deal', $product->todays_deal ?? '') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('todays_deal', $product->todays_deal ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                </div>
                @error('todays_deal')
                <span class="text-danger">{{$message}}</span>
                @enderror
                </div>
                <div class="col-md-4">
                <div class="form-group">
                <label for="inputsku" class="font-weight-bold">SKU <span class="text-danger">*</span></label>
                <input id="inputsku" type="text" name="sku" placeholder="Enter SKU" value="{{ old('sku', $product->sku ?? '') }}" class="form-control">
                @error('sku')
                <span class="text-danger">{{$message}}</span>
                @enderror
                </div>
            </div>
            </div>



            <div class="form-group">
                <label for="cat_id" class="font-weight-bold">Category <span class="text-danger">*</span></label>
                <select name="cat_id" id="cat_id" class="form-control">
                    <option value="">--Select any category--</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('cat_id', $product->cat_id ?? '') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            @error('cat_id')
            <span class="text-danger">{{$message}}</span>
            @enderror
            @php
                $sub_cat_info = DB::table('categories')->select('title')->where('id', $product->child_cat_id ?? null)->get();
            @endphp
            {{-- Child Category --}}
            <div class="form-group {{ old('child_cat_id', $product->child_cat_id ?? '') ? '' : 'd-none' }}" id="child_cat_div">
                <label for="child_cat_id" class="font-weight-bold">Sub Category</label>
                <select name="child_cat_id" id="child_cat_id" class="form-control">
                    <option value="">--Select any sub category--</option>
                    @foreach($childCategories as $child)
                        <option value="{{ $child->id }}" {{ old('child_cat_id', $product->child_cat_id ?? '') == $child->id ? 'selected' : '' }}>
                            {{ $child->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group {{ old('sub_child_cat_id', $product->sub_child_cat_id ?? '') ? '' : 'd-none' }}" id="sub_child_cat_div">
                <label for="sub_child_cat_id" class="font-weight-bold">Sub Sub Category</label>
                <select name="sub_child_cat_id" id="sub_child_cat_id" class="form-control">
                    <option value="">--Select any sub sub category--</option>
                    @foreach($subChildCategories as $subChild)
                    <option value="{{ $subChild->id }}" {{ old('sub_child_cat_id', $product->sub_child_cat_id ?? '') == $subChild->id ? 'selected' : '' }}>
                        {{ $subChild->title }}
                    </option>
                    @endforeach
                </select>
            </div>


    <div class="row">
    <div class="col-md-6">
            <div class="form-group">
                <label for="brand_id" class="font-weight-bold">Brand</label>
                <select name="brand_id" class="form-control">
                    <option value="">--Select Brand--</option>
                    @foreach($brands as $brand)
                    <option value="{{$brand->id}}" {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>{{$brand->title}}</option>
                    @endforeach
                </select>
                @error('brand_id')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            </div>
             <!-- Tags -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="tags" class="font-weight-bold">Tags</label>
                <input type="text" id="tags" class="form-control" name="tags[]" placeholder="Type to add a tag" data-role="tagsinput" value="{{ old('tags.0', isset($product->tags) ? implode(',', explode(',', $product->tags)) : '') }}">
                @error('tags.0')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


               </div>


    <div class="row">
    <div class="col-md-6">
            <div class="form-group">
                <label for="price" class="font-weight-bold">Price(NRS) <span class="text-danger">*</span></label>
                <input id="price" type="number" name="price" placeholder="Enter price" value="{{ old('price', $product->price ?? '') }}" class="form-control">
                @error('price')
                <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
            </div>
        <!-- Purchase Price -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="purchase_price" class="font-weight-bold">MRP <span class="text-danger">*</span></label>
                <input id="purchase_price" type="number" name="purchase_price" min="0" placeholder="Enter Purchase Price" value="{{ old('price', $product->purchase_price ?? '') }}" class="form-control">
                @error('purchase_price')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


    </div>


    <!-- Tax -->
    <div class="row">
           <!-- Shipping Cost -->
           <div class="col-md-4">
            <div class="form-group">
                <label for="shipping_cost" class="font-weight-bold">Shipping Cost <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input id="shipping_cost" type="number" name="shipping_cost" min="0" max="100" placeholder="Enter Shipping Cost" value="{{ old('price', $product->shipping_cost ?? '') }}" class="form-control">
                    <select name="shipping_type" id="shipping_type" class="form-control">
                        <option selected value="flat">Flat</option>
                    </select>
                </div>
                @error('shipping_cost')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('shipping_type')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="tax" class="font-weight-bold">Tax <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input id="tax" type="number" name="tax" min="0" placeholder="Enter Tax" value="{{ old('tax', $product->tax ?? '') }}" class="form-control">
                    <select name="tax_type" id="tax_type" class="form-control">
                        <option value="flat" {{ old('tax_type', $product->tax_type ?? '') == 'flat' ? 'selected' : '' }}>Flat</option>
                        <option value="percent" {{ old('tax_type', $product->tax_type ?? '') == 'percent' ? 'selected' : '' }}>Percent</option>
                    </select>
                </div>
                @error('tax')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('tax_type')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Discount -->
        <div class="col-md-4">
            <div class="form-group">
                <label for="discount" class="font-weight-bold">Discount(%)</label>
                <div class="input-group">
                    <input id="discount" type="number" name="discount" min="0" max="100" placeholder="Enter Discount" value="{{ old('discount', $product->discount ?? '') }}" class="form-control">
                    <select name="discount_type" id="discount_type" class="form-control">
                        <option disabled value="flat">Flat</option>
                        <option value="percent" {{ old('discount_type', $product->discount_type ?? 'percent') == 'percent' ? 'selected' : '' }}>Percent</option>
                    </select>
                </div>
                @error('discount')
                <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('discount_type')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- Quantity -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="stock" class="font-weight-bold">Quantity <span class="text-danger">*</span></label>
                <input id="stock" type="number" name="stock" min="0" placeholder="Enter Quantity" value="{{ old('stock', $product->stock ?? '') }}" class="form-control">
                @error('stock')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label for="min_qty" class="font-weight-bold">Min Quantity <span class="text-danger">*</span></label>
                <input id="min_qty" type="number" name="min_qty" min="0" placeholder="Enter Min Quantity" value="{{ old('min_qty', $product->min_qty ?? '') }}" class="form-control">
                @error('min_qty')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>



            <!-- Product Videos Field -->
            <div class="row">
            <div class="col-md-6">
             <div class="form-group">
                <label for="video_provider_id" class="font-weight-bold">Product Video Provider</label>
                <div class="row">
                    <div class="col-md-12">
                        <select name="video_provider_id" id="video_provider_id" class="form-control">
                            <option value="">-- Select Video Provider --</option>
                            @foreach($videoproviders as $videoprovider)
                                <option value="{{ $videoprovider->id }}"
                                    {{ old('video_provider_id', $product->video_provider_id ?? '') == $videoprovider->id ? 'selected' : '' }}>
                                    {{ $videoprovider->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('video_provider_id')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">
                <label for="video_link" class="font-weight-bold">Video Link</label>
                <div class="row">
                    <div class="col-md-12">
                        <input id="video_link" type="text" name="video_link" placeholder="Enter Video Link"
                            value="{{ old('video_link', $product->video_link ?? '') }}" class="form-control">
                        @error('video_link')
                            <span class="text-danger d-block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            </div>
            </div>



            <div class="form-group">
                <label for="image" class="font-weight-bold">Product Image</label>
                <input type="file" name="photo[]" id="thumbnail" class="form-control" onchange="previewImages(event)" multiple>
                <div id="image-previews"></div>
                <div style="margin-top: 10px;">
                    <div class="row" id="photo-container">
                        @forelse($product->photoproduct ?? [] as $photo)
                        <div class="col-md-3 photo-item" id="photo-{{ $photo->id }}">
                            <div class="card">
                                <img src="{{ asset('storage/products/photos/thumbnails/' . $photo->photo_path) }}" class="card-img-top img-fluid" alt="Product Photo">
                                <button type="button" class="btn btn-danger btn-sm remove-photo-btn" data-photo-id="{{ $photo->id }}">Remove</button>
                            </div>
                        </div>
                        @empty
                        <p class="text-center w-100">No photos available for this product.</p>
                        @endforelse
                    </div>
                </div>
                @error('photo')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="form-group">
                <label for="inputMetaTitle" class="font-weight-bold">Meta Title <span class="text-danger">*</span></label>
                <input id="inputMetaTitle" type="text" name="meta_title" placeholder="Enter Meta Title" value="{{ old('meta_title', $product->meta_title ?? '') }}" class="form-control">
                @error('meta_title')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="metadescription" class="font-weight-bold">Meta Description <span class="text-danger">*</span></label>
                <textarea class="form-control descriptionclass" id="meta_description" name="meta_description">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                @error('meta_description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="status" class="font-weight-bold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', $product->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $product->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group mb-3">
            <a href="{{route('product.index')}}" class="btn btn-warning">Back</a>
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
                url: customVariable + "/admin/category/" + cat_id + "/child",
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
                            $('#sub_child_cat_div').removeClass('d-none');
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

 var  sub_child_cat_id='{{$product->sub_child_cat_id ?? "" }}';

    $('#child_cat_id').change(function() {
        var child_cat_id = $(this).val();

        if (child_cat_id != null) {
            $.ajax({
                url: customVariable + "/admin/category/" + child_cat_id + "/subchild",
                data: {
                    _token: "{{csrf_token()}}",
                    id: child_cat_id
                },
                type: "POST",
                success: function (response) {
                    if (typeof response !== "object") {
                        response = $.parseJSON(response);
                    }
                    var html_option = "<option value=''>----Select sub sub category----</option>";
                    if (response.status) {
                        var data = response.data;
                        if (data) {
                            $('#sub_child_cat_div').removeClass('d-none');
                             $.each(data, function(id, title) {
                                html_option += "<option value='" + id + "' " + (sub_child_cat_id == id ? "selected" : "") + ">" + title + "</option>";
                            });
                        } else {
                            $('#sub_child_cat_div').addClass('d-none');
                        }
                    } else {
                        $('#sub_child_cat_div').addClass('d-none');
                    }
                    $('#sub_child_cat_id').html(html_option);
                },
            });
        }
    });

//     if(sub_child_cat_id!=null){
//       $('#child_cat_id').change();
//   }



</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoContainer = document.getElementById('photo-container');

        photoContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-photo-btn')) {
                event.preventDefault();
                const photoId = event.target.getAttribute('data-photo-id');

                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this photo!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        const deleteUrl = `{{ route('photos.delete', ':id') }}`.replace(':id', photoId);

                        fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                },
                            })
                            .then((response) => response.json())
                            .then((data) => {
                                if (data.success) {
                                    const photoItem = document.getElementById(`photo-${photoId}`);
                                    photoItem.remove();
                                    swal("Photo deleted successfully!", {
                                        icon: "success",
                                    });
                                } else {
                                    swal("Failed to delete the photo. Please try again.", {
                                        icon: "error",
                                    });
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                swal("An error occurred while deleting the photo.", {
                                    icon: "error",
                                });
                            });
                    } else {
                        swal("Your photo is safe!");
                    }
                });
            }
        });
    });
</script>

@endpush
