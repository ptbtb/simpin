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
			<li class="breadcrumb-item active">Neraca</li>
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
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('neraca-list') }}">
                    {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                    
                    <div class="col-md-6">
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="period" name="period" value="{{ Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                        <a href="{{ route('neraca-download-pdf', ['period' => $request->period]) }}" class="btn btn-info"><i class="fas fa-print"></i> Print</a>
                        {{-- <a href="{{ route('neraca-download-excel') }}" class="btn btn-success"><i class="fa fa-download"></i> Download Excel</a> --}}
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-6 table-responsive">
                <h5 class="text-center">Aktiva</h5>
                <table class="table table-striped table-aktiva">
                    <thead>
                        <tr>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th style="width: 30%">Bulan ini</th>
                            <th style="width: 30%">Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($aktivas as $aktiva)
                            <tr>
                                <td>{{ substr($aktiva['code']->CODE, 0, 3) }}</td>
                                <td>{{ $aktiva['code']->NAMA_TRANSAKSI }}</td>
                                <td>Rp. {{ number_format($aktiva['saldo'], 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($aktiva['saldoLastMonth'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>TOTAL</td>
                            <td>Rp. {{ number_format($aktivas->sum('saldo'), 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($aktivas->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-6 table-responsive">
                <h5 class="text-center">Passiva</h5>
                <table class="table table-striped table-passiva">
                    <thead>
                        <tr>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th style="width: 30%">Bulan ini</th>
                            <th style="width: 30%">Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($passivas as $passiva)
                            <tr>
                                <td>{{ substr($passiva['code']->CODE, 0, 3) }}</td>
                                <td>{{ $passiva['code']->NAMA_TRANSAKSI }}</td>
                                <td>Rp. {{ number_format($passiva['saldo'], 0, ',', '.') }}</td>
                                <td>Rp. {{ number_format($passiva['saldoLastMonth'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td>TOTAL</td>
                            <td>Rp. {{ number_format($passivas->sum('saldo'), 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($passivas->sum('saldoLastMonth'), 0, ',', '.') }}</td>
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

            $("#period").change(function(){
                console.log($('#period').val());
                console.log({{ $request->period }});
            });

            $('.datepicker').datepicker({
                format: "mm-yyyy",
                viewMode: "months", 
                minViewMode: "months"
            });

            $('input.datepicker').bind('keyup keydown keypress', function (evt) {
                return false;
            });
        });
    </script>
@endsection
