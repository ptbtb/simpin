<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">

    <title></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> --}}

    <style>
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .kartu-simpanan{
            font-size: 12px !important;
            font-family: 'Roboto Mono', monospace;
            color: #000;
        }

        .border{
            border: 1px solid #000;
        }
        .border-top-0{
            border-top: none;
        }
        .border-bottom-0{
            border-top: none;
        }
        .border-right-0{
            border-right: none;
        }
        .border-left-0{
            border-left: none;
        }
        .pr-0{
            padding-right: 0;
        }
        .pl-0{
            padding-left: 0;
        }
        .h-100{
            height: 100%!important;
        }
        .p-1{
            padding: 0 .3rem;
        }

    </style>

</head>
<body style="background: white">
    @php
        $jumlahSimpanan = 0;
        $jumlahPengambilan = 0;
    @endphp
    <div class="kartu-simpanan">
        <div class="row">
            <div class="col-xs-8">Koperasi Pegawai Maritim</div>
            <div class="col-xs-1 text-right">Tanggal</div>
            <div class="col-xs-2">: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
        </div>
        <div class="row">
            <div class="col-xs-8">JAKARTA</div>
            <div class="col-xs-1 text-right">Halaman</div>
            <div class="col-xs-2">: 1 / 1</div>
        </div>
        <div class="row">
            <div class="col-xs-12 text-center">
                <b>KARTU SIMPANAN ANGGOTA</b>
            </div>
        </div>
        <div class="border p-1">
            <div class="row">
                <div class="col-xs-2">No. Anggota</div>
                <div class="col-xs-9">: {{ $anggota->kode_anggota }}</div>
            </div>
            <div class="row">
                <div class="col-xs-2">Nama Lengkap</div>
                <div class="col-xs-9">: {{ $anggota->nama_anggota }}</div>
            </div>
            <div class="row">
                <div class="col-xs-2">Tanggal Lahir</div>
                <div class="col-xs-9">: {{ $anggota->tgl_lahir->format('d-m-Y') }}</div>
            </div>
            <div class="row">
                <div class="col-xs-2">Jenis Kelamin</div>
                <div class="col-xs-9">: {{ ($anggota->jenis_kelamin)? ($anggota->jenis_kelamin == 'L')? 'LAKI-LAKI':'PEREMPUAN':'-' }}</div>
            </div>
            <div class="row">
                <div class="col-xs-2">Unit</div>
                <div class="col-11">: {{ ($anggota->company)? $anggota->company->nama:'-' }}</div>
            </div>
        </div>
        <div class="border border-top-0 p-1">
            <div class="row">
                <div class="col-xs-8 text-center">Simpanan</div>
                <div class="col-xs-3 text-center">Pengambilan</div>
            </div>
        </div>
        <div class="border border-top-0 p-1">
            <div class="row">
                <div class="col-xs-4">Bulan</div>
                <div class="col-xs-3">Jumlah</div>
                <div class="col-xs-2">Tanggal</div>
                <div class="col-xs-2">Jumlah</div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-7">
                <table class="table">
                    <tr>
                        <td></td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-4">
                fdsfsd
            </div>
        </div>
    </div>
</body>
</html>
