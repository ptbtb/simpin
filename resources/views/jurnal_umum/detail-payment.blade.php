<div class="table-responsive">
    <table class="table table-striped">
        <tr>
            <td style="width:20%">Tanggal Transaksi</td>
            <td style="width:5%">:</td>
            <td>
                @if ($jurnalUmum->tgl_transaksi)
                    {{ $jurnalUmum->tgl_transaksi->format('d-m-Y') }}
                @else
                    -
                @endif
            </td>
            <td>Deskripsi</td>
            <td>:</td>
            <td>{{ $jurnalUmum->deskripsi or '-' }}</td>
            <td>Lampiran</td>
            <td>:</td>
            <td>
                @foreach ($jurnalUmum->jurnalUmumLampirans as $jurnalUmumLampiran)
                    <a class="btn btn-warning btn-sm" href="{{ asset($jurnalUmumLampiran->lampiran) }}" target="_blank"><i
                            class="fa fa-file"></i></a>
                @endforeach
            </td>
        </tr>
    </table>
</div>
<div class="row mt-2">
    <div class="table-responsive col-md-6">
        <table class="table table-striped">
            <tr>
                <td colspan="4" class="text-center"><b>Debet</b></td>
            </tr>
            <tr>
                <td>No</td>
                <td>Kode</td>
                <td>Nama</td>
                <td>Nominal</td>
            </tr>
            @foreach ($itemDebets as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->code->CODE }}</td>
                    <td>{{ $item->code->NAMA_TRANSAKSI }}</td>
                    <td>{{ $item->nominal_rupiah }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td><b>Total</b></td>
                <td>{{ $jurnalUmum->total_nominal_debet }}</td>
            </tr>
        </table>
    </div>
    <div class="table-responsive col-md-6">
        <table class="table table-striped">
            <tr>
                <td colspan="4" class="text-center"><b>Kredit</b></td>
            </tr>
            <tr>
                <td>No</td>
                <td>Kode</td>
                <td>Nama</td>
                <td>Nominal</td>
            </tr>
            @foreach ($itemCredits as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->code->CODE }}</td>
                    <td>{{ $item->code->NAMA_TRANSAKSI }}</td>
                    <td>{{ $item->nominal_rupiah }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td><b>Total</b></td>
                <td>{{ $jurnalUmum->total_nominal_kredit }}</td>
            </tr>
        </table>
    </div>
</div>
