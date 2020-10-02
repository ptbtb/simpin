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
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-3">
                <h6>Bukti Pengambilan Tunai</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-12">Tanggal: {{ $penarikan->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="row">
            
            <div class="col-12">Waktu: {{ $penarikan->created_at->format('H:i:s') }}</div>
        </div>
        <div class="row">
            
            <div class="col-12">No. Record: {{ $penarikan->kode_ambil.$penarikan->created_at->format('dmY') }}</div>
        </div>
        <div class="row">
            <div class="col-md-12 mt-3">Penarikan : Rp. {{ number_format($penarikan->besar_ambil,0,",",".") }}</div>
        </div>
        <div class="row">
            <div class="col-md-12">Sisa Saldo : Rp. {{ number_format($penarikan->tabungan->besar_tabungan,0,",",".") }}</div>
        </div>
        <div class="row">
            <div class="col-12 text-center mt-3">
                <p>Terima Kasih</p>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>