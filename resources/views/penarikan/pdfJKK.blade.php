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
    <h5 style="margin: 0">DAFTAR TRANSFER PENARIKAN SIMPANAN </h5>
    <h5 style="margin-top: 0">TGL {{  $tgl_print->format('d F Y') }}</h5>

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
        @foreach ($listPenarikan as $penarikan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $penarikan->kode_anggota }}</td>
                <td>{{ strtoupper($penarikan->anggota->nama_anggota) }}</td>
                <td>{{ strtoupper($settings[COMPANY_SETTING_BANK_NAME]) }}</td>
                <td>{{ "Rp " . number_format($penarikan->besar_ambil,2,',','.') }}</td>
                <td>PENARIKAN {{ strtoupper($jenisSimpanan->where('kode_jenis_simpan', $penarikan->code_trans)->first()->nama_simpanan) }}</td>
                <td>{{ $settings[COMPANY_SETTING_BANK_ACCOUNT] }}</td>
                <td>{{ "Rp " . number_format($penarikan->besar_ambil,0,',','.') }}</td>
            </tr>
            @php
                $totalcredited += $penarikan->besar_ambil;
                $totaldebited += $penarikan->besar_ambil;
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
                (...................)
            </td>
            <td>
                Maker <br><br><br><br><br>
                ({{ \Auth::user()->name }})
            </td>
        </tr>
    </table>
    @if (isset($reprint))
        <label style="font-size: 8px; font-weight: 700; position: absolute; bottom: 0; right: 0">Reprint JKK</label>
    @endif
</body>
