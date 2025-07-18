@extends('backend.layouts.master')

@section('main-content')

<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Product Lists</h6>
    <a href="{{route('product.manage')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add Product</a>
      <form id="deleteForm" action="{{ route('product.bulkDelete') }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" id="selectedeletedbtn" class="btn btn-danger btn-sm mr-2 float-right d-none dltBtn">Delete Selected</button>
      </form>
  </div>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
  <div class="card-body">
    <div class="table-responsive">
      @if(count($products)>0)
      <table class="table table-bordered" id="product-dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
              <th>
                  <input name="Selectall" id="Selectall" type="checkbox" value="" onclick="selectall(this)" />
                  <span class="text">Select All</span>
              </th>
            <th>S.N.</th>
            <th>Title</th>
            <th>Category</th>
            <th>Is Featured</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Brand</th>
            <th>Stock</th>
            <th>Photo</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
              <th >
                <input name="Selectall" id="Selectall" type="checkbox" value="" onclick="selectall(this)" />
                  <span class="text">Select All</span>
              </th>
            <th>S.N.</th>
            <th>Title</th>
            <th>Category</th>
            <th>Is Featured</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Brand</th>
            <th>Stock</th>
            <th>Photo</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </tfoot>
        <tbody>

          @foreach($products as $product)
            @php
            $sub_cat_info=DB::table('categories')->select('title')->where('id',$product->child_cat_id)->get();
            $brands=DB::table('brands')->select('title')->where('id',$product->brand_id)->get();
            @endphp
              <tr>
                  <td width="10%" style="padding-left:25px;padding-top:12px;">
                      <input name="product_ids[]" id="STCODE" type="checkbox" value="{{ $product->id }}" class="selectable-checkbox" />
                      <span class="text"></span>
                  </td>
                  <td>{{$product->id}}</td>
                  <td>{{$product->title}}</td>
                  <td>{{$product->cat_info['title']?? ""}}
                    <sub>
                        {{$product->sub_cat_info->title ?? ''}}
                    </sub>
                  </td>
                  <td>{{(($product->is_featured==1)? 'Yes': 'No')}}</td>
                  <td>Rs. {{$product->price}} /-</td>
                  <td>  {{$product->discount}}% OFF</td>
                  <td> {{ucfirst($product->brand->title ?? "")}}</td>
                  <td>
                    @if($product->stock>0)
                    <span class="badge badge-primary">{{$product->stock}}</span>
                    @else
                    <span class="badge badge-danger">{{$product->stock}}</span>
                    @endif
                  </td>
                  <td>
                      @if($product->photostring())
                          @php
                            $photo=explode(',',$product->photostring());
                          @endphp
                        <img src="{{ asset('storage/products/photos/thumbnails/'.$photo[0])}}" class="img-fluid zoom" style="max-width:80px" alt="avatar.png">
                      @else
                          <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                      @endif
                  </td>
                  <td>
                      @if($product->status=='active')
                          <span class="badge badge-success">{{$product->status}}</span>
                      @else
                          <span class="badge badge-warning">{{$product->status}}</span>
                      @endif
                  </td>
                  <td>
                      <a href="{{route('product.manage',$product->id)}}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom"><i class="fas fa-edit"></i></a>
                  <form method="POST" action="{{route('product.destroy',[$product->id])}}">
                    @csrf
                    @method('delete')
                        <button class="btn btn-danger btn-sm dltBtn" data-id="{{$product->id}}" style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fas fa-trash-alt"></i></button>
                      </form>
                  </td>
              </tr>
          @endforeach
        </tbody>         
      </table>
      <div class="d-flex justify-content-end mt-3" >
        {{$products->links()}}
      </div>      
      @else
        <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
          <h6 class="text-center m-0">No Products found! Please create a Product.</h6>
        </div>
      @endif
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

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/datatables/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('backend/vendor/datatables/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{asset('backend/js/demo/datatables-demo.js')}}"></script>
  <script>

      $('#product-dataTable').DataTable( {
        "scrollX": false
            "columnDefs":[
                {
                    "orderable":false,
                    "targets":[10,11,12]
                }
            ]
        } );
        // Sweet alert

        function deleteData(id){

        }
  </script>
  <script>
      $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $('.dltBtn').click(function(e){
            var form=$(this).closest('form');
              var dataID=$(this).data('id');
              // alert(dataID);
              e.preventDefault();
              swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this data!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                       form.submit();
                    } else {
                        swal("Your data is safe!");
                    }
                });
          })

      })
      function selectall(selectAllCheckbox) {
          let checkboxes = document.querySelectorAll('input[name="product_ids[]"]');
          checkboxes.forEach(function(checkbox) {
              checkbox.checked = selectAllCheckbox.checked;
          });

          updateHiddenFields();
          toggleDeleteButton();
      }

      document.querySelectorAll('input[name="product_ids[]"]').forEach(function(checkbox) {
          checkbox.addEventListener('change', function() {
              updateHiddenFields();
              toggleDeleteButton();
          });
      });

      function updateHiddenFields() {
          // Clear existing hidden fields
          document.querySelectorAll('#deleteForm input[name="product_ids[]"]').forEach(function(hiddenField) {
              hiddenField.remove();
          });

          // Add hidden fields for selected checkboxes
          document.querySelectorAll('input[name="product_ids[]"]:checked').forEach(function(checkbox) {
              let hiddenInput = document.createElement('input');
              hiddenInput.type = 'hidden';
              hiddenInput.name = 'product_ids[]';
              hiddenInput.value = checkbox.value;
              document.getElementById('deleteForm').appendChild(hiddenInput);
          });
      }

      function toggleDeleteButton() {
          let selectedCheckboxes = document.querySelectorAll('input[name="product_ids[]"]:checked');
          let deleteButton = document.getElementById('selectedeletedbtn');

          if (selectedCheckboxes.length > 0) {
              deleteButton.classList.remove('d-none');
          } else {
              $('#Selectall').prop('checked', false);
              deleteButton.classList.add('d-none');
          }
      }
  </script>
@endpush

