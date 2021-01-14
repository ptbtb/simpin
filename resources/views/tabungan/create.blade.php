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
            <li class="breadcrumb-item"><a href="">Saldo</a></li>
            <li class="breadcrumb-item active">Tambah Saldo</li>
        </ol>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('tabungan-create') }}" method="POST">
            @csrf
            <div class="row">
                @if ($request->kode_anggota)
                <div class="col-md-6 form-group">
                    <label for="kodeAnggota">Kode Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
                    </select>
                    <div class="text-danger" id="warningTextAnggota"></div>
                </div>
                @else
                <div class="col-md-6 form-group">
                    <label for="kodeAnggota">Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="">Pilih salah satu</option>
                    </select>
                    <div class="text-danger" id="warningTextAnggota"></div>
                </div>
                @endif

            </div>
            @foreach ($listJenisSimpanan as $jenisSimpanan)
            <div class="row">
                <div class="col-md-2">
                    <p><input type="text" name="kode_trans[]"  class="form-control"  autocomplete="off" required readonly value="{{ $jenisSimpanan->kode_jenis_simpan }}"></p>
                </div>
                <div class="col-md-3">
                    <p><input type="text" name="deskripsi[]"  class="form-control"  autocomplete="off" required readonly value="{{ $jenisSimpanan->nama_simpanan }}"></p>
                </div>
                <div class="col-md-2">
                    <p><input type="text" name="batch[]"  class="form-control" placeholder="Tahun" autocomplete="off" required></p>
                </div>
                <div class="col-md-2">
                    <p><input type="text" onkeypress="return isNumberKey(event)" name="besar_tabungan[]"  class="form-control" placeholder="Jumlah" autocomplete="off" required></p>
                </div>
            </div>
            @endforeach
            <div class="col-md-12 form-group">
                <button class="btn btn-sm btn-success" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
            </div>

        </form>

    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/collect.min.js') }}"></script>
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script>

                        function toRupiah(number)
                        {
                            var stringNumber = number.toString();
                            var length = stringNumber.length;
                            var temp = length;
                            var res = "Rp ";
                            for (let i = 0; i < length; i++) {
                                res = res + stringNumber.charAt(i);
                                temp--;
                                if (temp % 3 == 0 && temp > 0)
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