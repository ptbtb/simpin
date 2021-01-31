<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration Completed</title>
    <style>
        table, td, th {
            border: 1px solid black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td{
            text-align: center;
        }
    </style>
</head>
<body>
    <h4>Dear {{ $details['user']->name }}</h4>
    <p>{{ $details['penarikan']->anggota->nama_anggota }} telah mengajukan penarikan kepada koperasi dengan detail sebagai berikut:</p>
    <br>
    <table>
        <tr>
            <th>Tanggal Pengajuan</th>
            <th>Nama Anggota</th>
            <th>Jenis Penarikan</th>
            <th>Besar Penarikan</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{ $details['penarikan']->tgl_ambil->format('d M Y') }}</td>
            <td>{{ $details['penarikan']->anggota->nama_anggota }}</td>
            <td>{{ ucwords(strtolower($details['penarikan']->tabungan->jenisSimpanan->nama_simpanan)) }}</td>
            <td>Rp. {{ number_format($details['penarikan']->besar_ambil,0,",",".") }}</td>
            <td>{{ ucfirst($details['penarikan']->statusPenarikan->name) }}</td>
        </tr>
    </table>
    <br>
    <p>
        Silakan klik tautan berikut <a href="{{ route('penarikan-index') }}">{{ route('penarikan-index') }}</a> untuk melihat detail penarikan.
    </p>
    <p>Terima Kasih</p>
    <br>
    <p>
        Admin SIMPIN <br>
        <img src="{{ asset('img/new-logo.jpg') }}" style="width: 110px"><br>
        {{-- <img src="https://simpin.kopegmar.co.id/img/new-logo.jpg" style="width: 110px"><br> --}}
        KOPEGMAR  Tj Priok  <br>
        Jl. Cempaka No.14, RT.4/RW.12,  <br>
        Rawabadak Utara, <br>
        Kec. Koja, Kota Jkt Utara,  <br>
        Daerah Khusus Ibukota Jakarta 14230 <br>
        Hotline WA : 08111821414  <br>
        Cs email : uspkopegmar@yahoo.co.id <br>
    </p>
</body>
</html>