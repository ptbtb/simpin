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
			<li class="breadcrumb-item active">Jenis Anggota</li>
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
        @can('add jenis anggota')
            <div class="card-header text-right">
                <a href="{{ route('jenis-anggota-create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Jenis Anggota</a>
            </div>
        @endcan
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 15%">Kode Jenis Anggota</th>
                        <th>Nama Jenis Anggota</th>
                        <th style="width: 5%">Prefix</th>
                        <th style="width: 15%">Created By</th>
                        <th style="width: 15%">Updated By</th>
                        @if (auth()->user()->can('edit jenis anggota') || auth()->user()->can('delete jenis anggota'))
                            <th style="width: 15%">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jenisAnggotas as $jenisAnggota)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jenisAnggota->code_jenis_anggota }}</td>
                            <td>{{ $jenisAnggota->nama_jenis_anggota }}</td>
                            <td>
                                @if ($jenisAnggota->prefix && $jenisAnggota->prefix != '')
                                    {{ $jenisAnggota->prefix }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $jenisAnggota->createdBy->name }}
                            </td>
                            <td>
                                {{ $jenisAnggota->updatedBy->name }}
                            </td>
                            @if (auth()->user()->can('edit jenis anggota') || auth()->user()->can('delete jenis anggota'))
                                <td>
                                    @can('edit jenis anggota')
                                        <a href="{{ route('jenis-anggota-edit', ['id'=>$jenisAnggota->id_jenis_anggota]) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    @endcan
                                    @can('delete jenis anggota')
                                        <form action="{{ route('jenis-anggota-delete', ['id'=>$jenisAnggota->id_jenis_anggota]) }}" method="post" style="display: inline">
                                            <button  class="btn btn-sm btn-danger" type="submit" value="Delete">
                                                <i class="fa fa-trash"></i> Hapus
                                            </button>
                                            @method('delete')
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