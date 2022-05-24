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
			<li class="breadcrumb-item active">Laba Rugi</li>
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
@php
                            $saldoUntilBeforeMonthPend = 0;
                            $saldoPend = 0;
                            $saldoUntilMonthPend =0;
                        @endphp
    <div class="card">
        <div class="card-header">
            <div class="col-md-12">
                <form id="myForm" role="form" method="GET" enctype="multipart/form-data" action="{{ route('laba-rugi-list') }}">
                    {{-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> --}}
                    
                    <div class="col-md-6">
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="period" name="period" value="{{ Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 text-center" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-primary" name="search" value="search"><span class="fa fa-search"></span> Search</button>
                         <a href="{{ route('laba-rugi-download-excel') }}" class="btn btn-success"><i class="fa fa-download"></i> Download Excel</a> 
                         <a href="{{ route('laba-rugi-download-pdf') }}" class="btn btn-info"><i class="fa fa-download"></i> Download PDF</a> 
                    </div>
                </form>
            </div>
        </div>

        @if ($request->search)    
            <div class="card-body row">
                <div class="col-md-12 table-responsive">
                    <h5 class="text-center">Laba Rugi</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2">Rek</th>
                                <th rowspan="2" style="width: 20%">Nama</th>
                                <th rowspan="2" >Anggaran Tahun {{ $request->year }}</th>
                                <th rowspan="2" >Anggaran Triwulan</th>
                                <th rowspan="2" >S/D Bulan Lalu</th>
                                <th rowspan="2" >Bulan Ini</th>
                                <th rowspan="2" >S/D Bulan Ini</th>
                                <th colspan="2" >TREND</th>
                            </tr>
                            <tr>
                                <th style="width: 20%">7/3</th>
                                <th style="width: 20%">7/4</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>I</td>
                                <td>{{ substr($pendapatan[0]['code']->CODE, 0, 6)}}</td>
                                <td colspan="8"><b>PENDAPATAN</b></td>
                            </tr>
                            @foreach ($pendapatan as $item)
                            <tr>
                                <td></td>
                                <td class="text-right">{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'from'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->startOfYear()->format('d-m-Y'),'to'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->endOfMonth()->format('d-m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPend += $item['saldoUntilBeforeMonth'];
                                $saldoPend += $item['saldo'];
                                $saldoUntilMonthPend += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Pendapatan</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthPend, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoPend, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthPend, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>II</td>
                                <td>{{ substr($hpp[0]['code']->CODE, 0, 6)}}</td>
                                <td colspan="8"><b>HPP</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthHpp = 0;
                                $saldoHpp = 0;
                                $saldoUntilMonthHpp = 0;
                            @endphp
                            @foreach ($hpp as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthHpp += $item['saldoUntilBeforeMonth'];
                                $saldoHpp += $item['saldo'];
                                $saldoUntilMonthHpp += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Hpp</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthHpp, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoHpp, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthHpp, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                            $saldoUntilBeforeMonthHasil=$saldoUntilBeforeMonthPend-$saldoUntilBeforeMonthHpp;
                            $saldoHasil = $saldoPend-$saldoHpp;
                            $saldoUntilMonthHasil=$saldoUntilMonthPend-$saldoUntilMonthHpp;
                            @endphp
                            <tr>
                                <td>III</td>
                                <td></td>
                                <td><b>Hasil Usaha Bruto=(I-II)</b></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthHasil, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoHasil, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthHasil, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>IV</td>
                                <td></td>
                                <td colspan="8"><b>BEBAN USAHA</b></td>
                            </tr>
                            <tr>
                                <td>A</td>
                                <td>{{ substr($biayapegawai[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA PEGAWAI</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPegawai = 0;
                                $saldoPegawai = 0;
                                $saldoUntilMonthPegawai = 0;
                            @endphp
                            @foreach ($biayapegawai as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPegawai += $item['saldoUntilBeforeMonth'];
                                $saldoPegawai += $item['saldo'];
                                $saldoUntilMonthPegawai += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Pegawai</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthPegawai, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoPegawai, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthPegawai, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>B</td>
                                <td>{{ substr($biayaoperasional[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA OPERASIONAL</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthOp = 0;
                                $saldoOp = 0;
                                $saldoUntilMonthOp = 0;
                            @endphp
                            @foreach ($biayaoperasional as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthOp += $item['saldoUntilBeforeMonth'];
                                $saldoOp += $item['saldo'];
                                $saldoUntilMonthOp += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Operasional</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthOp, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoOp, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthOp, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>C</td>
                                <td>{{ substr($biayaperawatan[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA PERAWATAN</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPrwt = 0;
                                $saldoPrwt = 0;
                                $saldoUntilMonthPrwt = 0;
                            @endphp
                            @foreach ($biayaperawatan as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPrwt += $item['saldoUntilBeforeMonth'];
                                $saldoPrwt += $item['saldo'];
                                $saldoUntilMonthPrwt += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Perawatan</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthPrwt, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoPrwt, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthPrwt, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>D</td>
                                <td>{{ substr($biayapenyusutan[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA PENYUSUTAN</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPnyust = 0;
                                $saldoPnyust = 0;
                                $saldoUntilMonthPnyust = 0;
                            @endphp
                            @foreach ($biayapenyusutan as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPnyust += $item['saldoUntilBeforeMonth'];
                                $saldoPnyust += $item['saldo'];
                                $saldoUntilMonthPnyust += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Penyusutan</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthPnyust, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoPnyust, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthPnyust, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPnyish = 0;
                                $saldoPnyish = 0;
                                $saldoUntilMonthPnyish = 0;
                            @endphp
                            @if ($biayapenyisihan->count()>0)
                            <tr>
                                <td>E</td>
                                <td>{{ substr($biayapenyisihan[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA Penyisihan</b></td>
                            </tr>
                            
                            @foreach ($biayapenyusutan as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthPnyish += $item['saldoUntilBeforeMonth'];
                                $saldoPnyish += $item['saldo'];
                                $saldoUntilMonthPnyish += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Penyisihan</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthPnyish, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoPnyish, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthPnyish, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endif
                            <tr>
                                <td>F</td>
                                <td>{{ substr($biayaadminum[0]['code']->CODE, 0, 6) }}</td>
                                <td colspan="8"><b>BIAYA ADMINISTRASI DAN UMUM</b></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthAdm = 0;
                                $saldoAdm = 0;
                                $saldoUntilMonthAdm = 0;
                            @endphp
                            @foreach ($biayaadminum as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 7, 3) }}</td>
                                <td><a href="{{ route('jurnal-list',['code'=>$item['code']->CODE,'period'=> Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y')]) }}" target="_blank">{{ $item['code']->NAMA_TRANSAKSI }}</a></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthAdm += $item['saldoUntilBeforeMonth'];
                                $saldoAdm += $item['saldo'];
                                $saldoUntilMonthAdm += $item['saldoUntilMonth'];
                            @endphp
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;">Jumlah Biaya Administrasi dan Umum</td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthAdm, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoAdm, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthAdm, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                                $saldoUntilBeforeMonthTotalBiaya = $saldoUntilBeforeMonthAdm + $saldoUntilBeforeMonthPegawai + $saldoUntilBeforeMonthOp + $saldoUntilBeforeMonthPrwt + $saldoUntilBeforeMonthPnyust+$saldoUntilBeforeMonthPnyish;
                                $saldoTotalBiaya = $saldoAdm + $saldoPegawai + $saldoOp + $saldoPrwt + $saldoPnyust+$saldoPnyish;
                                $saldoUntilMonthTotalBiaya = $saldoUntilMonthAdm + $saldoUntilMonthPegawai + $saldoUntilMonthOp + $saldoUntilMonthPrwt + $saldoUntilMonthPnyust+$saldoUntilMonthPnyish;
                            @endphp
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="text-align:right;"><b>Jumlah Biaya (IV)</b></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthTotalBiaya, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoTotalBiaya, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthTotalBiaya, 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>V</td>
                                <td></td>
                                <td style="text-align:right;"><b>SHU Operasional (III-IV)</b></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">{{ number_format($saldoUntilBeforeMonthHasil - $saldoUntilBeforeMonthTotalBiaya , 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoHasil - $saldoTotalBiaya, 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($saldoUntilMonthHasil - $saldoUntilMonthTotalBiaya , 0, ',', '.') }}</td>
                                <td></td>
                                <td></td>
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
