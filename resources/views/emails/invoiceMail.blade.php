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
    <h4>Dear {{ $details['anggota']->nama_anggota }}</h4>
    <p>{{ $details['invoice']->description }} telah terbit.</p>
    <br>
    <table>
        <tr>
            <th>No Invoice</th>
            <th>Nama Anggota</th>
            <th>Tanggal Terbit</th>
            <th>Besar Tagihan</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
        </tr>
        <tr>
            <td>{{ $details['invoice']->invoice_number }}</td>
            <td>{{ $details['invoice']->anggota->nama_anggota }}</td>
            <td>{{ strtolower($details['invoice']->date->format('d M Y')) }}</td>
            <td>Rp. {{ number_format($details['invoice']->final_amount,0,",",".") }}</td>
            <td>{{ $details['invoice']->due_date->format('d M Y') }}</td>
            <td>{{ ucfirst($details['invoice']->invoiceStatus->name) }}</td>
        </tr>
    </table>
    <br>
    <p>
        Silakan klik tautan berikut <a href="{{ route('invoice-detail', [$details['invoice']->id]) }}">{{ route('invoice-detail', [$details['invoice']->id]) }}</a> untuk melihat detail invoice.
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