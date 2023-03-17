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
                <li class="breadcrumb-item"><a href="">Simpanan</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label>Filter</label>
        </div>
        <div class="card-body">
            <form  action="{{ route('filter-laporan-simpanan') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-2 form-group">
                        <label>Tanggal</label>
                        <input id="tahun" type="text" name="tahun" class="form-control datepicker" placeholder="yyyy-mm-dd" value="{{$request->tahun }}">
                    </div>
                    <div class="col-md-1 form-group" style="margin-top: 26px">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if ($request->tahun)
        <div class="card">
            <div class="card-body table-responsive">
                <div class="text-right mb-2">
                    <a target="_blank" href="{{ route('laporan-simpanan-excel', ['tahun' => $request->tahun]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Laporan</a>
                    <a target="_blank" href="{{ route('laporan-simpanan-excel', ['tahun' => $request->tahun, 'pdf' => 1]) }}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> Download Laporan PDF</a>
                </div>
                @include('simpanan.laporan-view')
            </div>
        </div>
    @endif
@stop

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $('#tahun').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
