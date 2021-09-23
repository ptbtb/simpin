
<table class="table" style="border-collapse:">
    <tr>
        <td style="width:17%; text-align: left">Koperasi Pegawai Maritim</td>
        <td></td>
        <td style="width:15%; text-align: left"></td>
    </tr>
    <tr>
        <td style="text-align: left">JAKARTA</td>
        <td style="font-size: 16px; text-align:center"><b>LAPORAN ARUS KAS</b></td>
        <td style="text-align: left"></td>
    </tr>
    <tr>
        <td style="text-align: left">Divisi Simpan/Pinjam</td>
        <td style="text-align:center">Periode : {{ $startPeriod }} s/d {{ $endPeriod }}</td>
        <td style="text-align: left">&nbsp;</td>
    </tr>
</table>
<br>
<table class="table" style="border-collapse: collapse; border: 1px solid black;">
    <tr>
        <th style="border: 1px solid black; text-align:center">Rekening</th>
        <th style="border: 1px solid black; text-align:center">Uraian</th>
        <th style="border: 1px solid black; text-align:center">Jumlah</th>
    </tr>
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">Saldo Awal</td>
        <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($saldoAwal, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">PENERIMAAN</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    @foreach ($dataPenerimaan as $penerimaan)
        <tr>
            <td style="text-align: center">{{ $penerimaan['code'] }}</td>
            <td style="border-left: 1px solid black; border-right: 1px solid black;">{{ $penerimaan['uraian'] }}</td>
            <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($penerimaan['nominal'], 0, ',', '.') }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">TOTAL PENERIMAAN</td>
        <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">PENGELUARAN</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    @foreach ($dataPengeluaran as $pengeluaran)
        <tr>
            <td style="text-align: center">{{ $pengeluaran['code'] }}</td>
            <td style="border-left: 1px solid black; border-right: 1px solid black;">{{ $pengeluaran['uraian'] }}</td>
            <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($pengeluaran['nominal'], 0, ',', '.') }}</td>
        </tr>
    @endforeach
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">TOTAL PENGELUARAN</td>
        <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;"></td>
    </tr>
    <tr>
        <td></td>
        <td style="border-left: 1px solid black; border-right: 1px solid black;">SALDO AKHIR</td>
        <td style="text-align: right; border-left: 1px solid black; border-right: 1px solid black;">{{ number_format($saldoAkhir, 0, ',', '.') }}</td>
    </tr>
</table>
