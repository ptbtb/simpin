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
			<li class="breadcrumb-item active">Bank</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        @can('add Bank')
            <div class="card-header text-right">
                <a href="{{ route('bank.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Bank</a>
            </div>
        @endcan
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Nama Bank</th>
                        <th>Kode Bank</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kode }}</td>
                            @if (auth()->user()->can('edit bank') || auth()->user()->can('delete bank'))
                                <td>
                                    @can('edit Bank')
                                        <a href="{{ route('bank.edit', ['id'=>$item->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    @endcan
                                    @can('delete Bank')
                                        <form action="{{ route('bank.delete', ['id'=>$item->id]) }}" method="post" style="display: inline">
                                            <button  class="btn btn-sm btn-danger" type="submit" value="Delete">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                            @method('post')
                                            @csrf
                                        </form>
                                    @endcan
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.table').DataTable();
    </script>
@endsection