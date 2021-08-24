<table class="table table-stripped">
    <thead>
        <tr>
            <th>Kode Anggota</th>
            <th>Nama</th>
            <th>Unit Kerja</th>
            <th>Tahun SHU</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($shu as $item)
            <tr>
                <td>{{ $item->kode_anggota }}</td>
                <td>{{ $item->anggota->nama_anggota ?? '-' }}</td>
                <td>{{ $item->anggota->company->nama ?? '-' }}</td>
                <td>{{ $item->year ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
