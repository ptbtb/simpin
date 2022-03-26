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
    <table class="table table-striped">
        <tr>
            <td>Nama</td>
            <td>
                @if ($pinjaman->anggota)
                    {{ $pinjaman->anggota->nama_anggota }}
                @else
                    -
                @endif
            </td>
            <td>Jenis Pinjaman</td>
            <td>{{ $jenisPinjaman->nama_pinjaman }}</td>
            <td>Besar Pinjaman</td>
            <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Tanggal Peminjaman</td>
            <td>
                @if ($pinjaman->tgl_transaksi)
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d',$pinjaman->tgl_transaksi)->format('d M Y') }}
                @endif
            </td>
            <td>Lama Angsuran</td>
            <td>{{ $pinjaman->lama_angsuran }}</td>
            <td>Besar Angsuran</td>
            <td>Rp. {{ number_format($pinjaman->besar_angsuran,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td>
            <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
            <td>Sisa Angsuran</td>
            <td>{{ $pinjaman->sisa_angsuran }}</td>
            <td>Sisa Pinjaman</td>
            <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
        </tr>
        <tr><td>Discount</td>
            <td>{{ number_format($pinjaman->total_discount,0,",",".") }}</td>
            <td>Administrasi</td>
            <td>Rp. {{ number_format($pinjaman->biaya_administrasi,0,",",".") }}</td>
            <td>Provisi</td>
            <td>Rp. {{ number_format($pinjaman->biaya_provisi,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="font-weight-bold"><strong>{{ ucwords($pinjaman->statusPinjaman->name) }}</strong></td>
            <td>Jasa/Bulan</td>
            <td>{{ $pinjaman->jenisPinjaman->jasa*100 }}%</td>
        </tr>
    </table>
    <table class="table table-striped">
        <thead>
            <tr>
                <th><strong>Angsuran Ke</strong></th>
                <th><strong>Angsuran Pokok</strong></th>
                <th><strong>Jasa%</strong></th>
                <th><strong>Jasa</strong></th>
                <th><strong>Total Angsuran</strong></th>
                <th><strong>Periode Pembayaran</strong></th>
                <th><strong>Besar Pembayaran</strong></th>
                <th><strong>Dibayar Pada Tanggal</strong></th>
                <th><strong>Status</strong></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listAngsuran as $angsuran)
                <tr>
                    <td>{{ $angsuran->angsuran_ke }}</td>
                    <td>Rp. {{ number_format($angsuran->besar_angsuran,0,",",".") }}</td>
                    <td>{{ $pinjaman->jenisPinjaman->jasa*100 }}%</td>
                    <td>Rp. {{ number_format($angsuran->jasa,0,",",".") }}</td>
                    <td>Rp. {{ number_format($angsuran->total_angsuran,0,",",".") }}</td>
                    <td>{{ $angsuran->jatuh_tempo->format('m-Y') }}</td>
                    <td>Rp. {{ number_format($angsuran->besar_pembayaran,0,",",".") }}</td>
                    <td>{{($angsuran->tgl_transaksi)?  $angsuran->tgl_transaksi->format('d M Y'):'-' }}</td>
                    <td>{{ $angsuran->statusAngsuran->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
