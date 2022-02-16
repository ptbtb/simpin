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
                    <tr>
                        <td>{{ $penarikan->code_trans }}</td>
                        <td>Rp. {{ number_format($penarikan->besar_ambil, 2, ',', '.') }}</td>
                        <td>
                            @if($penarikan->akunDebet)
                                {{ $penarikan->akunDebet->CODE }}
                            @else
                                Bank/Kas
                            @endif</td>
                        <td>Rp. {{ number_format($penarikan->besar_ambil, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
