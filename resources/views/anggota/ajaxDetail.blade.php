<table class="table">
    <tr>
        <td>Kode Anggota</td>
        <td>:</td>
        <td>{{ ($anggota->kode_anggota)? $anggota->kode_anggota:'-' }}</td>
        <td>Nama Anggota</td>
        <td>:</td>
        <td>{{ ($anggota->nama_anggota)? $anggota->nama_anggota:'-' }}</td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td>:</td>
        <td>{{ ($anggota->alamat_anggota)? $anggota->alamat_anggota:'-' }}</td>
        <td>Jenis Kelamin</td>
        <td>:</td>
        <td>{{ ($anggota->jenis_kelamin)? $anggota->jenis_kelamin:'-' }}</td>
    </tr>
    <tr>
        <td>No Rek</td>
        <td>:</td>
        <td>{{ ($anggota->no_rek)? $anggota->no_rek:'-' }}</td>
        <td>NIPP</td>
        <td>:</td>
        <td>{{ ($anggota->nipp)? $anggota->nipp:'-' }}</td>
    </tr>
    <tr>
        <td>Lokasi Kerja</td>
        <td>:</td>
        <td>{{ ($anggota->lokasi_kerja)? $anggota->lokasi_kerja:'-' }}</td>
        <td>Telepon</td>
        <td>:</td>
        <td>{{ ($anggota->telp)? $anggota->telp:'-' }}</td>
    </tr> 
    <tr>
        <td>Status</td>
        <td>:</td>
        <td>{{ ($anggota->status)? $anggota->status:'-' }}</td>
        <td colspan="3"></td>
    </tr>
</table>