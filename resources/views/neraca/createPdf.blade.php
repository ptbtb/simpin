<style>
    .text-center{
        text-align: center;
    }

    table tr{
        font-size: 12px;
        padding: 2.5px;
    }

    table td{
        padding: 2.5px;
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
        font-size: 11px;
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
    @page {
        margin-top: 5px;
    }
</style>
<body>
    <table>
        <tr>
            <td style="width:17%; text-align: left">Koperasi Pegawai Maritim</td>
            <td></td>
            <td style="width:15%; text-align: left">Rep. ID : Neraca</td>
        </tr>
        <tr>
            <td style="text-align: left">JAKARTA</td>
            <td style="font-size: 16px"><b>N  E  R  A  C  A</b></td>
            <td style="text-align: left">Tanggal : {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td style="text-align: left">Unit Simpan/Pinjam</td>
            <td>Bulan : {{ strtoupper(\Carbon\Carbon::createFromFormat('m-Y', $period)->locale('id_ID')->isoFormat('MMMM Y')) }}</td>
            <td style="text-align: left">Halaman : 1/1</td>
        </tr>
    </table>
    <br>
    <table>
        <tr class="border-collapse">
            <td class="border-collapse" colspan="4">Aktiva</td>
            <td class="border-collapse" colspan="4">Pasiva</td>
        </tr>
        <tr class="border-collapse">
            <td style="width:3%; text-align: left; border-left: 1px solid black; border-bottom: 1px solid black;">Rek</td>
            <td style="width:27%; text-align: left; border-bottom: 1px solid black;">Nama Rekening</td>
            <td style="width:10%; text-align: right" class="border-collapse">Bulan ini</td>
            <td style="width:10%; text-align: right" class="border-collapse">Bulan lalu</td>
            <td style="width:3%; text-align: left; border-bottom: 1px solid black;">Rek</td>
            <td style="width:27%; text-align: left; border-bottom: 1px solid black;">Nama Rekening</td>
            <td style="width:10%; text-align: right" class="border-collapse">Bulan ini</td>
            <td style="width:10%; text-align: right" class="border-collapse">Bulan lalu</td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black;"><b>AKTIVA LANCAR</b></td>
            <td style="text-align: right; border-right: 1px solid black;"></td>
            <td style="text-align: right; border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black;"><b>KEWAJIBAN DAN KEKAYAAN BERSIH</b></td>
            <td style="text-align: right; border-right: 1px solid black;"></td>
            <td style="text-align: right; border-right: 1px solid black;"></td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[0]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[0]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[0]['saldo'] < 0)
                    ({{ number_format($aktivas[0]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[0]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[0]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[0]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[0]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[0]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[0]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[0]['saldo'] < 0)
                    ({{ number_format($passivas[0]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[0]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[0]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[0]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[0]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[1]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[1]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[1]['saldo'] < 0)
                    ({{ number_format($aktivas[1]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[1]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[1]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[1]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[1]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[1]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[1]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[1]['saldo'] < 0)
                    ({{ number_format($passivas[1]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[1]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[1]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[1]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[1]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[2]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[2]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[2]['saldo'] < 0)
                    ({{ number_format($aktivas[2]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[2]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[2]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[2]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[2]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[2]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[2]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[2]['saldo'] < 0)
                    ({{ number_format($passivas[2]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[2]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[2]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[2]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[2]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[3]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[3]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[3]['saldo'] < 0)
                    ({{ number_format($aktivas[3]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[3]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[3]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[3]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[3]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[3]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[3]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[3]['saldo'] < 0)
                    ({{ number_format($passivas[3]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[3]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[3]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[3]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[3]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[4]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[4]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[4]['saldo'] < 0)
                    ({{ number_format($aktivas[4]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[4]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[4]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[4]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[4]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[4]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[4]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[4]['saldo'] < 0)
                    ({{ number_format($passivas[4]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[4]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[4]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[4]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[4]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[5]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[5]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[5]['saldo'] < 0)
                    ({{ number_format($aktivas[5]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[5]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[5]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[5]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[5]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[5]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[5]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[5]['saldo'] < 0)
                    ({{ number_format($passivas[5]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[5]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[5]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[5]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[5]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[6]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[6]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[6]['saldo'] < 0)
                    ({{ number_format($aktivas[6]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[6]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[6]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[6]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[6]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[6]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[6]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[6]['saldo'] < 0)
                    ({{ number_format($passivas[6]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[6]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[6]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[6]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[6]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[7]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[7]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[7]['saldo'] < 0)
                    ({{ number_format($aktivas[7]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[7]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[7]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[7]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[7]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[7]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[7]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[7]['saldo'] < 0)
                    ({{ number_format($passivas[7]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[7]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[7]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[7]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[7]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[8]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[8]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[8]['saldo'] < 0)
                    ({{ number_format($aktivas[8]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[8]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[8]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[8]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[8]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[8]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[8]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[8]['saldo'] < 0)
                    ({{ number_format($passivas[8]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[8]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[8]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[8]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[8]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[9]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[9]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[9]['saldo'] < 0)
                    ({{ number_format($aktivas[9]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[9]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[9]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[9]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[9]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[9]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[9]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[9]['saldo'] < 0)
                    ({{ number_format($passivas[9]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[9]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[9]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[9]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[9]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH AKTIVA LANCAR</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldoAktivaLancar = $aktivas->where('rekGroup', 1)->sum('saldo');
                    $jumlahSaldoLastMonthAktivaLancar = $aktivas->where('rekGroup', 1)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldoAktivaLancar < 0)
                    ({{ number_format($jumlahSaldoAktivaLancar * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoAktivaLancar, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonthAktivaLancar < 0)
                    ({{ number_format($jumlahSaldoLastMonthAktivaLancar * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonthAktivaLancar, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[10]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[10]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[10]['saldo'] < 0)
                    ({{ number_format($passivas[10]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[10]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[10]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[10]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[10]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"><b>AKTIVA TETAP</b></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[11]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[11]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[11]['saldo'] < 0)
                    ({{ number_format($passivas[11]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[11]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[11]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[11]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[11]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[10]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[10]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[10]['saldo'] < 0)
                    ({{ number_format($aktivas[10]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[10]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[10]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[10]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[10]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH KEWAJIBAN DAN KEKAYAAN BERSIH</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldoKewajibanKekayaan = $passivas->where('rekGroup', 4)->sum('saldo');
                    $jumlahSaldoLastMonthKewajibanKekayaan = $passivas->where('rekGroup', 4)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldoKewajibanKekayaan < 0)
                    ({{ number_format($jumlahSaldoKewajibanKekayaan * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoKewajibanKekayaan, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonthKewajibanKekayaan < 0)
                    ({{ number_format($jumlahSaldoLastMonthKewajibanKekayaan * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonthKewajibanKekayaan, 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[11]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[11]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[11]['saldo'] < 0)
                    ({{ number_format($aktivas[11]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[11]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[11]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[11]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[11]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[12]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[12]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[12]['saldo'] < 0)
                    ({{ number_format($aktivas[12]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[12]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[12]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[12]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[12]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[12]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[12]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[12]['saldo'] < 0)
                    ({{ number_format($passivas[12]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[12]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[12]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[12]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[12]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[13]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[13]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[13]['saldo'] < 0)
                    ({{ number_format($aktivas[13]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[13]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[13]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[13]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[13]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[13]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[13]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[13]['saldo'] < 0)
                    ({{ number_format($passivas[13]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[13]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[13]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[13]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[13]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH AKTIVA TETAP</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldoAktivaTetap = $aktivas->where('rekGroup', 2)->sum('saldo');
                    $jumlahSaldoLastMonthAktivaTetap = $aktivas->where('rekGroup', 2)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldoAktivaTetap < 0)
                    ({{ number_format($jumlahSaldoAktivaTetap * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoAktivaTetap, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonthAktivaTetap < 0)
                    ({{ number_format($jumlahSaldoLastMonthAktivaTetap * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonthAktivaTetap, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldo5 = $passivas->where('rekGroup', 5)->sum('saldo');
                    $jumlahSaldoLastMonth5 = $passivas->where('rekGroup', 5)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldo5 < 0)
                    ({{ number_format($jumlahSaldo5 * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldo5, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonth5 < 0)
                    ({{ number_format($jumlahSaldoLastMonth5 * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonth5, 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"><br></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($aktivas[14]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $aktivas[14]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[14]['saldo'] < 0)
                    ({{ number_format($aktivas[14]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[14]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($aktivas[14]['saldoLastMonth'] < 0)
                    ({{ number_format($aktivas[14]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($aktivas[14]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[14]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[14]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[14]['saldo'] < 0)
                    ({{ number_format($passivas[14]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[14]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[14]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[14]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[14]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldoAktivaLain = $aktivas->where('rekGroup', 3)->sum('saldo');
                    $jumlahSaldoLastMonthAktivaLain = $aktivas->where('rekGroup', 3)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldoAktivaLain < 0)
                    ({{ number_format($jumlahSaldoAktivaLain * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoAktivaLain, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonthAktivaLain < 0)
                    ({{ number_format($jumlahSaldoLastMonthAktivaLain * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonthAktivaLain, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[15]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[15]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[15]['saldo'] < 0)
                    ({{ number_format($passivas[15]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[15]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[15]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[15]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[15]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[16]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[16]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[16]['saldo'] < 0)
                    ({{ number_format($passivas[16]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[16]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[16]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[16]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[16]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[17]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[17]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[17]['saldo'] < 0)
                    ({{ number_format($passivas[17]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[17]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[17]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[17]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[17]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black;">{{ substr($passivas[18]['code']->CODE, 0, 3) }}</td>
            <td style="text-align: left; border-right: 1px solid black;">{{ $passivas[18]['code']->NAMA_TRANSAKSI }}</td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[18]['saldo'] < 0)
                    ({{ number_format($passivas[18]['saldo'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[18]['saldo'], 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right; border-right: 1px solid black;">
                @if($passivas[18]['saldoLastMonth'] < 0)
                    ({{ number_format($passivas[18]['saldoLastMonth'] * -1, 0, ',', '.') }})
                @else
                    {{ number_format($passivas[18]['saldoLastMonth'], 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="border-left: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="border-right: 1px solid black;"></td>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;">JUMLAH</td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $jumlahSaldo6 = $passivas->where('rekGroup', 6)->sum('saldo');
                    $jumlahSaldoLastMonth6 = $passivas->where('rekGroup', 6)->sum('saldoLastMonth');
                @endphp
                @if($jumlahSaldo6 < 0)
                    ({{ number_format($jumlahSaldo6 * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldo6, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($jumlahSaldoLastMonth6 < 0)
                    ({{ number_format($jumlahSaldoLastMonth6 * -1, 0, ',', '.') }})
                @else
                    {{ number_format($jumlahSaldoLastMonth6, 0, ',', '.') }}
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"><b>Jumlah Aktiva</b></td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $totalSaldoAktiva = $aktivas->sum('saldo');
                    $totalSaldoLastMonthAktiva = $aktivas->sum('saldoLastMonth');
                @endphp
                @if($totalSaldoAktiva < 0)
                    ({{ number_format($totalSaldoAktiva * -1, 0, ',', '.') }})
                @else
                    {{ number_format($totalSaldoAktiva, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($totalSaldoLastMonthAktiva < 0)
                    ({{ number_format($totalSaldoLastMonthAktiva * -1, 0, ',', '.') }})
                @else
                    {{ number_format($totalSaldoLastMonthAktiva, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: left; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"></td>
            <td style="text-align: left; border-right: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black;"><b>Jumlah Pasiva</b></td>
            <td style="text-align: right" class="border-collapse">
                @php
                    $totalSaldoPasiva = $passivas->sum('saldo');
                    $totalSaldoLastMonthpasiva = $passivas->sum('saldoLastMonth');
                @endphp
                @if($totalSaldoPasiva < 0)
                    ({{ number_format($totalSaldoPasiva * -1, 0, ',', '.') }})
                @else
                    {{ number_format($totalSaldoPasiva, 0, ',', '.') }}
                @endif
            </td>
            <td style="text-align: right" class="border-collapse">
                @if($totalSaldoLastMonthpasiva < 0)
                    ({{ number_format($totalSaldoLastMonthpasiva * -1, 0, ',', '.') }})
                @else
                    {{ number_format($totalSaldoLastMonthpasiva, 0, ',', '.') }}
                @endif
            </td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td colspan="6" style="text-align: center">JAKARTA, {{ strtoupper(\Carbon\Carbon::createFromFormat('m-Y', $period)->locale('id_ID')->isoFormat('MMMM Y')) }}</td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center">PENGURUS KOPEPASI PEGAWAI MARITIM TANJUNG PRIOK</td>
        </tr>
        <tr>
            <td style="width: 20%"></td>
            <td style="text-align: center">
                KETUA<br><br><br><br><br>
                DJUSMAN HI UMAR
            </td>
            <td style="text-align: center">
                WAKIL KETUA<br><br><br><br><br>
                M. FAJAR SUHARDIMAN
            </td>
            <td style="text-align: center">
                SEKRETARIS<br><br><br><br><br>
                AINUL
            </td>
            <td style="text-align: center">
                BENDAHARA<br><br><br><br><br>
                ARDIANSYAH
            </td>
            <td style="width: 20%"></td>
        </tr>
    </table>
</body>
    