
<table class="table table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Nama Anggota</th>
            <th>Nomor Anggota</th>
            <th>Tanggal Pinjaman</th>
            <th>Jenis Pinjaman</th>
            <th>Besar Pinjaman</th>
            <th>Saldo Mutasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listPinjaman as $pinjaman)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $pinjaman->kode_pinjam }}</td>
            <td>{{ $pinjaman->nama_anggota }}</td>
            <td>{{ $pinjaman->nomor_anggota }}</td>
            <td>{{ $pinjaman->tgl_entri }}</td>
            <td>{{ $pinjaman->nama_pinjaman }}</td>
            <td>{{ $pinjaman->besar_pinjam }}</td>
            <td>{{ $pinjaman->saldo_mutasi }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
