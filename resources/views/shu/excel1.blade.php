<table class="table table-stripped">
    <thead>
        <tr>
            <th>Kode Anggota</th>
            <th>Nama</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->kode_anggota }}</td>
                <td>{{ $item->nama_anggota }}</td>
                <td>{{ $item->amount }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
