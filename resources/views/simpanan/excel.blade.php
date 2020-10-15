<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                    <th>Nama Anggota</th>
                    <th>Nomor Anggota</th>
                @endif
                <th>Jenis Simpanan</th>
                <th>Besar Simpanan</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Entry</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listSimpanan as $simpanan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                        <td>
                            @if ($simpanan->anggota)
                                {{ $simpanan->anggota->nama_anggota }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($simpanan->anggota)
                                {{ $simpanan->anggota->kode_anggota }}
                            @else
                                -
                            @endif
                        </td>
                    @endif
                    <td>{{ $simpanan->jenis_simpan }}</td>
                    <td>Rp. {{ number_format($simpanan->besar_simpanan,0,",",".") }}</td>
                    <td>
                        @if ($simpanan->tgl_mulai)
                            {{ $simpanan->tgl_mulai->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($simpanan->tgl_entri)
                            {{ $simpanan->tgl_entri->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody> 
    </table>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>