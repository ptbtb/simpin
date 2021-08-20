@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bank.list') }}">Bank</a></li>
			<li class="breadcrumb-item active">Edit Bank</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .form-control{
            font-size: .8rem;
            height: calc(2rem + 2px);
        }
    </style>
@endsection

@section('plugins.Datatables', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('bank.edit', ['id'=>$data->id]) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label>Nama Bank</label>
                            <input type="text" name="nama" value="{{$data->nama}}" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Kode Bank</label>
                            <input type="text" name="kode" value="{{$data->kode}}" class="form-control" required>
                        </div>
                        
                        <button class="btn btn-sm btn-success"><i class="fa fa-save"></i> Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection