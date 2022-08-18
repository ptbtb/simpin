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
        <th colspan="5" style="text-align: center; font-weight: bold;"> Buku Besar Tanggal {{ \Carbon\Carbon::createFromFormat('Y-m-d',$request->period)->format('d M Y')}}</th>
    </tr>
    <tr>
        <th>No</th>
        <th>Code</th>
        <th>Nama Transaksi</th>
        <th>Tipe</th>
        <th>Saldo Awal</th>
        <th>Trx</th>
        <th>Saldo Akhir</th>
    </tr>
    </thead>
    <tbody>
    @php
        $sumaktiva=0;
        $sumpasiva=0;
        $sumpendapatan=0;
        $sumbeban=0;
    @endphp
    @foreach ($codes->sortBy('CODE') as $bukuBesar)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                {{ $bukuBesar->CODE }}
            </td>
            <td>
                {{ $bukuBesar->NAMA_TRANSAKSI }}
            </td>
            <td>
                {{ $bukuBesar->codeType->name }}
            </td>
            <td>
                {{ $bukuBesar->awal }}
            </td>
            <td>
                {{ $bukuBesar->trx }}
            </td>
            <td>
                {{ $bukuBesar->akhir }}
            </td>
        </tr>

    @endforeach
    <tr>
        <td></td>
        <td>

        </td>
        <td>
            <b>SELISIH</b>
        </td>
        <td>

        </td>
        <td>

        </td>
    </tr>
    <tr>
        <td></td>
        <td>

        </td>
        <td>
            <b></b>
        </td>
        <td>

        </td>
        <td>

        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="5"  style="font-style: italic;">&copy; escndl printed on {{\Carbon\Carbon::now()->format('d M Y his')}}</th>
    </tr>
    </tfoot>
</table>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
