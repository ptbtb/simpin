<div class="table-responsive">
    <table class="table">
        <tr>
            <th colspan="14">LAPORAN PENDAPATAN DSP TAHUN {{ $request->year }}</th>
        </tr>
        <tr>
            <th colspan="14">PENDAPATAN PINJAMAN BY JENIS PINJAMAN</th>
        </tr>

        <tr>
            <th>No</th>
            <th>Jenis Pendapatan</th>
            @foreach (ARR_BULAN as $key => $val)
                <th>{{ $val }}</th>
            @endforeach
            <th>Grand Total</th>
        </tr>
        @foreach ($pendapatanByJenis as $pendapatan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pendapatan->code->NAMA_TRANSAKSI }}</td>
                @foreach (ARR_BULAN as $key => $val)
                    <th>{{ number_format($pendapatan->saldoMonth[$key], 0, ',', '.') }}</th>
                @endforeach
                <th>{{ number_format($pendapatan->saldo, 0, ',', '.') }}</th>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Grand Total</td>
            @foreach (ARR_BULAN as $key => $val)
                <th>{{ number_format($saldoMonthGroup[$key], 0, ',', '.') }}</th>
            @endforeach
            <th>{{ number_format($pendapatanByJenis->sum('saldo'), 0, ',', '.') }}</th>
        </tr>
    </table>
</div>
