<style>
    .text-center{
        text-align: center;
    }

    table tr td{
        font-size: 12px;
    }
    
    .border-collapse, .border-collapse tr td, .border-collapse tr th {
        border: 1px solid black;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td{
        text-align: center;
        font-size: 12px;
    }

    body{
        font-size: 12px;
    }

    h5{
        font-size: 12px;
    }
    .page-break {
        page-break-after: always;
    }
</style>
<body>
    <h5 style="margin: 0">REKAP PEMBAYARAN SIMPANAN&UTIP DAERAH</h5>
    <h5 style="margin-top: 0">TGL {{ \Carbon\Carbon::now()->format('d F Y') }}</h5>

    <table class="border-collapse">
        <tr>
            <th>No</th>
            <th>Trx Reference</th>
            <th>Credited Account Name</th>
            <th>Bank Name Country</th>
            <th>Credited Transfer</th>
            <th>Remark</th>
            <th>Debited Account</th>
            <th>Debited Amount</th>
        </tr>
        @php
            $totalcredited = 0;
            $totaldebited = 0;
        @endphp
        @foreach ($listPengajuan as $pengajuan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pengajuan->kode_pengajuan }}</td>
                <td>{{ strtoupper($pengajuan->anggota->nama_anggota) }}</td>
                <td>{{ strtoupper($settings[COMPANY_SETTING_BANK_NAME]) }}</td>
                <td>{{ "Rp " . number_format($pengajuan->pinjaman->pinjamanDitransfer,2,',','.') }}</td>
                <td>{{ strtoupper($pengajuan->jenisPinjaman->nama_pinjaman) }}</td>
                <td>{{ $settings[COMPANY_SETTING_BANK_ACCOUNT] }}</td>
                <td>{{ "Rp " . number_format($pengajuan->pinjaman->pinjamanDitransfer,2,',','.') }}</td>
            </tr>
            @php
                $totalcredited += $pengajuan->pinjaman->pinjamanDitransfer;
                $totaldebited += $pengajuan->pinjaman->pinjamanDitransfer;
            @endphp
        @endforeach
        <tr>
            <td></td>
            <td colspan="3">JUMLAH</td>
            <td>{{ "Rp " . number_format($totalcredited,2,',','.') }}</td>
            <td colspan="2"></td>
            <td>{{ "Rp " . number_format($totaldebited,2,',','.') }}</td>
        </tr>
    </table>
    <br><br><br>
    <table>
        <tr>
            <td colspan="3" style="text-align: right">Jakarta, {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: left">Mengetahui</td>
        </tr>
        <tr>
            <td style="text-align: left">
                <i style="color: transparent">.</i> Bendahara <br><br><br><br><br>
                (....................)
            </td>
            <td style="text-align: right">
                Verifikator <i style="color: transparent">..</i><br><br><br><br><br>
                ({{ \Auth::user()->name }})
            </td>
            <td>
                Maker <br><br><br><br><br>
                (....................)
            </td>
        </tr>
    </table>
</body>