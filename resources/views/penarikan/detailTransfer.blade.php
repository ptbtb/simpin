<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <td style="width: 20%">Kode Anggota</td>
            <td>:</td>
            <td style="width: 30%">{{ $penarikan->anggota->kode_anggota }}</td>
            <td style="width: 20%">Nama Anggota</td>
            <td>:</td>
            <td style="width: 30%">{{ ucwords(strtolower($penarikan->anggota->nama_anggota)) }}</td>
        </tr>
        <tr>
            <td>Besar Pengambilan</td>
            <td>:</td>
            <td>Rp {{ number_format($penarikan->besar_ambil,0,',','.') }}</td>
            <td>Besar Transfer</td>
            <td>:</td>
            <td>Rp {{ number_format($penarikan->besar_ambil,0,',','.') }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>:</td>
            <td>{{ $penarikan->statusPenarikan->name }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
</div>