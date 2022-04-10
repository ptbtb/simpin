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
                <li class="breadcrumb-item"><a href="{{ route('sumber-dana.index') }}">Sumber Dana</a></li>
                <li class="breadcrumb-item active">Tambah Sumber Dana</li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sumber-dana.update', [$sumberDana->id]) }}" method="post">
                @method('put')
                @csrf
                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Nama" required value="{{ $sumberDana->name }}">
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label for="code">Code</label>
                        <select multiple class="form-control" name="code[]" id="code">
                            @foreach ($sumberDana->codes as $code)
                                <option value="{{ $code->id }}" selected>
                                    {{ $code->NAMA_TRANSAKSI }} ({{ $code->CODE }})
                                </option>
                            @endforeach
                            @foreach ($codes as $code)
                                <option value="{{ $code->id }}">
                                    {{ $code->NAMA_TRANSAKSI }} ({{ $code->CODE }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('#code').select2({
            placeholder: "Choose Code"
        });
    </script>
@stop
