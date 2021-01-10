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
    <p>Berikut adalah daftar pengajuan pinjaman yang menunggu pembayaran:</p>
    <br>
    <table>
        <tr>
            <th>Tanggal Pengajuan</th>
            <th>Nama Anggota</th>
            <th>Jenis Pinjaman</th>
            <th>Besar Pinjaman</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{ $details['pengajuan']->tgl_pengajuan->format('d M Y') }}</td>
            <td>{{ $details['pengajuan']->anggota->nama_anggota }}</td>
            <td>{{ ucwords(strtolower($details['pengajuan']->jenisPinjaman->nama_pinjaman)) }}</td>
            <td>Rp. {{ number_format($details['pengajuan']->besar_pinjam,0,",",".") }}</td>
            <td>{{ ucfirst($details['pengajuan']->statusPengajuan->name) }}</td>
        </tr>
    </table>
    <br>
    <p>
        Silakan klik tautan berikut <a href="{{ route('pengajuan-pinjaman-list') }}">{{ route('pengajuan-pinjaman-list') }}</a> untuk melihat detail pengajuan.
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