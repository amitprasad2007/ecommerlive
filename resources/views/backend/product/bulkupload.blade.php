@extends('backend.layouts.master')

@section('main-content')
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="{{ route('product.bulkUpload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="csv_file">Upload Products CSV</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
@endsection


