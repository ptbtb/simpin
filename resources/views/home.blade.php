@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1>Dashboard</h1>
@stop

@section('css')
    <style>
        .link-dashboard{
            color: white;
        }

        .link-dashboard:hover{
            color: azure;
        }
    </style>
@endsection

@section('content')
<div class="col-lg-12">
    <div class="form-panel">
        <div class="row">
            @if ($role->id == ROLE_ANGGOTA)
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner" style="min-height: 123px;">
                            <h3 style="font-size: 1.5rem">Rp. {{ number_format($saldo->jumlah,0,",",".") }}</h3>

                            <p class="font-weight-bold">Saldo </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner" style="min-height: 123px;">
                            <h3><a href="{{ route('transaksi-list-anggota') }}" class="link-dashboard">Transaksi</a></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <a href="{{ route('transaksi-list-anggota') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6 d-none">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner" style="min-height: 123px;">
                            <h3 style="font-size: 1.5rem"><a href="{{ route('pinjaman-list') }}" class="link-dashboard">Rp. {{ number_format($listPinjaman->sum('sisa_pinjaman'),0,",",".") }}</a></h3>

                            <p class="font-weight-bold">Sisa Pinjaman </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <a href="{{ route('pinjaman-list') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @else
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{$anggota}}</h3>

                            <p>Anggota Aktif </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <a href="/anggota" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    console.log('Hi!');
</script>
@stop