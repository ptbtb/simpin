
<table class="table table-striped" id="penarikan-table">
    <thead>
        <tr>
            <th>Tanggal Penarikan</th>
            <th>Nama Anggota</th>
            <th>Jenis Simpanan</th>
            <th>Besar Penarikan</th>
            <th>Status</th>
            <th>Tanggal Acc</th>
            <th>Diajukan Oleh</th>
            <th>Dikonfirmasi Oleh</th>
            <th>Pembayaran Oleh</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listPenarikan as $penarikan)
            <tr>
                <td>{{ $penarikan->tgl_ambil->toDateString() }}</td>
                <td>{{ $penarikan->anggota->nama_anggota }}</td>
                <td>{{ $penarikan->jenisSimpanan->nama_simpanan }}</td>
                <td>{{ $penarikan->besar_ambil }}</td>
                <td>{{ $penarikan->statusPenarikan->name }}</td>
                <td>{{ $penarikan->tgl_acc }}</td>
                <td>{{ $penarikan->createdBy->name ?? '' }}</td>
                <td>{{ $penarikan->approvedBy->name ?? '' }}</td>
                <td> {{ $penarikan->paidByCashier->name ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
