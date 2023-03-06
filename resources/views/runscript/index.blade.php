@extends('adminlte::page')

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
                <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
        </div>
    </div>
@endsection

{{-- @section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true) --}}

@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
    <style>
        .btn-sm {
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <form action="{{ route('script-submit') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="yyyy-mm" id="periode" name="periode" autocomplete="off" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <label>Run Script</label>
                        <select class="form-control" name="script" id="script" required>
                            <option value="">--Choose Script--</option>
                            <option value="1">Jurnal Balance Resolver</option>
                            <option value="2">Jurnal Penarikan Generator</option>
                            <option value="3">Jurnal Umum Generator</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mt-2 form-group text-right">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-play"></i> Run</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $('.datepicker').datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });

        $('input.datepicker').bind('keyup keydown keypress', function(evt) {
            return true;
        });
    </script>
@endsection
