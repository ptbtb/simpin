<table class="table">
    <tr>
        <td>Nama Anggota</td>
        <td>:</td>
        <td>{{ ($anggota->nama_anggota)? $anggota->nama_anggota:'-' }}</td>
        <td>Alamat</td>
        <td>:</td>
        <td>{{ ($anggota->alamat_anggota)? $anggota->alamat_anggota:'-' }}</td>
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
        <td>Jenis Kelamin</td>
        <td>:</td>
        <td>{{ ($anggota->jenis_kelamin)? $anggota->jenis_kelamin:'-' }}</td>
        <td>Lokasi Kerja</td>
        <td>:</td>
        <td>{{ ($anggota->lokasi_kerja)? $anggota->lokasi_kerja:'-' }}</td>
    </tr>
    <tr>
        <td>Telepon</td>
        <td>:</td>
        <td>{{ ($anggota->telp)? $anggota->telp:'-' }}</td>
        <td>Lokasi Kerja</td>
        <td>:</td>
        <td>{{ ($anggota->status)? $anggota->status:'-' }}</td>
    </tr> 
</table>