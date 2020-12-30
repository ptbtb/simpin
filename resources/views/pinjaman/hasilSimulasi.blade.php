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
			<li class="breadcrumb-item active">{{ $title }}</li>
		</ol>
	</div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="width: 20%">Kode Anggota</th>
                            <th>:</th>
                            <td style="width: 30%">{{ $anggota->kode_anggota }}</td>
                            <th style="width: 20%">Unit Kerja</th>
                            <th>:</th>
                            <td style="width: 30%">{{ ucwords(strtolower($anggota->company->nama)) }}</td>
                        </tr>
                        <tr>
                            <th>Nama Anggota</th>
                            <th>:</th>
                            <td>{{ ucwords(strtolower($anggota->nama_anggota)) }}</td>
                            <th>Kelas</th>
                            <th>:</th>
                            <td>{{ $anggota->penghasilan->kelasCompany->nama }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Kelamin</th>
                            <th>:</th>
                            <td>{{ $anggota->jenis_kelamin }}</td>
                            <th>Jenis Anggota</th>
                            <th>:</th>
                            <td>{{ ucwords(strtolower($anggota->jenisAnggota->nama_jenis_anggota)) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <th>:</th>
                            <td>{{ ucwords(strtolower($anggota->status)) }}</td>
                            <th>Saldo</th>
                            <th>:</th>
                            <td id="saldo"></td>
                        </tr>
                    </table>
                </div>
                <div class="table table-responsive">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="width: 20%">Kategori Pinjaman</th>
                            <th>:</th>
                            <td style="width: 30%">{{ ucwords(strtolower($jenisPinjaman->kategoriJenisPinjaman->name)) }}</td>
                            <th style="width: 20%">Tipe Pinjaman</th>
                            <th>:</th>
                            <td style="width: 30%">{{ ucwords(strtolower($jenisPinjaman->tipeJenisPinjaman->name)) }}</td>
                        </tr>
                        <tr>
                            <th>Jenis Pinjaman</th>
                            <th>:</th>
                            <td>{{ ucwords(strtolower($jenisPinjaman->nama_pinjaman)) }}</td>
                            <th>Lama Angsuran</th>
                            <th>:</th>
                            <td>{{ $lamaAngsuran }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Besar Pinjaman</th>
                            <th>:</th>
                            <td id="besarPinjaman"></td>
                            <th>Maksimal Pinjaman</th>
                            <th>:</th>
                            <td id="maksimalPinjaman"></td>
                        </tr>
                        <tr>
                            <th>Biaya Administrasi</th>
                            <th>:</th>
                            <td id="biayaAdministrasi"></td>
                            <th>Provisi</th>
                            <th>:</th>
                            <td id="provisi"></td>
                        </tr>
                        <tr>
                            <th>Asuransi</th>
                            <th>:</th>
                            <td id="asuransi"></td>
                            <th>Jasa</th>
                            <th>:</th>
                            <td id="jasa"></td>
                        </tr>
                        <tr>
                            <th>Angsuran Pokok</th>
                            <th>:</th>
                            <td id="angsuranPokok"></td>
                            <th>Besar Angsuran</th>
                            <th>:</th>
                            <td id="besarAngsuran"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-4">
                <a href="{{ route('generate-form-pinjaman', $collection) }}" class="btn btn-sm btn-info" target="_blank"><i class="fas fa-download"></i> Download Form Persetujuan</a>
            </div>
        </div>
    </div>
</div>
{{-- {{ dd($collection) }} --}}
@endsection

@section('css')
    <style>
        .table tr th, .table tr td{
            padding: 8px;
        }
    </style>
@endsection

@section('js')
    <script>
        var saldo = besarPinjaman = maksimalPinjaman = biayaAdministrasi = provisi = asuransi = jasa = angsuranPokok = besarAngsuran = 0;

        $(document).ready(function ()
        {
            saldo = {{ $saldo->jumlah }};
            besarPinjaman = {{ $besarPinjaman }};
            maksimalPinjaman = {{ $maksimalBesarPinjaman }};
            biayaAdministrasi = {{ $biayaAdministrasi }};
            provisi = {{ $provisi }};
            asuransi = {{ $asuransi }};
            jasa = {{ $jasa }};
            angsuranPokok = {{ $angsuranPokok }};
            besarAngsuran = {{ $angsuranPerbulan }};

            $('#saldo').text(toRupiah(saldo));
            $('#besarPinjaman').text(toRupiah(besarPinjaman));
            $('#maksimalPinjaman').text(toRupiah(maksimalPinjaman));
            $('#biayaAdministrasi').text(toRupiah(biayaAdministrasi));
            $('#provisi').text(toRupiah(provisi));
            $('#asuransi').text(toRupiah(asuransi));
            $('#jasa').text(toRupiah(jasa));
            $('#angsuranPokok').text(toRupiah(angsuranPokok));
            $('#besarAngsuran').text(toRupiah(besarAngsuran));
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
    </script>
@endsection