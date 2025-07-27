@extends('backend.layouts.master')

@section('main-content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Product Details</h6>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{$product->title}}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" class="form-control" id="category" name="category" value="{{$product->cat_info->title}}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Price</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{$product->price}}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category">Discount</label>
                    <input type="text" class="form-control" id="category" name="category" value="{{$product->discount}}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Discount Type</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{$product->discount_type == 'fixed' ? 'Fixed' : 'Percentage'}}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Brand</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{$product->brand->title}}" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Images</label>
                    @foreach($photoproduct as $photo)
                        <img src="{{asset('storage/products/photos/thumbnails/'.$photo->photo_path)}}" alt="Product Image" class="img-fluid" style="width: 100px; height: 100px;">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
  <link href="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate{
          display: none;
      }
      .zoom {
        transition: transform .2s; /* Animation */
      }

      .zoom:hover {
        transform: scale(5);
      }
  </style>
@endpush

@push('scripts')


@endpush
