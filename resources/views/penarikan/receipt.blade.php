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
			<li class="breadcrumb-item"><a href="{{ route('penarikan-create') }}">Penarikan</a></li>
			<li class="breadcrumb-item active">Receipt</li>
		</ol>
	</div>
</div>
@endsection

@section('content')
<div class="card" style="width: 30rem;">
    <div class="card-body">
        <div class="row">
            <div class="col-12 text-center mb-3">
                <h6>Bukti Pengambilan Tunai</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-12">Tanggal: {{ $penarikan->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="row">
            
            <div class="col-12">Waktu: {{ $penarikan->created_at->format('H:i:s') }}</div>
        </div>
        <div class="row">
            
            <div class="col-12">No. Record: {{ $penarikan->kode_ambil.$penarikan->created_at->format('dmY') }}</div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">Penarikan : Rp. {{ number_format($penarikan->besar_ambil,0,",",".") }}</div>
        </div>
        <div class="row">
            <div class="col-md-12">Sisa Saldo : Rp. {{ number_format($penarikan->tabungan->besar_tabungan,0,",",".") }}</div>
        </div>
        <div class="row">
            <div class="col-12 text-center mt-3">
                <p>Terima Kasih</p>
            </div>
        </div>
        <div class="text-right">
            <a href="{{ route('penarikan-receipt-download', ['id'=>$penarikan->kode_ambil]) }}" class="btn btn-success btn-sm"><i class="fas fa-download"></i> Download Receipt</a>
        </div>
    </div>
</div>
@endsection