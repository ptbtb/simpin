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
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label>Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('filter-laporan-simpanan') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            <option value="">Select Year</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ ($request->tahun == $year)? 'selected':'' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 text-center">
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
@stop
