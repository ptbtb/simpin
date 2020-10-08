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
                <th>Tanggal Pinjaman</th>
                <th>Jenis Pinjaman</th>
                <th>Besar Pinjaman</th>
                <th>Sisa Pinjaman</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listPinjaman as $pinjaman)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pinjaman->tgl_entri->format('d M Y') }}</td>
                    <td>{{ $pinjaman->jenisPinjaman->nama_pinjaman }}</td>
                    <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
                    <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
                    <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
                    <td>{{ ucwords($pinjaman->status) }}</td>
                </tr>
            @endforeach
        </tbody> 
    </table>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>