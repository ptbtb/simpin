@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">Import</a></li>
			<li class="breadcrumb-item active">Import SHU</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <label>Upload Excel File</label>
            </div>
            <div class="card-body">
                <form action="{{ route('list-shu.storeImport') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <a href="{{ asset('template-excel/template shu.xlsx') }}">Download Template Excel Here</a>
                    </div>
                    <div class="form-group ">
                        <label>Choose File</label><br>
                        <input type="file" name="file" id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"><br>
                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
@stop
