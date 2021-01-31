<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <td style="width: 20%">Kode Anggota</td>
            <td>:</td>
            <td style="width: 30%">{{ $pinjaman->anggota->kode_anggota }}</td>
            <td style="width: 20%">Nama Anggota</td>
            <td>:</td>
            <td style="width: 30%">{{ ucwords(strtolower($pinjaman->anggota->nama_anggota)) }}</td>
        </tr>
        <tr>
            <td>Jenis Pinjaman</td>
            <td>:</td>
            <td>{{ ucwords(strtolower($jenisPinjaman->nama_pinjaman)) }}</td>
            <td>Tanggal Pinjaman</td>
            <td>:</td>
            <td>{{ $pinjaman->pengajuan->tgl_pengajuan->format('d F Y') }}</td>
        </tr>
        <tr>
            <td>Lama Angsuran</td>
            <td>:</td>
            <td>{{ $pinjaman->lama_angsuran }} bulan</td>
            <td>Sisa Angsuran</td>
            <td>:</td>
            <td>{{ $pinjaman->sisa_angsuran }} bulan</td>
        </tr>
        <tr>
            <td>Besar Pinjaman</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->besar_pinjam,2,',','.') }}</td>
            <td>Besar Angsuran</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->besar_angsuran,2,',','.') }}</td>
        </tr>
        <tr>
            <td>Administrasi</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->biaya_administrasi,2,',','.') }}</td>
            <td>Provisi</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->biaya_provisi,2,',','.') }}</td>
        </tr>
        <tr>
            <td>Total Topup Pinjaman</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->totalPinjamanTopup,2,',','.') }}</td>
            <td>Total Biaya di Transfer</td>
            <td>:</td>
            <td>{{ "Rp " . number_format($pinjaman->pinjamanDitransfer,2,',','.') }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $pinjaman->pengajuan->statusPengajuan->name }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>