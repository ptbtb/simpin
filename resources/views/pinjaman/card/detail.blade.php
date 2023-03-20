<style>
use Carbon\Carbon;
    .table{
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
    }
    .table tr td{
        border: 1px solid #000;
        padding: 0 5px;
    }
    tr.border-0 td{
        border: none !important;
    }
    .borderx-0{
        border-left: none !important;
        border-right: none !important;
    }

    .bordery-0{
        border-top: none !important;
        border-bottom: none !important;
    }
    .border-0{
        border-left: none !important;
        border-right: none !important;
        border-top: none !important;
        border-bottom: none !important;
    }
    .border-bottom-0{
        border-bottom: none !important;
    }
    .border-top-0{
        border-top: none !important;
    }
    .border-right-0{
        border-right: none !important;
    }
    .border-left-0{
        border-left: none !important;
    }
</style>
<table class="table">
    <tr class="border-0">
        <td colspan="9" class="border-0">Koperasi Pegawai Maritim</td>
        <td>Tanggal</td>
        <td>:</td>
        <td>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
    </tr>
    <tr class="border-0">
        <td colspan="9" class="border-0">JAKARTA</td>
        <td>Halaman</td>
        <td>:</td>
        <td>1/1</td>
    </tr>
    <tr class="border-0">
        <td colspan="12" style="text-align: center; font-weight: bold">KARTU PINJAMAN ANGGOTA</td>
    </tr>
    <tr>
        <td class="border-right-0 border-bottom-0">No. Anggota</td>
        <td class="borderx-0 border-bottom-0">:</td>
        <td colspan="10" class="border-left-0 border-bottom-0" style="text-align: left">{{ $anggota->kode_anggota }}</td>
    </tr>
    <tr>
        <td class="border-right-0 bordery-0">Nama Lengkap</td>
        <td class="border-0">:</td>
        <td colspan="10" class="border-left-0 bordery-0" style="text-align: left">{{ $anggota->nama_anggota }}</td>
    </tr>
    <tr>
        <td class="border-right-0 bordery-0">Tanggal Lahir</td>
        <td class="border-0">:</td>
        <td colspan="10" class="border-left-0 bordery-0" style="text-align: left">{{ $anggota->tgl_lahir->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <td class="border-right-0 bordery-0">Jenis Kelamin</td>
        <td class="border-0">:</td>
        <td colspan="10" class="border-left-0 bordery-0" style="text-align: left">{{ ($anggota->jenis_kelamin)? ($anggota->jenis_kelamin == 'L')? 'LAKI-LAKI':'PEREMPUAN':'-' }}</td>
    </tr>
    <tr>
        <td class="border-right-0 border-top-0">Unit</td>
        <td class="border-top-0 borderx-0">:</td>
        <td colspan="10" class="border-left-0 border-top-0" style="text-align: left">{{ ($anggota->lokasi_kerja)? $anggota->lokasi_kerja:'-' }}</td>
    </tr>
    <tr>
        <td colspan="8" class="border-right-0">Pinjaman</td>
        <td colspan="4" class="border-left-0">Angsuran</td>
    </tr>
    <tr>
        <td colspan="4" class="border-right-0">Bulan</td>
        <td colspan="4" class="border-left-0">Jumlah</td>
        <td colspan="2" class="border-right-0">Tanggal</td>
        <td colspan="2" style="text-align: right" class="border-left-0">Jumlah</td>
    </tr>
    @php
        $jumlahPinjaman = 0;
        $jumlahAngsuran = 0;
        $countPinjaman = 0;
        $countAngsuran = 0;
        $maxIteration = 0;
//        dd($listSimpanan);
    @endphp
    @foreach ($listPinjaman as $value)
        @php
            $countPinjaman = $value->list->count();
            $countAngsuran = $value->withdrawalList->count();
            if ($countPinjaman > $countAngsuran)
            {
                $maxIteration = $countPinjaman;
            }
            else
            {
                $maxIteration = $countAngsuran;
            }
            $list = $value->list;
            $withdrawalList = $value->withdrawalList;
        @endphp
    @if ($value->final_balance!==0)
        <tr>
            <td colspan="4" style="text-align: center" class="border-right-0 bordery-0">{{ strtoupper($value->name) }}</td>
            <td colspan="4" style="text-align: right" class="border-left-0 bordery-0">{{ number_format($value->balance,0) }}</td>
            <td colspan="2" class="border-right-0 bordery-0"></td>
            <td colspan="2" class="border-left-0 bordery-0"></td>
        </tr>
        @for ($i = 0; $i < $maxIteration; $i++)
            <tr>
                @if (isset($list[$i]))
                    <td colspan="4" class="border-right-0 bordery-0">{{ $list[$i]->tgl_transaksi }}</td>
                    <td class="border-0">{{ number_format($list[$i]->debet,0) }}</td>
                    <td colspan="3" class="border-left-0 bordery-0"></td>
                @else
                    <td colspan="4" class="border-right-0 bordery-0"></td>
                    <td class="border-0"></td>
                    <td colspan="3" class="border-left-0 bordery-0"></td>
                @endif
                @if (isset($withdrawalList[$i]))
                    <td colspan="2" class="border-right-0 bordery-0">{{ $withdrawalList[$i]->tgl_transaksi}}</td>
                    <td colspan="2" class="border-left-0 bordery-0" style="text-align: right">{{ number_format($withdrawalList[$i]->kredit,0) }}</td>
                @else
                    <td colspan="2" class="border-right-0 bordery-0"></td>
                    <td colspan="2" class="border-left-0 bordery-0"></td>
                @endif
            </tr>
        @endfor
        <tr>
            <td colspan="3" style="text-align: right" class="border-right-0 bordery-0">Sub Jumlah</td>
            <td class="border-0">:</td>
            <td colspan="4" style="text-align: right" class="border-left-0 bordery-0">{{ number_format($value->amount,0) }}</td>
            <td colspan="2" class="border-right-0 bordery-0"></td>
            <td colspan="2" class="border-left-0 bordery-0"></td>
        </tr>
        <tr>
            <td colspan="8" style="text-align: right" class="border-top-0">
                {{ number_format($value->final_balance - $value->withdrawalAmount,0) }}
                @php
                    $jumlahPinjaman = $jumlahPinjaman + $value->final_balance;
                @endphp
            </td>
            <td colspan="2" class="border-top-0 border-right-0"></td>
            <td colspan="2" class="border-top-0 border-left-0"></td>
        </tr>
        @php
            $jumlahAngsuran = $jumlahAngsuran + $value->withdrawalAmount;
        @endphp
        @endif
    @endforeach
    <tr>
        <td colspan="3" style="text-align: right" class="border-right-0">Jumlah</td>
        <td class="borderx-0">:</td>
        <td colspan="4" style="text-align: right; font-weight: bold" class="border-left-0">{{ number_format($jumlahPinjaman,0) }}</td>
        <td colspan="2" style="text-align: right" class="border-right-0">Jumlah :</td>
        <td colspan="2" style="text-align: right; font-weight: bold" class="border-left-0">{{ number_format($jumlahAngsuran,0) }}</td>
    </tr>
    <tr>
        <td colspan="3" style="text-align: right" class="border-right-0">Jumlah Sisa Pinjaman</td>
        <td class="borderx-0">:</td>
        <td colspan="4" style="text-align: right; font-weight: bold" class="borderx-0">{{ number_format($jumlahPinjaman - $jumlahAngsuran,0) }}</td>
        <td colspan="4" class="border-left-0"></td>
    </tr>
</table>
