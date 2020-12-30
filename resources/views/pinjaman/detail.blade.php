<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>
                @if ($pinjaman->anggota)
                    {{ $pinjaman->anggota->nama_anggota }}
                @else
                    -
                @endif
            </td>
            <td>Jenis Pinjaman</td>
            <td>:</td>
            <td>{{ $jenisPinjaman->nama_pinjaman }}</td>
            <td>Besar Pinjaman</td>
            <td>:</td>
            <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Tanggal Peminjaman</td>
            <td>:</td>
            <td>{{ $pinjaman->tgl_entri->format('d M Y') }}</td>
            <td>Lama Angsuran</td>
            <td>:</td>
            <td>{{ $pinjaman->lama_angsuran }}</td>
            <td>Besar Angsuran</td>
            <td>:</td>
            <td>Rp. {{ number_format($pinjaman->besar_angsuran,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Jatuh Tempo</td>
            <td>:</td>
            <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
            <td>Sisa Angsuran</td>
            <td>:</td>
            <td>{{ $pinjaman->sisa_angsuran }}</td>
            <td>Sisa Pinjaman</td>
            <td>:</td>
            <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td class="font-weight-bold">{{ ucwords($pinjaman->statusPinjaman->name) }}</td>
            <td>Administrasi</td>
            <td>:</td>
            <td>Rp. {{ number_format($pinjaman->biaya_administrasi,0,",",".") }}</td>
            <td>Provisi</td>
            <td>:</td>
            <td>Rp. {{ number_format($pinjaman->biaya_provisi,0,",",".") }}</td>
        </tr>
    </table>
</div>

<div class="mt-3 p-2 box-custom">
    <h6 style="font-weight: 600">Angsuran</h6>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Angsuran Ke</th>
                    <th>Angsuran Pokok</th>
                    <th>Jasa</th>
                    <th>Total Angsuran</th>
                    <th>Denda</th>
                    <th>Sisa Pinjaman</th>
                    <th>Dibayar Pada Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->listAngsuran->sortBy('angsuran_ke')->values() as $angsuran)
                    <tr>
                        <td>{{ $angsuran->angsuran_ke }}</td>
                        <td>Rp. {{ number_format($angsuran->besar_angsuran,0,",",".") }}</td>
                        <td>Rp. {{ number_format($angsuran->jasa,0,",",".") }}</td>
                        <td>Rp. {{ number_format($angsuran->besar_angsuran + $angsuran->jasa,0,",",".") }}</td>
                        <td>Rp. {{ number_format($angsuran->denda,0,",",".") }}</td>
                        <td>Rp. {{ number_format($angsuran->sisa_pinjam,0,",",".") }}</td>
                        <td>{{($angsuran->paid_at)?  $angsuran->paid_at->format('d M Y'):'-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>