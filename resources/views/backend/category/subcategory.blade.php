@extends('backend.layouts.master')

@section('main-content')
 <!-- DataTales Example -->
 <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Category Lists</h6>
      <a href="{{ route('admin.category.subcreate') }}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add Category">
        <i class="fas fa-plus"></i> Add SubCategory
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        @if($categories->count() > 0)
        <table class="table table-bordered" id="banner-dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>S.N.</th>
              <th>Title</th>
              <th>Slug</th>
              <th>Parent Category</th>
              <th>Photo</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>S.N.</th>
              <th>Title</th>
              <th>Slug</th>
              <th>Parent Category</th>
              <th>Photo</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </tfoot>
          <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->title }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->parent_info->title ?? '' }}</td>
                    <td>
                        @if($category->photo)
                            <img src="{{asset( 'storage/categories/thumbnails/'.$category->photo )}}" class="img-fluid" style="max-width:80px" alt="{{ $category->title }}">
                        @else
                            <img src="{{ asset('backend/img/thumbnail-default.jpg') }}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $category->status == 'active' ? 'badge-success' : 'badge-warning' }}">{{ $category->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.category.subedit', $category->id) }}" class="btn btn-primary btn-sm float-left mr-1" style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip" title="edit" data-placement="bottom">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('category.destroy', $category->id) }}">
                          @csrf
                          @method('delete')
                          <button class="btn btn-danger btn-sm dltBtn" data-id="{{ $category->id }}" style="height:30px; width:30px; border-radius:50%" data-toggle="tooltip" data-placement="bottom" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </form>
                    </td>
                </tr>
            @endforeach
          </tbody>
        </table>
        <span style="float:right">{{ $categories->links() }}</span>
        @else
          <h6 class="text-center">No Categories found!!! Please create a Category</h6>
        @endif
      </div>
    </div>
</div>
@endsection

@push('styles')
  <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
  <style>
      div.dataTables_wrapper div.dataTables_paginate {
          display: none;
      }
  </style>
@endpush

@push('scripts')
  <!-- Page level plugins -->
  <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
  <script>
      $('#banner-dataTable').DataTable({
          "columnDefs": [
              {
                  "orderable": false,
                  "targets": [3, 4, 5]
              }
          ]
      });

      $(document).ready(function() {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });

          $('.dltBtn').click(function(e) {
              var form = $(this).closest('form');
              var dataID = $(this).data('id');
              e.preventDefault();
              swal({
                  title: "Are you sure?",
                  text: "Once deleted, you will not be able to recover this data!",
                  icon: "warning",
                  buttons: true,
                  dangerMode: true,
              }).then((willDelete) => {
                  if (willDelete) {
                      form.submit();
                  } else {
                      swal("Your data is safe!");
                  }
              });
          });
      });
  </script>
@endpush
