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
                    <input type="hidden" name="kode_pinjam" value="{{$pinjaman->kode_pinjam}}">
                    <label for="kodeAnggota">Kode Anggota</label>
                    <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                        <option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
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
                        {!! Form::text('besar_pinjam',number_format($pinjaman->besar_pinjam,0,",","."), ['id' => 'besar_pinjam', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="sisa_pinjaman">Sisa Pinjaman</label>
                        {!! Form::text('sisa_pinjaman',  number_format($pinjaman->sisa_pinjaman,0,",","."), ['id' => 'sisa_pinjaman', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_asuransi">Biaya Asuransi</label>
                        {!! Form::text('biaya_asuransi',  number_format($pinjaman->biaya_asuransi,0,",","."), ['id' => 'biaya_asuransi', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
            
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_provisi">Biaya Provisi</label>
                        {!! Form::text('biaya_provisi',  number_format($pinjaman->biaya_provisi,0,",","."), ['id' => 'biaya_provisi', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="biaya_administrasi">Biaya Administrasi</label>
                        {!! Form::text('biaya_administrasi',  number_format($pinjaman->biaya_administrasi,0,",","."), ['id' => 'biaya_administrasi', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="id_status_pinjaman">Status Pinjaman</label>
                       {!! Form::select('id_status_pinjaman', array(''=>'pilih status','1'=>'Belum Lunas','2'=>'Lunas'),$pinjaman->id_status_pinjaman, ['id' => 'id_status_pinjaman', 'class' => 'form-control toRupiah']) !!}
                    </div>
                </div>
            </div>

            <hr>
            <h5>List Angsuran</h5>
            <div class="text-right mb-2">
                <a class="btn btn-xs btn-success text-white btn-add-row"><i class="fa fa-plus"></i> Add row</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Angsuran Ke</th>
                            <th>Angsuran Pokok</th>
                            <th>Jasa</th>
                            <th>Periode Pembayaran</th>
                            <th>Besar Pembayaran</th>
                            <th>Dibayar Pada Tanggal</th>
                            <th>COA Kredit</th>
                            <th>Serial Jurnal</th>
                            <th style="min-width: 120px;">Status</th>
                            <th style="min-width: 120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        @foreach ($listAngsuran as $angsuran)
                            <tr>
                                <td><input type="number" name="angsuran_ke[]" value="{{ $angsuran->angsuran_ke }}"></td>
                                <td><input type="text" class="toRupiah" name="besar_angsuran[]" value="{{ $angsuran->besar_angsuran }}"></td>
                                <td><input type="text" class="toRupiah" name="jasa[]" value="{{ $angsuran->jasa }}"></td>
                                <td><input type="date" name="jatuh_tempo[]" value="{{ $angsuran->jatuh_tempo->toDateString() }}"></td>
                                <td><input type="text"  class="toRupiah"name="besar_pembayaran[]" value="{{ $angsuran->besar_pembayaran }}"></td>
                                <td>
                                    <input type="date" name="tanggal_pembayaran[]" value="{{($angsuran->tgl_transaksi)?  $angsuran->tgl_transaksi->toDateString():'' }}">
                                </td>
                                <td ><input  type="text"  class=""name="id_akun_kredit[]" value="{{ ($angsuran->id_akun_kredit!==null)?$angsuran->akunKredit->CODE:'' }}"></td>
                                <td ><input type="text"  class=""name="serial_number[]" value="{{ $angsuran->serial_number }}"></td>
                                
                                <td style="width" > {!! Form::select('id_status_angsuran[]', array(''=>'pilih status','1'=>'Belum Lunas','2'=>'Lunas'),$angsuran->id_status_angsuran, ['id' => 'id_status_angsuran', 'class' => 'form-control toRupiah']) !!}
                                </td>
                                <td>
                                    <a class="btn btn-xs btn-danger text-white btn-delete-row"><i class="fa fa-trash"></i> Delete Row</a>
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
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script>
    var rowAngsuran = {{ $listAngsuran->count() }};
    function toRupiah(field)
    {
        new Cleave(field, {
            numeralDecimalMark: ',',
            delimiter: '.',
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            numeralDecimalScale: 4,
        });
    }
    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

    $(document).on('click', '.btn-add-row', function ()
    {
        var pattern = '<tr>' +
                            '<td><input type="number" name="angsuran_ke[]"></td>' +
                            '<td><input type="text" class="toRupiah"" name="besar_angsuran[]"></td>' +
                            '<td><input type="text" class="toRupiah" name="jasa[]"></td>' +
                            '<td><input type="text" class="toRupiah" name="total_angsuran"></td>' +
                            '<td><input type="date" name="jatuh_tempo[]"></td>' +
                            '<td><input type="text" class="toRupiah" name="besar_pembayaran[]"></td>' +
                            '<td>' +
                                '<input type="date" name="tanggal_pembayaran[]">' +
                            '</td>' +
                            '<td>' +
                                '<a class="btn btn-xs btn-danger text-white btn-delete-row"><i class="fa fa-trash"></i> Delete Row</a>' +
                            '</td>' +
                        '</tr>';

        $('.table-body').append(pattern);

        $('.toRupiah').toArray().forEach(function(field){
            toRupiah(field);
        });
    });

    $(document).on('click', '.btn-delete-row', function ()
    {
        $(this).parent().parent().remove();
    });

    $('.toRupiah').toArray().forEach(function(field){
        toRupiah(field);
    });
    $('#kodeAnggota').select2({
            ajax: {
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
    $('#kode_jenis_pinjam').select2();

</script>
@stop