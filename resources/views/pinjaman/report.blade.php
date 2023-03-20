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
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('pinjaman-report') }}">
                    <div class="col-md-2 form-group">
                        <label>Tanggal</label>
                        <input id="tahun" type="text" name="tahun" class="form-control datepicker" placeholder="yyyy-mm-dd" value="{{$request->tahun }}">
                    </div>
                    <div class="col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                        {{-- <a href="{{ route('neraca-download-pdf', ['period' => $request->period]) }}" class="btn btn-info"><i class="fas fa-print"></i> Print</a> --}}
                        <a href="{{ route('laporan-pinjaman-download-excel', ['tahun' => $request->tahun]) }}" class="btn btn-success"><i class="fa fa-download"></i> Excel</a>
                        <a href="{{ route('laporan-pinjaman-download-excel', ['tahun' => $request->tahun, 'pdf' => 1]) }}" class="btn btn-info"><i class="fa fa-download"></i> PDF</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-12 table-responsive">
{{--                <table class="table table-striped" style="text-align: center">--}}
{{--                    <thead>--}}
{{--                        <tr>--}}
{{--                            <th rowspan="2" style="vertical-align: middle">Bulan</th>--}}
{{--                            <th colspan="3">JAPAN {{ $request->tahun }}</th>--}}
{{--                            <th colspan="3">JAPEN {{ $request->tahun }}</th>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>TRX</th>--}}
{{--                            <th>APPROVED</th>--}}
{{--                            <th>DITERIMA</th>--}}
{{--                            <th>TRX</th>--}}
{{--                            <th>APPROVED</th>--}}
{{--                            <th>DITERIMA</th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        @foreach ($reports as $report)--}}
{{--                            <tr>--}}
{{--                                <td>{{ Carbon\Carbon::createFromFormat('m', $loop->iteration)->format('M') }}</td>--}}
{{--                                <td>{{ number_format($report['trxJapen'],0,",",".") }}</td>--}}
{{--                                <td>{{ number_format($report['japenApproved'],0,",",".") }}</td>--}}
{{--                                <td>{{ number_format($report['japenDiterima'],0,",",".") }}</td>--}}
{{--                                <td>{{ number_format($report['trxJapan'],0,",",".") }}</td>--}}
{{--                                <td>{{ number_format($report['japanApproved'],0,",",".") }}</td>--}}
{{--                                <td>{{ number_format($report['japanDiterima'],0,",",".") }}</td>--}}
{{--                            </tr>--}}
{{--                        @endforeach--}}
{{--                        <tr style="font-weight:bold">--}}
{{--                            <td>Total</td>--}}
{{--                            <td>{{ number_format($totalJapenTrx,0,",",".") }}</td>--}}
{{--                            <td>{{ number_format($totalJapenApproved,0,",",".") }}</td>--}}
{{--                            <td>{{ number_format($totalJapenDiterima,0,",",".") }}</td>--}}
{{--                            <td>{{ number_format($totalJapanTrx,0,",",".") }}</td>--}}
{{--                            <td>{{ number_format($totalJapanApproved,0,",",".") }}</td>--}}
{{--                            <td>{{ number_format($totalJapanDiterima,0,",",".") }}</td>--}}
{{--                        </tr>--}}
{{--                    </tbody>--}}
{{--                </table>--}}
            </div>
        </div>
    </div>
@endsection

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
