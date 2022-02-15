<style>
    .text-center{
        text-align: center;
    }

    table tr td{
        font-size: 14px;
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
        font-size: 14px;
    }

    body{
        font-size: 14px;
    }

    h5{
        font-size: 12px;
    }
    .page-break {
        page-break-after: always;
    }
</style>
<body>
    <table class="" cellspacing="0" cellpadding="5" style="border:none;">
        <tr>
            <td style="text-align: left ; width:25%">Koperasi Pegawai Maritim</td>
            <td></td>
            <td style="text-align: left; width:25%">Rep. ID : B_JU</td>
        </tr>
        <tr>
            <td style="text-align: left">JAKARTA</td>
            <td></td>
            <td style="text-align: left">Tanggal : {{ $tgl_print }}</td>
        </tr>
        <tr>
            <td style="text-align: left">Unit Simpan Pinjam</td>
            <td><b><span style="font-size:18px; text-align:center">JURNAL UMUM</span></b></td>
            <td></td>
        </tr>
    </table>
    <table class="" cellspacing="0" cellpadding="5" style="border:1px solid black">
        <tr>
            <td style="text-align: left; width:25%">Bukti</td>
            <td style="text-align: left; width:5%">:</td>
            <td style="text-align: left">JR {{ $jurnalUmum->serial_number_view }} &nbsp;&nbsp;&nbsp; Tanggal : {{ $jurnalUmum->created_at->format('d F Y') }}</td>
        </tr>
        <tr>
            <td style="text-align: left">Total jurnal transaksi</b></td>
            <td style="text-align: left">:</td>
            <td style="text-align: left"><b>Rp. {{ $jurnalUmum->total_nominal_debet_rupiah }}</td>
        </tr>
        <tr>
            <td style="text-align: left">Terbilang</td>
            <td style="text-align: left">:</td>
            <td style="text-align: left"># {{ $terbilang }} #</td>
        </tr>
        <tr>
            <td style="text-align: left">Uraian</td>
            <td style="text-align: left">:</td>
            <td style="text-align: left">{{ $jurnalUmum->deskripsi }}</td>
        </tr>
    </table>
    <table cellspacing="0" cellpadding="5">
        <tr>
          <td style="padding:0">
            <table class="" cellspacing="0" cellpadding="5" style="border:1px solid black;">
                <tr style="border-bottom: 1px solid black">
                    <th style="width:75%">Rekening</th>
                    <th></th>
                    <th style="text-align:right">Debet</th>
                </tr>
                @foreach ($jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_DEBET) as $itemDebet)
                    <tr>
                        <td>{{ $itemDebet->code->CODE }}</td>
                        <td>Rp.</td>
                        <td style="text-align:right">{{ $itemDebet->nominal_rupiah }}</td>
                    </tr>
                @endforeach
                @for ($i = 1; $i < 5; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </table>
          </td>
          <td style="padding:0">
            <table class="" cellspacing="0" cellpadding="5" style="border:1px solid black;">
                <tr style="border-bottom: 1px solid black">
                    <th style="width:75%">Rekening</th>
                    <th></th>
                    <th style="text-align:right">Kredit</th>
                </tr>
                @foreach ($jurnalUmum->jurnalUmumItems->where('normal_balance_id', NORMAL_BALANCE_KREDIT) as $itemKredit)
                    <tr>
                        <td>{{ $itemKredit->code->CODE }}</td>
                        <td>Rp.</td>
                        <td style="text-align:right">{{ $itemKredit->nominal_rupiah }}</td>
                    </tr>
                @endforeach
                @for ($i = 1; $i < 5; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </table>
          </td>
        </tr>
    </table>
    <table class="" cellspacing="0" cellpadding="5" style="border:1px solid black;">
        <tr>
            <td colspan="2" style="text-align: center; border:1px solid black">TELAH DIPERIKSA</td>
            <td colspan="3" style="text-align: left; "><i>Mengetahui :</i></td>
        </tr>
        <tr>
            <td style="text-align: left; border:1px solid black">Oleh :</td>
            <td style="text-align: center; border:1px solid black">PARAF</td>
            <td colspan="3" style="text-align: right">JAKARTA, {{ \Carbon\Carbon::now()->format('d F Y') }}</td>
        </tr>
        <tr style="border-bottom:1px solid black">
            <td style="text-align: left; border:1px solid black">
                <i style="color: transparent">.</i> Asman Pendanaan <br><br><br><br><br>
                Asman Pembiayaan
            </td>
            <td style="border:1px solid black"></td>
            <td style="text-align: center">
                <i style="color: transparent">.</i> KETUA <br><br><br><br><br>
                <u>DJUSMAN HI UMAR</u>
                <br>
                NIPP : 272076315
            </td>
            <td style="text-align: center">
                <i style="color: transparent">.</i> BENDAHARA <br><br><br><br><br>
                <u>ARDIANSYAH</u>
                <br>
                NIPP : 276076919
            </td>
            <td style="text-align: center">
                <i style="color: transparent">.</i> Yang Menerima, <br><br><br><br><br>
                (............................)
                <br>
                Nama Terang
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;">No Posting : {{ $jurnalUmum->serial_number_view }}</td>
            <td colspan="3" style="text-align: center; border-top:1px solid black">Paraf Petugas Posting : </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;">Tgl Posting : {{ $jurnalUmum->view_tgl_transaksi }}</td>
            <td colspan="3" style="text-align: center"></td>
        </tr>
    </table>
</body>
