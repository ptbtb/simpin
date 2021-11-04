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
                <li class="breadcrumb-item"><a href="">Arus Kas</a></li>
                <li class="breadcrumb-item active">Laporan</li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label>Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('filter.laporan.arus-kas') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label>Dari</label>
                        <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-12 text-center mt-1">
                        <button type="submit" class="btn btn-sm btn-success" name="search" value="search"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if (($dataPengeluaran || $dataPenerimaan) && $request->search)
        <div class="card">
            <div class="card-body table-responsive">
                <div class="text-right mb-2">
                    <a target="_blank" href="{{ route('excel.laporan.arus-kas', ['period' => $request->period]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Laporan</a>
                </div>
                <div class="table-responsive">
                    @include('arus_kas.laporan-view')
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function ()
        {
            $('.datepicker').datepicker({
                format: "dd-mm-yyyy",
                orientation: "bottom",
                autoclose: true,
            });
        });
    </script>
@stop
