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
            <li class="breadcrumb-item"><a href="{{ route('pengajuan-pinjaman-list') }}">Pengajuan Pinjaman</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Select2', true)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('pengajuan-pinjaman-add') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nama Anggota</label>
                    <input type="text" name="nama_anggota" class="form-control" readonly value="{{ Auth::user()->anggota->nama_anggota }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Kode Anggota</label>
                    <input type="text" name="kode_anggota" class="form-control" readonly value="{{ Auth::user()->anggota->kodeAnggotaPrefix }}">
                </div>
                <div class="col-md-6 form-group">
                    <label>Jenis Pinjaman</label>
                    <select name="jenis_pinjaman" class="form-control" required id="jenisPinjaman">
                        @foreach ($listJenisPinjaman as $jenisPinjaman)
                            <option value="{!! $jenisPinjaman->kode_jenis_pinjam !!}">{{ $jenisPinjaman->nama_pinjaman }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Besar Pinjaman</label>
                    <input type="text" name="besar_pinjaman" class="form-control" placeholder="Besar Pinjaman"required id="besarPinjaman">
                </div>
                <div class="col-md-6 form-group">
                    <label>Maksimal Pinjaman</label>
                    <input type="text" name="maksimal_besar_pinjaman" class="form-control" readonly id="maksimalBesarPinjaman">
                </div>
                <div class="col-md-6 form-group">
                    <label>Lama Angsuran</label>
                    <input type="text" name="lama_angsuran" class="form-control" placeholder="Lama Angsuran" readonly id="lamaAngsuran">
                </div>
                {{-- <div class="col-md-6 form-group">
                    <label>Bunga</label>
                    <input type="text" name="bunga" class="form-control" placeholder="Bunga" readonly id="bunga">
                </div>
                <div class="col-md-6 form-group">
                    <label>Besar Angsuran</label>
                    <input type="text" name="besar_angsuran" class="form-control" placeholder="Besar Angsuran" readonly id="besarAngsuran">
                </div> --}}
                <div class="col-12">
                    <button type="submit" name="ajukan_pinjaman" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Ajukan Pinjaman</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/collect.min.js') }}"></script>
    <script>
        var jenisPinjaman = collect({!!$listJenisPinjaman!!});

        $(document).ready(function ()
        {
            $('#jenisPinjaman').select2();
            var selectedId = jenisPinjaman.first().kode_jenis_pinjam;
            updateInfo(selectedId);
        });
        
        $('#jenisPinjaman').on('change', function ()
        {
            var selectedId = $(this).find(":selected").val();
            var besarPinjaman = $('#besarAngsuran').val();
            updateInfo(selectedId);
            // updateAngsuran(idJenisPinjaman, besarPinjaman);
        });

        function updateInfo(id)
        {
            var selectedJenisPinjaman = jenisPinjaman.where('kode_jenis_pinjam',id).first();
            $('#lamaAngsuran').val(selectedJenisPinjaman.lama_angsuran);
            // $('#bunga').val(selectedJenisPinjaman.bunga);
            $('#maksimalBesarPinjaman').val(selectedJenisPinjaman.maks_pinjam);
        }

        $('#besarPinjaman').on('keyup', function ()
        {
            var besarPinjaman = $(this).val();
            var idJenisPinjaman = $('#jenisPinjaman').find(":selected").val();
            updateAngsuran(idJenisPinjaman, besarPinjaman);
        });

        function updateAngsuran(idJenisPinjaman, besarPinjaman) {
            var jPinjaman = jenisPinjaman.where('kode_jenis_pinjam',idJenisPinjaman).first();
            var bunga = jPinjaman.bunga;
            var lamaAngsuran = jPinjaman.lama_angsuran;
            var angsuranBulan = besarPinjaman/lamaAngsuran;
            var persentaseBunga = angsuranBulan*bunga/100;
            var angsuran = angsuranBulan + persentaseBunga;
            // var b = $('#besarAngsuran').val(angsuran);
        }
    </script>
@endsection