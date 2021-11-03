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
                        <button type="submit" name="search" value="search" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                        <a href="{{ route('neraca-download-pdf', ['period' => $request->period]) }}" class="btn btn-info"><i class="fas fa-print"></i> Print</a>
                        <a href="{{ route('neraca-download-excel', ['period' => $request->period]) }}" class="btn btn-success"><i class="fa fa-download"></i> Excel</a>
                    </div>
                </form>
            </div>
        </div>

        @if ($request->search)   
            <div class="card-body row">
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Aktiva</h5>
                    <table class="table table-striped table-aktiva">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Rek</th>
                                <th style="width: 30%">Nama Rekening</th>
                                <th>Bulan ini</th>
                                <th >Bulan lalu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>I</td>
                                <td>AKTIVA LANCAR</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($aktivalancar as $item)
                                <tr>
                                    <td></td>
                                    <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                    <td><a href="{{ route('jurnal-list',['code'=>substr($item['code']->CODE, 0, 3) ,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                    <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item['saldoLastMonth'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jumlah Aktiva Lancar</td>
                                <td class="text-right">{{ number_format($aktivalancar->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($aktivalancar->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>II</td>
                                <td>AKTIVA TETAP</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($aktivatetap as $item)
                                <tr>
                                    <td></td>
                                    <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>substr($item['code']->CODE, 0, 3) ,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                    <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item['saldoLastMonth'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jumlah Aktiva Tetap</td>
                                <td class="text-right">{{ number_format($aktivatetap->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($aktivatetap->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>TOTAL AKTIVA</td>
                                <td class="text-right">{{ number_format($aktivatetap->sum('saldo')+$aktivalancar->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($aktivatetap->sum('saldoLastMonth')+$aktivalancar->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 table-responsive">
                    <h5 class="text-center">Passiva</h5>
                    <table class="table table-striped table-passiva">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Rek</th>
                                <th style="width: 30%">Nama Rekening</th>
                                <th >Bulan ini</th>
                                <th >Bulan lalu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>III</td>
                                <td>KEWAJIBAN LANCAR</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($kewajibanlancar as $item)
                                <tr>
                                    <td></td>
                                    <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>substr($item['code']->CODE, 0, 3) ,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                    <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item['saldoLastMonth'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jumlah Kewajiban Lancar</td>
                                <td class="text-right">{{ number_format($kewajibanlancar->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($kewajibanlancar->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>IV</td>
                                <td>KEWAJIBAN JANGKA PANJANG</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($kewajibanjangkapanjang as $item)
                                <tr>
                                    <td></td>
                                    <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>substr($item['code']->CODE, 0, 3) ,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                    <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item['saldoLastMonth'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jumlah Kewajiban Jangka Panjang</td>
                                <td class="text-right">{{ number_format($kewajibanjangkapanjang->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($kewajibanjangkapanjang->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>V</td>
                                <td>KEKAYAAN BERSIH</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($kekayaanbersih as $item)
                                <tr>
                                    <td></td>
                                    <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>
                                    @if (substr($item['code']->CODE, 0, 3)!=='607')
                                    <a href="{{ route('jurnal-list',['code'=>substr($item['code']->CODE, 0, 3) ,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->subYear()->endOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a>
                                    @else
                                    {{ $item['code']->NAMA_TRANSAKSI }}
                                    @endif
                                    </td>
                                    <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                    <td class="text-right">{{ number_format($item['saldoLastMonth'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td>Jumlah Kekayaan Bersih</td>
                                <td class="text-right">{{ number_format($kekayaanbersih->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($kekayaanbersih->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>TOTAL PASIVA</td>
                                <td class="text-right">{{ number_format($kekayaanbersih->sum('saldo')+$kewajibanjangkapanjang->sum('saldo')+$kewajibanlancar->sum('saldo'), 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($kekayaanbersih->sum('saldoLastMonth')+$kewajibanjangkapanjang->sum('saldoLastMonth')+$kewajibanlancar->sum('saldoLastMonth'), 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
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
