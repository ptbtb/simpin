@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h4>{{ $title }}</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="">Pengurus</a></li>
                <li class="breadcrumb-item"><a href="">Tambah Pengurus</a></li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('pengurus.update', $pengurus->id) }}" method="post">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" required value="{{ $pengurus->nama }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="jabatan">Jabatan</label>
                        <select name="jabatan" id="jabatan" class="form-control">
                            <option value="">Pilih Satu</option>
                            @foreach (ARRAY_JABATAN_PENGURUS as $key => $value)
                                <option value="{{ $key }}" {{ ($key == $pengurus->jabatan)? 'selected':'' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="start">Start</label>
                        <input type="text" name="start" id="start" class="form-control" required value="{{ $pengurus->start->format('d-m-Y') }}" autocomplete="off">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="expired">Expired</label>
                        <input type="text" name="expired" id="expired" class="form-control" required value="{{ $pengurus->expired->format('d-m-Y') }}" autocomplete="off">
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-sm btn-success">
                            <i class="fa fa-save"></i> Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script>
    $('#start').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'dd-mm-yyyy'
    });
    $('#expired').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'dd-mm-yyyy'
    });
</script>
@stop
