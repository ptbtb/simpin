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
			<li class="breadcrumb-item active">Laporan Pinjaman</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('pinjaman-report') }}">
                    <div class="col-md-4">
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="yyyy" id="period" name="period" value="{{ Carbon\Carbon::createFromFormat('Y', $request->period)->format('Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                        {{-- <a href="{{ route('neraca-download-pdf', ['period' => $request->period]) }}" class="btn btn-info"><i class="fas fa-print"></i> Print</a> --}}
                        <a href="{{ route('laporan-pinjaman-download-excel', ['period' => $request->period]) }}" class="btn btn-success"><i class="fa fa-download"></i> Excel</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped" style="text-align: center">
                    <thead>
                        <tr>
                            <th rowspan="2" style="vertical-align: middle">Bulan</th>
                            <th colspan="3">JAPAN {{ $request->period }}</th>
                            <th colspan="3">JAPEN {{ $request->period }}</th>
                        </tr>
                        <tr>
                            <th>TRX</th>
                            <th>APPROVED</th>
                            <th>DITERIMA</th>
                            <th>TRX</th>
                            <th>APPROVED</th>
                            <th>DITERIMA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ Carbon\Carbon::createFromFormat('m', $loop->iteration)->format('M') }}</td>
                                <td>{{ number_format($report['trxJapen'],0,",",".") }}</td>
                                <td>{{ number_format($report['japenApproved'],0,",",".") }}</td>
                                <td>{{ number_format($report['japenDiterima'],0,",",".") }}</td>
                                <td>{{ number_format($report['trxJapan'],0,",",".") }}</td>
                                <td>{{ number_format($report['japanApproved'],0,",",".") }}</td>
                                <td>{{ number_format($report['japanDiterima'],0,",",".") }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight:bold">
                            <td>Total</td>
                            <td>{{ number_format($totalJapenTrx,0,",",".") }}</td>
                            <td>{{ number_format($totalJapenApproved,0,",",".") }}</td>
                            <td>{{ number_format($totalJapenDiterima,0,",",".") }}</td>
                            <td>{{ number_format($totalJapanTrx,0,",",".") }}</td>
                            <td>{{ number_format($totalJapanApproved,0,",",".") }}</td>
                            <td>{{ number_format($totalJapanDiterima,0,",",".") }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function ()
        {

            $('.datepicker').datepicker({
                format: "yyyy",
                viewMode: "years", 
                minViewMode: "years",
                autoclose: true
            });

            $('input.datepicker').bind('keyup keydown keypress', function (evt) {
                return false;
            });
        });
    </script>
@endsection
