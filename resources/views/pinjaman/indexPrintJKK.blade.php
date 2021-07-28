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
            <li class="breadcrumb-item"><a href="">Pinjaman</a></li>
			<li class="breadcrumb-item active">Print JKK</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Select2', true)

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title">Donwload JKK</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('pengajuan-pinjaman-print-jkk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>No JKK</label>
                                <input type="text" name="no_jkk" id="noJkk" class="form-control" placeholder="Nomor JKK" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Pengajuan Pinjaman</label>
                                <select name="kode_pengajuan[]" id="kodePengajuan" class="form-control select2" multiple required>
                                    <option value="">Pilih</option>
                                    @foreach ($listPengajuanPinjaman as $pengajuan)
                                        <option value="{{ $pengajuan->kode_pengajuan }}">{{ $pengajuan->kode_anggota }} {{ $pengajuan->anggota->nama_anggota }} {{ $pengajuan->jenisPinjaman->nama_pinjaman }} {{ number_format($pengajuan->besar_pinjam,0,',','.') }} {{ $pengajuan->tgl_pengajuan->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                        <label for="tgl_print">Tgl Print</label>
                        <input id="tgl_print" type="date" name="tgl_print" class="form-control" placeholder="yyyy-mm-dd" required value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                    </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" name="submit" class="btn btn-sm btn-success"><i class="fas fa-print"></i> Print JKK</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    $(document).ready(function ()
    {
        $('.select2').select2();
    });
</script>
@endsection