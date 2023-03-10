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
                <li class="breadcrumb-item"><a href="{{ route('active-saldo-awal.index') }}">Active Saldo Awal</a></li>
                <li class="breadcrumb-item"><a href="">Edit</a></li>
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
            <form action="{{ route('active-saldo-awal.update', $activeSaldoAwal->id) }}" method="post">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="tgl_saldo">Tanggal Saldo</label>
                        <input type="text" name="tgl_saldo" id="tgl_saldo" class="form-control" required value="{{ $activeSaldoAwal->tgl_saldo->format('d-m-Y') }}" autocomplete="off">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">Pilih Satu</option>
                            <option value="1" {{ ($activeSaldoAwal->status == 1)? 'selected':'' }}>Active</option>
                            <option value="0" {{ ($activeSaldoAwal->status == 0)? 'selected':'' }}>Not Active</option>
                        </select>
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
    $('#tgl_saldo').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'dd-mm-yyyy'
    });
</script>
@stop
