<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <style>
        .page-break {
            page-break-after: always;
        }
        </style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No Anggota</th>
                <th>NIPP</th>
                <th>Nama Anggota</th>
                <th>Jenis Anggota</th>
                <th>Tanggal Lahir</th>
                <th>Pekerjaan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anggotas as $anggota)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $anggota->kode_anggota }}</td>
                    <td>{{ $anggota->nipp }}</td>
                    <td>{{ $anggota->nama_anggota }}</td>
                    <td>
                        @if ($anggota->jenisAnggota && $anggota->jenisAnggota->nama_jenis_anggota)
                            {{ $anggota->jenisAnggota->nama_jenis_anggota }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $anggota->tgl_lahir }}</td>
                    <td>{{ $anggota->lokasi_kerja }}</td>
                    <td>{{ $anggota->status }}</td>
                </tr>
                @if ($loop->iteration % 20 == 0)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>