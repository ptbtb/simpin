<table class="table" style="font-size: 14px">
    <thead class="thead-dark">
        <tr>
            <th>Akun Debet</th>
            <th>Debet</th>
            <th>Akun Kredit</th>
            <th>Kredit</th>
        </tr>
    </thead>
    <tbody>
        @if ($jurnals->count() == 0)
        <tr>
            <td colspan="4" class="text-center">Jurnal tidak ditemukan</td>
        </tr>
        @else
            @foreach ($jurnals as $jurnal)
                <tr>
                    <td>{{ $jurnal->akun_debet }}</td>
                    <td>Rp. {{ number_format($jurnal->debet, 2, ',', '.') }}</td>
                    <td>{{ $jurnal->akun_kredit }}</td>
                    <td>Rp. {{ number_format($jurnal->kredit, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>