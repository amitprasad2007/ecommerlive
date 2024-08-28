@extends('backend.layouts.master')

@section('main-content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
        	<h6 class="m-0 font-weight-bold text-primary float-left">Product Upload</h6>
      		<a href="{{route('admin')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="To Dashboard"><i class="fa fa-arrow-left"></i> Back</a>
      		<div class="card-body">
          		<form action="{{ route('product.bulkUpload') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                	<div class="form-group">
                    	<label for="csv_file">Upload Products CSV</label>
                    	<input type="file" name="csv_file" id="csv_file" class="form-control" required accept=".csv">
                	</div>
                	<button type="submit" class="btn btn-primary">Upload</button>
          		</form>
        	</div>
            <a class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Downlad Sample File"  href="{{asset('backend/excel/Sample.csv')}}" download="Sample.csv" >Download Sample Excel File</a>
        </div>
    </div>
@endsection


