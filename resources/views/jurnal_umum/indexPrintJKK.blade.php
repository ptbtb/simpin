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
            <li class="breadcrumb-item"><a href="">Jurnal Umum</a></li>
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
                <form action="{{ route('jurnal-umum-print-jkk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>No Jurnal Umum</label>
                                <select name="kode_jurnal_umum" id="kodeJurnalUmum" class="form-control select2" required>
                                    <option value="">Pilih</option>
                                    @foreach ($listJurnalUmum as $jurnalUmum)
                                        <option value="{{ $jurnalUmum->id }}">{{ $jurnalUmum->serial_number_view }} {{ $jurnalUmum->deskripsi }} {{ $jurnalUmum->tgl_transaksi->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
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