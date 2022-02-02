<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table class="table table-striped" id="pengajuan-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No JKK</th>
                <th>Tanggal Pengajuan</th>
                <th>Nomer Anggota</th>
                <th>Nama Anggota</th>
                <th>Jenis Pinjaman</th>
                <th>Besar Pinjaman</th>
                <th>Status</th>
                <th>Tanggal Acc</th>
                <th>Diajukan Oleh</th>
                <th>Dikonfirmasi Oleh</th>
                <th>Keterangan</th>
                <th>Pembayaran Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listPengajuanPinjaman as $pengajuanPinjaman)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pengajuanPinjaman->no_jkk }}</td>
                    <td>{{ $pengajuanPinjaman->tgl_pengajuan->toDateString() }}</td>
                    <td>{{ $pengajuanPinjaman->kode_anggota }}</td>
                    <td>{{ $pengajuanPinjaman->anggota->nama_anggota }}</td>
                    <td>{{ $pengajuanPinjaman->jenisPinjaman->nama_pinjaman }}</td>
                    <td>{{ $pengajuanPinjaman->besar_pinjam }}</td>
                    <td>{{ $pengajuanPinjaman->statusPengajuan->name }}</td>
                    <td>{{ $pengajuanPinjaman->tgl_acc? $pengajuanPinjaman->tgl_acc->toDateString():'' }}</td>
                    <td>{{ $pengajuanPinjaman->createdBy->name ?? '' }}</td>
                    <td>{{ $pengajuanPinjaman->approvedBy->name ?? '' }}</td>
                    <td>{{ $pengajuanPinjaman->keterangan}}</td>
                    <td>{{ $pengajuanPinjaman->paidByCashier->name ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
