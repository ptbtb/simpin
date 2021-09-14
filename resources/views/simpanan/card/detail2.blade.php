{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Kartu Simpanan {{ ucwords(strtolower($anggota->nama_anggota)) }}</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <style>
        .kartu-simpanan{
            font-size: 12px;
            font-family: 'Roboto Mono', monospace;
            color: #000;
        }

        .kartu-simpanan .border{
            border-color: #000 !important;
        }
    </style>
</head>
<body> --}}
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <style>
        .kartu-simpanan{
            font-size: 12px;
            font-family: 'Roboto Mono', monospace;
            color: #000;
        }

        .kartu-simpanan .border{
            border-color: #000 !important;
        }
    </style>
    @php
        $jumlahSimpanan = 0;
        $jumlahPengambilan = 0;
    @endphp
    <div class="container-fluid kartu-simpanan">
        <div class="row">
            <div class="col-10">Koperasi Pegawai Maritim</div>
            <div class="col-1 text-right">Tanggal</div>
            <div class="col-1 px-0">: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</div>
        </div>
        <div class="row">
            <div class="col-10">JAKARTA</div>
            <div class="col-1 text-right">Halaman</div>
            <div class="col-1 px-0">: 1 / 1</div>
        </div>
        <div class="row">
            <div class="col-12 text-center font-weight-bold">
                KARTU SIMPANAN ANGGOTA
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="border p-1">
                    <div class="row">
                        <div class="col-1">No. Anggota</div>
                        <div class="col-11">: {{ $anggota->kode_anggota }}</div>
                    </div>
                    <div class="row">
                        <div class="col-1 pr-0">Nama Lengkap</div>
                        <div class="col-11">: {{ $anggota->nama_anggota }}</div>
                    </div>
                    <div class="row">
                        <div class="col-1 pr-0">Tanggal Lahir</div>
                        <div class="col-11">: {{ $anggota->tgl_lahir->format('d-m-Y') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-1 pr-0">Jenis Kelamin</div>
                        <div class="col-11">: {{ ($anggota->jenis_kelamin)? ($anggota->jenis_kelamin == 'L')? 'LAKI-LAKI':'PEREMPUAN':'-' }}</div>
                    </div>
                    <div class="row">
                        <div class="col-1 pr-0">Unit</div>
                        <div class="col-11">: {{ ($anggota->company)? $anggota->company->nama:'-' }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="border p-1 border-top-0">
                    <div class="row">
                        <div class="col-9 pr-1">
                            Simpanan
                        </div>
                        <div class="col-3 pl-2">
                            Pengambilan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-9 pr-0">
                <div class="border p-1 border-top-0">
                    <div class="row">
                        <div class="col-8">Bulan</div>
                        <div class="col-4">Jumlah</div>
                    </div>
                </div>
            </div>
            <div class="col-3 pl-0">
                <div class="border p-1 border-top-0 border-left-0">
                    <div class="row">
                        <div class="col-6">Tanggal</div>
                        <div class="col-6 text-right">Jumlah</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- *********************************************************** LIST SIMPANAN *********************************************************** -->
        @foreach ($listSimpanan as $data)
            <div class="row">
                <div class="col-9 pr-0">
                    <div class="border p-1 border-top-0 h-100">
                        <div class="row">
                            <div class="col-3 offset-1">
                                {{ strtoupper($data->name) }}
                            </div>
                            <div class="col-8 text-right">
                                {{ number_format($data->balance,0,",",".") }}
                            </div>
                        </div>
                        @foreach ($data->list as $simpanan)
                            <div class="row">
                                <div class="col-8">{{ $simpanan->periode->format('m-Y') }}</div>
                                <div class="col-4">{{ number_format($simpanan->besar_simpanan,0,",",".") }}</div>
                            </div>
                        @endforeach
                        <div class="row">
                            <div class="col-3 pr-0 text-right">
                                Sub Jumlah :
                            </div>
                            <div class="col-9 text-right">
                                {{ number_format($data->amount,0,",",".") }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                @php
                                    $jumlahSimpanan = $jumlahSimpanan + $data->final_balance;
                                @endphp
                                {{ number_format($data->final_balance,0,",",".") }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-3 pl-0">
                    <div class="border p-1 border-top-0 border-left-0 h-100">
                        @foreach ($data->withdrawalList as $withdraw)
                            <div class="row">
                                <div class="col-6">{{ $withdraw->tgl_ambil->format('d-m-Y') }}</div>
                                <div class="col-6 text-right">{{ number_format($withdraw->besar_ambil,0,",",".") }}</div>
                            </div>
                        @endforeach
                        @php
                            $jumlahPengambilan = $jumlahPengambilan + $data->withdrawalAmount;
                        @endphp
                    </div>
                </div>
            </div>
        @endforeach
        <!-- *********************************************************** JUMLAH *********************************************************** -->
        <div class="row">
            <div class="col-9 pr-0">
                <div class="border p-1 border-top-0 h-100">
                    <div class="row">
                        <div class="col-3 pr-0 text-right">
                            Jumlah :
                        </div>
                        <div class="col-9 text-right font-weight-bold">
                            {{ number_format($jumlahSimpanan,0,",",".") }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 pl-0">
                <div class="border p-1 border-top-0 border-left-0 h-100">
                    <div class="row">
                        <div class="col-6 text-left">
                            Jumlah :
                        </div>
                        <div class="col-6 text-right font-weight-bold">
                            {{ number_format($jumlahPengambilan,0,",",".") }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-9 pr-0">
                <div class="border p-1 border-top-0 border-right-0 h-100">
                    <div class="row">
                        <div class="col-3 pr-0 text-right">
                            Jumlah Simpanan :
                        </div>
                        <div class="col-9 text-right font-weight-bold">
                            {{ number_format($jumlahSimpanan - $jumlahPengambilan,0,",",".") }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3 pl-0">
                <div class="border p-1 border-top-0 border-left-0 h-100">
                </div>
            </div>
        </div>
    </div>
{{--
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html> --}}
