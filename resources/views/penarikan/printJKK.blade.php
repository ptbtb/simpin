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
            <li class="breadcrumb-item"><a href="">Penarikan</a></li>
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
                <form action="{{ route('penarikan-print-jkk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>No JKK</label>
                                <input type="text" name="no_jkk" id="noJkk" class="form-control" placeholder="Nomor JKK" autocomplete="off" required>
                            </div>
                            <div class="form-group">
                                <label>Kode Pengajuan Pinjaman</label>
                                <select name="kode_ambil[]" id="kodeAmbil" class="form-control select2" multiple required>
                                    @foreach ($listPenarikan as $penarikan)
                                        <option value="{{ $penarikan->kode_ambil }}">{{ $penarikan->kode_ambil }}</option>
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
        $('.select2').select2({
            placeholder: 'Pilih Pengambilan'
        });
    });
</script>
@endsection