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
                    <input type="text" name="besar_simpanan" id="besarSimpanan" onkeypress="return isNumberKey(event)"  class="form-control" placeholder="Besar Simpanan" autocomplete="off" required >
                </div>
                <div class="col-md-12 form-group">
                    <label for="kodeAnggota">Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="">Pilih salah satu</option>
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

    $("#jenisSimpanan").select2({
        ajax: {
            url: '{{ route('jenis-simpanan-search') }}',
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
        }
        else
        {
            $('#besarSimpanan').val(toRupiah(besarSimpanan));
            $('#besarSimpanan').attr('readonly',true);
        }
    });

    $('#besarSimpanan').on('keyup', function ()
    {
        var besarSimpanan = $(this).val().toString();
        besarSimpanan = besarSimpanan.replace(/[^\d]/g, "",'');
        $('#besarSimpanan').val(toRupiah(besarSimpanan));
    });

    function toRupiah(number)
    {
        var stringNumber = number.toString();
        var length = stringNumber.length;
        var temp = length;
        var res = "Rp ";
        for (let i = 0; i < length; i++) {
            res = res + stringNumber.charAt(i);
            temp--;
            if (temp%3 == 0 && temp > 0)
            {
                res = res + ".";
            }
        }
        return res;
    }

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

        return true;
    }
</script>
@stop