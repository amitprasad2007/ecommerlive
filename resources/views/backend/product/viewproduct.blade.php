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
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Short Description</label>
                    <textarea class="form-control" id="description" name="description" readonly>{{$product->description}}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Meta Description</label>
                    <textarea class="form-control" id="description" name="description" readonly>{{$product->meta_description}}</textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Additional Specification</label>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Value</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($specifications as $specification)
                                <tr>
                                    <td>{{$specification->name}}</td>
                                    <td>{{$specification->value}}</td>
                                    <td>
                                        <form action="{{ route('product.specifications.remove', $specification->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title">Additional Specification</label>
                    <form action="{{ route('product.specifications', $product->id) }}" method="POST">
                        @csrf
                        <div id="specifications">
                            <div class="form-row mb-2 specification-row">
                                <div class="col">
                                    <input type="text" class="form-control" name="spec_key[]" placeholder="Key">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="spec_value[]" placeholder="Value">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success add-spec">+</button>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Add key-value pairs for additional specifications.</small>
                        <div class="text-right mt-2">
                            <button type="submit" class="btn btn-primary">Save Specifications</button>
                        </div>
                    </form>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const specifications = document.getElementById('specifications');
        specifications.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-spec')) {
                e.preventDefault();
                const row = e.target.closest('.specification-row');
                const newRow = row.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelector('.add-spec').classList.remove('btn-success', 'add-spec');
                newRow.querySelector('.btn').classList.add('btn-danger', 'remove-spec');
                newRow.querySelector('.btn').textContent = '-';
                specifications.appendChild(newRow);
            } else if (e.target.classList.contains('remove-spec')) {
                e.preventDefault();
                const row = e.target.closest('.specification-row');
                row.remove();
            }
        });
    });
</script>
@endpush
