
<table class="table table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Nomor Anggota</th>
            <th>Tanggal Pinjaman</th>
            <th>Kode Pinjaman</th>
            <th>Besar Pinjaman</th>
            <th>Saldo Mutasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listPinjaman as $pinjaman)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $pinjaman->kode_pinjam }}</td>
            <td>{{ $pinjaman->kode_anggota }}</td>
            <td>{{ $pinjaman->tgl_entri }}</td>
            <td>{{ $pinjaman->kode_jenis_pinjam}}</td>
            <td>{{ $pinjaman->besar_pinjam }}</td>
            <td>{{ $pinjaman->saldo_mutasi }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
