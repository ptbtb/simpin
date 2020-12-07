<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        .page-break {
            page-break-after: always;
        }
        @font-face{
            font-family: "Roboto Mono";
            src: url('../../../fonts/RobotoMono-Regular.ttf')
        }
        .kartu-simpanan{
            font-size: 12px;
            font-family: 'Roboto Mono';
            color: #000;
        }

        .table-bordered td{
            border-color: #000 !important;
        }

        .table td{
            padding: 5px;
        }

        tr.border-bottom-0 td{
            border-bottom: none;
        }
        tr.border-top-0 td{
            border-top: none;
        }
        tr.border-y-0 td{
            border-bottom: none;
            border-top: none;
        }

        .table.border-0 tr td{
            border: none !important;
        }

        .border-left-0{
            border-left: none !important;
        }

        .border-right-0{
            border-right: none !important;
        }
        td.border-0{
            border: none;
        }
    </style>
</head>
<body>
    @php
        $jumlahSimpanan = 0;
        $jumlahPengambilan = 0;
    @endphp
    <div class="container-fluid">
        <div class="table-responsive kartu-simpanan">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td colspan="10" class="border-0">Koperasi Pegawai Maritim</td>
                        <td class="text-right border-0">Tanggal</td>
                        <td class="border-0">: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td colspan="10" class="border-0">JAKARTA</td>
                        <td class="text-right border-0">Halaman</td>
                        <td class="border-0">: 1 / 1</td>
                    </tr>
                    <tr>
                        <td colspan="12" class="text-center"><b>KARTU SIMPANAN ANGGOTA</b></td>
                    </tr>
                    <tr class="border-bottom-0">
                        <td style="width: 10%" class="border-right-0">No. Anggota</td>
                        <td colspan="11" class="border-left-0">: {{ $anggota->kode_anggota }}</td>
                    </tr>
                    <tr class="border-y-0">
                        <td class="border-right-0"> Nama Lengkap</td>
                        <td colspan="11" class="border-left-0">: {{ $anggota->nama_anggota }}</td>
                    </tr>
                    <tr class="border-y-0">
                        <td class="border-right-0">Tanggal Lahir</td>
                        <td colspan="11" class="border-left-0">: {{ $anggota->tgl_lahir->format('d-m-Y') }}</td>
                    </tr>
                    <tr class="border-y-0">
                        <td class="border-right-0">Jenis Kelamin</td>
                        <td colspan="11" class="border-left-0">: {{ ($anggota->jenis_kelamin)? ($anggota->jenis_kelamin == 'L')? 'LAKI-LAKI':'PEREMPUAN':'-' }}</td>
                    </tr>
                    <tr class="border-top-0">
                        <td class="border-right-0">Unit</td>
                        <td colspan="11" class="border-left-0">: {{ ($anggota->lokasi_kerja)? $anggota->lokasi_kerja:'-' }}</td>
                    </tr>
                    <tr>
                        <td colspan="9" style="width: 75%" class="border-right-0">Simpanan</td>
                        <td colspan="3" class="border-left-0">Pengambilan</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="width: 50%" class="border-right-0">Bulan</td>
                        <td colspan="3" class="border-left-0">Jumlah</td>
                        <td colspan="2" class="border-right-0">Tanggal</td>
                        <td class="border-left-0 text-right">Jumlah</td>
                    </tr>
                    @foreach ($listSimpanan as $data)
                        <tr>
                            <td colspan="9" style="width: 75%">
                                <table class="table m-0 border-0">
                                    <tr>
                                        <td class="p-0" style="width: 8.3333%"></td>
                                        <td colspan="3" class="p-0" style="width: 25.8%">{{ $data->name }}</td>
                                        <td colspan="8" class="p-0 text-right">{{ number_format($data->balance,0,",",".") }}</td>
                                    </tr>
                                    @foreach ($data->list as $simpanan)
                                        <tr>
                                            <td colspan="8" class="p-0" style="width: 67.3%">{{ $simpanan->tgl_entri->format('m-Y') }}</td>
                                            <td colspan="4" class="p-0" style="width: 33.3333%">{{ number_format($simpanan->besar_simpanan,0,",",".") }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" style="width: 25%" class="text-right p-0">Sub Jumlah :</td>
                                        <td colspan="9" class="text-right p-0">{{ number_format($data->amount,0,",",".") }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right p-0">
                                            @php
                                                $jumlahSimpanan = $jumlahSimpanan + $data->final_balance;
                                            @endphp
                                            {{ number_format($data->final_balance,0,",",".") }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td colspan="3">
                                <table class="table m-0 border-0">
                                    @foreach ($data->withdrawalList as $withdraw)
                                        <tr>
                                            <td colspan="2" class="p-0">{{ $withdraw->tgl_ambil->format('d-m-Y') }}</td>
                                            <td class="p-0 text-right">{{ number_format($withdraw->besar_ambil,0,",",".") }}</td>
                                        </tr>
                                    @endforeach
                                    @php
                                        $jumlahPengambilan = $jumlahPengambilan + $data->withdrawalAmount;
                                    @endphp
                                </table>
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-right border-right-0" style="width: 16.66667%"> Jumlah :</td>
                        <td colspan="7" class="text-right border-left-0">{{ number_format($jumlahSimpanan,0,",",".") }}</td>
                        <td colspan="2" class="border-right-0">Jumlah : </td>
                            <td class="border-left-0 text-right">{{ number_format($jumlahPengambilan,0,",",".") }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-right border-right-0" style="width: 16.66667%"> Jumlah Simpanan :</td>
                        <td colspan="7" class="text-right border-left-0 border-right-0">{{ number_format($jumlahSimpanan - $jumlahPengambilan,0,",",".") }}</td>
                        <td colspan="2" class="border-right-0 border-left-0"></td>
                        <td class="border-left-0 text-right"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>