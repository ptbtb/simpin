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
            <li class="breadcrumb-item"><a href="{{ route('jurnal-umum-list') }}">Jurnal Umum</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jurnal-umum-list') }}">List Jurnal Umum</a></li>
			<li class="breadcrumb-item active">Detail Jurnal Umum</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .box-custom{
            border: 1px solid black;
            border-radius: 0;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h6 style="font-weight: 600">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tr>
                    <td style="width:20%">Tanggal Transaksi</td>
                    <td style="width:5%">:</td>
                    <td>
                        @if ($jurnalUmum->tgl_transaksi)
                            {{ $jurnalUmum->tgl_transaksi->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Deskripsi</td>
                    <td>:</td>
                    <td>{{ $jurnalUmum->deskripsi or '-'}}</td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>
                        @foreach ($jurnalUmum->jurnalUmumLampirans as $jurnalUmumLampiran)
                            <a class="btn btn-warning btn-sm" href="{{ asset($jurnalUmumLampiran->lampiran) }}" target="_blank"><i class="fa fa-file"></i></a>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="table-responsive col-md-6">
                <table class="table table-striped">
                    <tr>
                        <td colspan="4" class="text-center"><b>Debet</b></td>
                    </tr>
                    <tr>
                        <td>No</td>
                        <td>Kode</td>
                        <td>Nama</td>
                        <td>Nominal</td>
                    </tr>
                    @foreach ($itemDebets as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->code->CODE }}</td>
                            <td>{{ $item->code->NAMA_TRANSAKSI }}</td>
                            <td>{{ $item->nominal }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td><b>Total</b></td>
                        <td>{{ $jurnalUmum->total_nominal_debet }}</td>
                    </tr>
                </table>
            </div>
            <div class="table-responsive col-md-6">
                <table class="table table-striped">
                    <tr>
                        <td colspan="4" class="text-center"><b>Kredit</b></td>
                    </tr>
                    <tr>
                        <td>No</td>
                        <td>Kode</td>
                        <td>Nama</td>
                        <td>Nominal</td>
                    </tr>
                    @foreach ($itemCredits as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->code->CODE }}</td>
                            <td>{{ $item->code->NAMA_TRANSAKSI }}</td>
                            <td>{{ $item->nominal }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"></td>
                        <td><b>Total</b></td>
                        <td>{{ $jurnalUmum->total_nominal_kredit }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
    <script>
    </script>
@endsection