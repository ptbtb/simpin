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
            <li class="breadcrumb-item"><a href="">Pinjaman</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('pinjaman-edit') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="kodeAnggota">Kode Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="{{ $anggota->kode_anggota }}">{{ $anggota->kode_anggota }}-{{ $anggota->nama_anggota }}</option>
                    </select>
                    <div class="text-danger" id="warningTextAnggota"></div>
                </div>
                <div class="col-md-6 form-group">
                    <label for="kode_jenis_pinjam">Jenis Simpanan</label>
                    {!! Form::select('kode_jenis_pinjam', $listJenisPinjaman,$pinjaman->kode_jenis_pinjam, ['id' => 'kode_jenis_pinjam', 'class' => 'form-control']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="alamat">Besar Pinjaman</label>
                        {!! Form::text('besar_pinjam',number_format($pinjaman->besar_pinjam,0,",","."), ['id' => 'besar_pinjam', 'class' => 'form-control']) !!}
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="sisa_pinjaman">Sisa Pinjaman</label>
                        {!! Form::text('sisa_pinjaman',  number_format($pinjaman->sisa_pinjaman,0,",","."), ['id' => 'sisa_pinjaman', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_asuransi">Biaya Asuransi</label>
                        {!! Form::text('biaya_asuransi',  number_format($pinjaman->biaya_asuransi,0,",","."), ['id' => 'biaya_asuransi', 'class' => 'form-control']) !!}
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_provisi">Biaya Provisi</label>
                        {!! Form::text('biaya_provisi',  number_format($pinjaman->biaya_provisi,0,",","."), ['id' => 'biaya_provisi', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_administrasi">Biaya Administrasi</label>
                        {!! Form::text('biaya_administrasi',  number_format($pinjaman->biaya_administrasi,0,",","."), ['id' => 'biaya_administrasi', 'class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="id_status_pinjaman">Status Pinjaman</label>
                       {!! Form::select('id_status_pinjaman', array(''=>'pilih status','1'=>'Belum Lunas','2'=>'Lunas'),$pinjaman->id_status_pinjaman, ['id' => 'id_status_pinjaman', 'class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <hr>
            <div class="d-flex">
                <h5>List Angsuran</h5>
                <a href="#" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Add</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Angsuran Ke</th>
                            <th>Angsuran Pokok</th>
                            <th>Jasa</th>
                            <th>Total Angsuran</th>
                            <th>Periode Pembayaran</th>
                            <th>Besar Pembayaran</th>
                            <th>Dibayar Pada Tanggal</th>
                            <th>Status</th>
                            <th style="width: 10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($listAngsuran as $angsuran)
                            <tr id="{{ $loop->iteration }}">
                                <td>{{ $angsuran->angsuran_ke }}</td>
                                <td>Rp. {{ number_format($angsuran->besar_angsuran,0,",",".") }}</td>
                                <td>Rp. {{ number_format($angsuran->jasa,0,",",".") }}</td>
                                <td>Rp. {{ number_format($angsuran->total_angsuran,0,",",".") }}</td>
                                <td>{{ $angsuran->jatuh_tempo->format('m-Y') }}</td>
                                <td>Rp. {{ number_format($angsuran->besar_pembayaran,0,",",".") }}</td>
                                <td>{{($angsuran->tgl_transaksi)?  $angsuran->tgl_transaksi->format('d M Y'):'-' }}</td>
                                <td>{{ $angsuran->statusAngsuran->name }}</td>
                                <td>
                                    <a href="#" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="form-group">
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
    var rowAngsuran = {{ $listAngsuran->count() }};
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