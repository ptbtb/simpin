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
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Anggota</th>
                    <th>Kode Tabungan</th>
                    <th>Besar Ambil</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Keterangan</th>
                    <th>Kode Transaksi</th>
                    <th>Tanggal Posting</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listPenarikan as $penarikan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $penarikan->anggota->kode_anggota }}</td>
                        <td>{{ $penarikan->anggota->tabungan->where('id', $penarikan->id_tabungan)->first()->kode_tabungan ?? '-' }}</td>
                        <td>Rp. {{ number_format($penarikan->besar_ambil,0,",",".") }}</td>
                        <td>{{ $penarikan->tgl_ambil->format('d M Y') }}</td>
                        <td>{{ $penarikan->keterangan }}</td>
                        <td>{{ $penarikan->code_trans }}</td>
                        <td>{{ $penarikan->tgl_transaksi_view}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
