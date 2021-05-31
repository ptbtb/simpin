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

    .c-title{
        font-weight: bold;
        font-size: 16px;
    }

    .small-box h3{
        font-size: 1.5rem !important;
    }
</style>
@endsection

@section('content')
<div class="col-lg-12">
    <div class="form-panel">
        <div class="row">
            @if ($role->id == ROLE_ANGGOTA)
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner" style="min-height: 123px;">
                            <h3><a href="{{ route('simpanan-index-card') }}" class="link-dashboard">
                                @if ($saldo)
                                    Rp. {{ number_format($saldo->jumlah,0,",",".") }}
                                @else
                                    0
                                @endif
                            </a></h3>

                            <p class="font-weight-bold">Saldo </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="{{ route('simpanan-index-card') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
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
            @else
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{$anggota}}</h3>

                            <p>Anggota Aktif </p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <a href="/anggota/list" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($simpanan,0,",",".") }}</h3>

                            <p>Total Simpanan </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <a href="/simpanan/list" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            @endif
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($sisaPinjaman,0,",",".") }}</h3>

                        <p>Sisa Pinjaman </p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <a href="{{ route('pinjaman-list') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
 <div class="px-3 pb-3 text-red">
        <hr style="border-color: #fff">
        *{!! $settings[COMPANY_SETTING_SPLASH] !!}
    </div>

@if (!\Auth::user()->isAnggota())
<div class="card">
    <div class="card-body">
        <div class="form-group">
            <form action="{{ route('home') }}" method="POST">
                @csrf
                <label>Search Anggota</label>
                <div class="input-group">
                    <input class="form-control form-control-navbar" name="kw_kode_anggota" type="search" name="q" placeholder="No Anggota" aria-label="search" autocomplete="off" required>
                    <div class="input-group-append border" style="border-top-right-radius: .25rem; border-bottom-right-radius: .25rem; border-color: #ced4da !important">
                        <button class="btn btn-navbar" type="submit" name="search" value="search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @if (isset($searchResult))
        <hr class="my-3">
        <h6 style="font-size: .8rem; font-weight: 700" class="mt-3">Data Anggota</h6>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive border p-2" style="border-radius: 4px">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="border-0">Tanggal Daftar</th>
                                <td class="border-0">:</td>
                                <td class="border-0">{{ $searchResult->created_at->format('d F Y') }}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="width: 20%">No Anggota</th>
                                <td style="width: 2%">:</td>
                                <td style="width: 28%">{{ $searchResult->kode_anggota }}</td>
                                <th style="width: 20%">Nama</th>
                                <td style="width: 2%">:</td>
                                <td style="width: 28%">{{ $searchResult->nama_anggota }}</td>
                            </tr>
                            <tr>
                                <th>Tempat Lahir</th>
                                <td>:</td>
                                <td>{{ ($searchResult->tempat_lahir)? ucwords(strtolower($searchResult->tempat_lahir)):'-' }}</td>
                                <th>Tanggal Lahir</th>
                                <td>:</td>
                                <td>{{ ($searchResult->tgl_lahir)? $searchResult->tgl_lahir->format('d F Y'):'-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>:</td>
                                <td>{{ ($searchResult->jenis_kelamin)? ($searchResult->jenis_kelamin == 'L')? 'Laki - laki':'Perempuan':'-' }}</td>
                                <th>Alamat</th>
                                <td>:</td>
                                <td>{{ ($searchResult->alamat_anggota)? $searchResult->alamat_anggota:'' }}</td>
                            </tr>
                            <tr>
                                <th>KTP</th>
                                <td>:</td>
                                <td>{{ ($searchResult->ktp)? $searchResult->ktp:'-' }}</td>
                                <th>NIPP</th>
                                <td>:</td>
                                <td>{{ ($searchResult->nipp)? $searchResult->nipp:'-' }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi Kerja</th>
                                <td>:</td>
                                <td>{{ ($searchResult->lokasi_kerja)? $searchResult->lokasi_kerja:'-' }}</td>
                                <th>Tanggal Masuk</th>
                                <th>:</td>
                                <td>{{ ($searchResult->tgl_masuk)? $searchResult->tgl_masuk->format('d F Y'):'-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>:</td>
                                <td>{{ ($searchResult->email)? $searchResult->email:'-' }}</td>
                                <th>Nomor Telepon</th>
                                <td>:</td>
                                <td>{{ ($searchResult->telp)? $searchResult->telp:'-' }}</td>
                            </tr>
                            <tr>
                                <th>Emergency Kontak</th>
                                <td>:</td>
                                <td>{{ ($searchResult->emergency_kontak)? $searchResult->emergency_kontak:'-' }}</td>
                                <th>Nomor Rekening</th>
                                <td>:</td>
                                <td>{{ ($searchResult->no_rek)? $searchResult->no_rek:'-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            
            <div class="col-md-2">
                <a href="anggota/edit/{{$searchResult->kode_anggota}}" class="btn btn-default btn-sm w-100 mt-1"><i class="fa fa-money-bill"></i> Edit Anggota</a>
                <a href="{{ route('simpanan-add', ['kode_anggota'=>$searchResult->kode_anggota]) }}" class="btn btn-success btn-sm w-100 mt-1"><i class="fa fa-money-bill"></i> Simpanan</a>
                <a href="pinjaman/list/{{$searchResult->kode_anggota}}" class="btn btn-info btn-sm w-100 mt-1"><i class="fas fa-hand-holding-usd"></i> Pinjaman</a>
            </div>
            @if($searchResult->tabungan->isNotEmpty())
            <div class="col-md-2">
                <a href="{{ route('simpanan-index-card', ['kode_anggota' => $searchResult->kode_anggota]) }}" class="btn btn-warning btn-sm w-100 mt-1"><i class="fas fa-clipboard"></i> Kartu Simpanan</a>
                @if($searchResult->pinjaman->isEmpty())
                <a href="{{ route('pinjaman-create', ['kode_anggota' => $searchResult->kode_anggota]) }}" class="btn btn-primary btn-sm w-100 mt-1"><i class="fas fa-clipboard"></i> Set Saldo Pinjaman</a>
                @endif
            </div>
            @else
            <div class="col-md-2">
                <a href="{{ route('tabungan-create', ['kode_anggota' => $searchResult->kode_anggota]) }}" class="btn btn-primary btn-sm w-100 mt-1"><i class="fas fa-clipboard"></i> Saldo Awal Simpanan</a>
            </div>

            @endif

        </div>
        @endif
    </div>
</div>
@endif
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
    console.log('Hi!');
</script>
@stop