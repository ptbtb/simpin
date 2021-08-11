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
                        <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                         <a href="{{ route('laba-rugi-download-excel') }}" class="btn btn-success"><i class="fa fa-download"></i> Download Excel</a> 
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body row">
            <div class="col-md-12 table-responsive">
                <h5 class="text-center">Laba Rugi</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Rek</th>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2" style="width: 20%">Anggaran Tahun {{ $request->year }}</th>
                            <th rowspan="2" style="width: 20%">Anggaran Triwulan</th>
                            <th rowspan="2" style="width: 20%">S/D Bulan Lalu</th>
                            <th rowspan="2" style="width: 20%">Bulan Ini</th>
                            <th rowspan="2" style="width: 20%">S/D Bulan Ini</th>
                            <th colspan="2" style="width: 20%">TREND</th>
                        </tr>
                        <tr>
                            <th style="width: 20%">7/3</th>
                            <th style="width: 20%">7/4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>PENDAPATAN</b></td>
                        </tr>
                        @foreach ($pendapatan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Pendapatan</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthPend, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoPend, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthPend, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>HPP</b></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthHpp = 0;
                            $saldoHpp = 0;
                            $saldoUntilMonthHpp = 0;
                        @endphp
                        @foreach ($hpp as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Pendapatan</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthHpp, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoHpp, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthHpp, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA</b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PEGAWAI</b></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPegawai = 0;
                            $saldoPegawai = 0;
                            $saldoUntilMonthPegawai = 0;
                        @endphp
                        @foreach ($biayapegawai as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Biaya Pegawai</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthPegawai, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoPegawai, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthPegawai, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA OPERASIONAL</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthOp = 0;
                            $saldoOp = 0;
                            $saldoUntilMonthOp = 0;
                        @endphp
                        @foreach ($biayaoperasional as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Biaya Operasional</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthOp, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoOp, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthOp, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PERAWATAN</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthPrwt = 0;
                            $saldoPrwt = 0;
                            $saldoUntilMonthPrwt = 0;
                        @endphp
                        @foreach ($biayaperawatan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Biaya Perawatan</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthPrwt, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoPrwt, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthPrwt, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PENYUSUTAN</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthPnyust = 0;
                            $saldoPnyust = 0;
                            $saldoUntilMonthPnyust = 0;
                        @endphp
                         @foreach ($biayapenyusutan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Biaya Penyusutan</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthPnyust, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoPnyust, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthPnyust, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA ADMINISTRASI DAN UMUM</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthAdm = 0;
                            $saldoAdm = 0;
                            $saldoUntilMonthAdm = 0;
                        @endphp
                        @foreach ($biayaadminum as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
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
                            <td style="text-align:right;">Jumlah Biaya Administrasi dan Umum</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthAdm, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoAdm, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthAdm, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthTotalBiaya = $saldoUntilBeforeMonthAdm + $saldoUntilBeforeMonthPegawai + $saldoUntilBeforeMonthOp + $saldoUntilBeforeMonthPrwt + $saldoUntilBeforeMonthPnyust;
                            $saldoTotalBiaya = $saldoAdm + $saldoPegawai + $saldoOp + $saldoPrwt + $saldoPnyust;
                            $saldoUntilMonthTotalBiaya = $saldoUntilMonthAdm + $saldoUntilMonthPegawai + $saldoUntilMonthOp + $saldoUntilMonthPrwt + $saldoUntilMonthPnyust;
                        @endphp
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthTotalBiaya, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoTotalBiaya, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthTotalBiaya, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Laba/Rugi sebelum luar usaha</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format( $saldoUntilBeforeMonthTotalBiaya - $saldoUntilBeforeMonthPend, 0, ',', '.') }}</td>
                            <td>{{ number_format( $saldoTotalBiaya - $saldoPend, 0, ',', '.') }}</td>
                            <td>{{ number_format( $saldoUntilMonthTotalBiaya - $saldoUntilMonthPend, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>Pend dan Biaya di Luar Usaha</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthLu = 0;
                            $saldoLu = 0;
                            $saldoUntilMonthLu = 0;
                        @endphp
                        @foreach ($luarusaha as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($item['saldoUntilBeforeMonth'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldo'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['saldoUntilMonth'], 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthLu += $item['saldoUntilBeforeMonth'];
                            $saldoLu += $item['saldo'];
                            $saldoUntilMonthLu += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Sls Pend dan Biaya di Luar Usaha</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthLu, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoLu, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthLu, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Laba/Rugi setelah luar usaha</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoUntilBeforeMonthLu + $saldoUntilBeforeMonthTotalBiaya - $saldoUntilBeforeMonthPend, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoLu + $saldoTotalBiaya - $saldoPend, 0, ',', '.') }}</td>
                            <td>{{ number_format($saldoUntilMonthLu + $saldoUntilMonthTotalBiaya - $saldoUntilMonthPend, 0, ',', '.') }}</td>
                            <td></td>
                            <td></td>
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
