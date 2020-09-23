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
            <li class="breadcrumb-item"><a href="{{ route('jenis-anggota-list') }}">Jenis Anggota</a></li>
			<li class="breadcrumb-item active">Edit Jenis Anggota</li>
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
            <form action="{{ route('jenis-anggota-edit', ['id' => $jenisAnggota->id_jenis_anggota]) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label>Kode Jenis Anggota</label>
                            <input type="text" name="kode_jenis_anggota" class="form-control" value="{{ $jenisAnggota->code_jenis_anggota }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Jenis Anggota</label>
                            <input type="text" name="nama_jenis_anggota" class="form-control" value="{{ $jenisAnggota->nama_jenis_anggota }}" required>
                        </div>
                        <div class="form-group">
                            <label>Prefix</label>
                            <input type="text" name="prefix" class="form-control" value="{{ $jenisAnggota->prefix }}" required>
                        </div>
                        <button class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection