<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px
        }
        #table{
            width: 100%;
            border-collapse: collapse;
        }

        #table tr, #table th, #table td{
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
        }
        #table th {
            text-align: center;
            color: black;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $shu->anggota->nama_anggota ?? '-' }}</td>
        </tr>
        <tr>
            <td>NIPP</td>
            <td>:</td>
            <td>{{ $shu->anggota->nipp ?? '-' }}</td>
        </tr>
        <tr>
            <td>No Anggota</td>
            <td>:</td>
            <td>{{ $shu->anggota->kode_anggota ?? '-' }}</td>
        </tr>
        <tr>
            <td>Unit Kerja</td>
            <td>:</td>
            <td>{{ $shu->anggota->company->name ?? '-' }}</td>
        </tr>
    </table>

    <h5 style="margin-top: 1rem; margin-bottom: 1rem">SHU TAHUN BUKU {{ $shu->year ?? '-' }}</h5>

    <table style="width: 100%;" id="table">
        <tr>
            <th rowspan="2">Bulan</th>
            <th colspan="5">Simpanan Pokok Wajib Sukarela (PWS)</th>
            <th colspan="2">Simpanan Khusus</th>
            <th rowspan="2">Cashback</th>
            <th rowspan="2">Kontribusi</th>
            <th rowspan="2">Total SHU Sebelum Pajak</th>
            <th rowspan="2">Pajak (Pph 21)</th>
            <th rowspan="2">Total SHU Setelah Pajak</th>
            <th>SHU Disimpan</th>
            <th>SHU Dibagi</th>
        </tr>
        <tr>
            <th>Pokok</th>
            <th>Wajib</th>
            <th>Sukarela</th>
            <th>Saldo</th>
            <th>SHU</th>
            <th>Saldo</th>
            <th>SHU</th>
            <th>25%</th>
            <th>75%</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>1</th>
            <th></th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5 (1+2+3+4)</th>
            <th>6</th>
            <th>7 (5-6)</th>
            <th></th>
            <th></th>
        </tr>
        @foreach ($shu->shuDetails as $detail)
            <tr>
                <td>
                    @if ($detail->isSaldoAwal() || $detail->isJumlah())
                        {{ $detail->shuDetailType->name ?? '' }}
                    @else
                        {{ ARR_BULAN[$detail->month] ?? '' }}
                    @endif
                </td>
                <td>{{ number_format($detail->pokok) ?? '' }}</td>
                <td>{{ number_format($detail->wajib) ?? '' }}</td>
                <td>{{ number_format($detail->sukarela) ?? '' }}</td>
                <td>{{ number_format($detail->saldo_pws) ?? '' }}</td>
                <td>{{ number_format($detail->shu_pws) ?? '' }}</td>
                <td>{{ number_format($detail->saldo_khusus) ?? '' }}</td>
                <td>{{ number_format($detail->shu_khusus) ?? '' }}</td>
                <td>{{ number_format($detail->cashback) ?? '' }}</td>
                <td>{{ number_format($detail->kontribusi) ?? '' }}</td>
                <td>{{ number_format($detail->total_shu_sebelum_pajak) ?? '' }}</td>
                <td>{{ number_format($detail->pajak_pph) ?? '' }}</td>
                <td>{{ number_format($detail->total_shu_setelah_pajak) ?? '' }}</td>
                <td>{{ number_format($detail->shu_disimpan) ?? '' }}</td>
                <td>{{ number_format($detail->shu_dibagi) ?? '' }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
