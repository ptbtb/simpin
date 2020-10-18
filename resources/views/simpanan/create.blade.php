@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
			<li class="breadcrumb-item active">Tambah Transaksi</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="jenisSimpanan">Jenis Simpanan</label>
                    <select name="jenis_simpanan" id="jenisSimpanan" class="form-control" required>
                        <option value="">Pilih salah satu</option>
                        @foreach ($listJenisSimpanan as $jenisSimpanan)
                            <option value="{{ $jenisSimpanan->kode_jenis_simpan }}">{{ strtoupper($jenisSimpanan->nama_simpanan) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="besarSimpanan">Besar Simpanan</label>
                    <input type="text" name="besar_simpanan" id="besarSimpanan" class="form-control" placeholder="Besar Simpanan" autocomplete="off" required>
                </div>
                <div class="col-md-6 form-group">
                    <label for="kodeAnggota">Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="">Pilih salah satu</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="kodeTransaksi">Kode Transaksi</label>
                    <select name="kode_transaksi" id="kodeTransaksi" class="form-control" required>
                        <option value="">Pilih salah satu</option>
                        @foreach ($listKodeTransaksi as $kodeTransaksi)
                            <option value="{!! $kodeTransaksi->CODE !!}">{{ $kodeTransaksi->NAMA_TRANSAKSI }} ({{ $kodeTransaksi->CODE }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="5" class="form-control"></textarea>
                </div>
                <div class="col-md-12 mt-md-3 form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-md-12 form-group">
                    <button class="btn btn-sm btn-success"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/collect.min.js') }}"></script>+
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script>
    var jenisSimpanan = collect({!!$listJenisSimpanan!!});

    $('#jenisSimpanan').select2();
    $('#kodeTransaksi').select2();
    $("#kodeAnggota").select2({
        ajax: {
            placeholder: "Select a state",
            url: '{{ route('anggota-ajax-search') }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        }
    });
    $('#jenisSimpanan').on('change', function ()
    {
        var selectedValue = $(this).children("option:selected").val();
        var selectedJenisSimpanan = jenisSimpanan.where('kode_jenis_simpan', selectedValue).first();
        var besarSimpanan = selectedJenisSimpanan.besar_simpanan;
        if (besarSimpanan == 0 || besarSimpanan == null || besarSimpanan == '')
        {
            $('#besarSimpanan').val('');
            $('#besarSimpanan').attr('readonly',false);
            // var cleave = new Cleave('#besarSimpanan', {
            //     numeral: true,
            //     prefix: 'Rp ',
            //     noImmediatePrefix: true,
            //     numeralThousandsGroupStyle: 'thousand'
            // });
        }
        else
        {
            $('#besarSimpanan').val(besarSimpanan);
            // var cleave = new Cleave('#besarSimpanan', {
            //     numeral: true,
            //     prefix: 'Rp ',
            //     noImmediatePrefix: true,
            //     numeralThousandsGroupStyle: 'thousand'
            // });
            $('#besarSimpanan').attr('readonly',true);
        }
    });
</script>
@stop