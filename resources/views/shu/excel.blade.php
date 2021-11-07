<table class="table table-stripped">
    <thead>
        <tr>
            <th></th>
            <th colspan="5">SALDO AWAL</th>
            <th colspan="13">SIMPANAN POKOK</th>
            <th colspan="13">AMBIL POKOK</th>
            <th colspan="13">SIMPANAN WAJIB</th>
            <th colspan="13">AMBIL WAJIB</th>
            <th colspan="13">SIMPANAN SUKARELA</th>
            <th colspan="13">AMBIL SUKARELA</th>
            <th colspan="13">SIMPANAN KHUSUS</th>
            <th colspan="13">AMBIL KHUSUS</th>
            <th colspan="5">SALDO AKHIR {{ $year ?? '-' }}</th>
            <th colspan="13">SALDO PWS {{ $year ?? '-' }}</th>
            <th colspan="12">SHU PWS</th>
            <th>0,6444%</th>
            <th colspan="13">SALDO SIMPANAN KHUSUS</th>
            <th colspan="13">SHU JSK</th>
            <th colspan="12">CASH BACK</th>
            <th></th>
            <th>SHU</th>
            <th>PAJAK</th>
            <th colspan="13">SHU SEBELUM PAJAK (PWS,JSK,CB)</th>
            <th colspan="12">PAJAK SHU PSW+JSK+CB</th>
            <th>10%</th>
            <th colspan="13">SHU 100%</th>
            <th colspan="13">SHU 75%</th>
            <th colspan="13">SHU 25%</th>
            <th>SHU</th>
        </tr>
        <tr>
            <th>NO_ANGGOTA</th>
            {{-- SALDO AWAL --}}
            <th>POKOK</th>
            <th>WAJIB</th>
            <th>SUKARELA</th>
            <th>KHUSUS</th>
            <th>TOTAL</th>
            {{-- POKOK --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- WAJIB --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- SUKARELA --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- KHUSUS --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- SALDO AKHIR --}}
            <th>POKOK</th>
            <th>WAJIB</th>
            <th>SUKARELA</th>
            <th>KHUSUS</th>
            <th>TOTAL</th>
            {{-- SALDO PWS --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>SALDO AKHIR</th>
            {{-- SHU PWS --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>SHU PWS</th>
            {{-- SALDO KHS --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>SALDO KHS</th>
            {{-- SHU JSK --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>SHU JSK</th>
            {{-- CASH BACK --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>CASH BACK</th>
            <th>KONTRIBUSI</th>
            <th>SHU BISNIS</th>
            {{-- SHU SEBELUM PAJAK (PWS,JSK,CB) --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- PAJAK SHU PSW+JSK+CB --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- SHU 100% --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- SHU 75% --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            {{-- SHU 25% --}}
            @foreach (ARR_BULAN as $month)
                <th>{{ strtoupper(substr($month, 0, 3)) }}</th>
            @endforeach
            <th>JUMLAH</th>
            <th>DITRANSFER</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listSHU as $shu)
            @php
                $details = $shu->shuDetails;
            @endphp
            <tr>
                <td>{{ $shu->kode_anggota }}</td>
                {{-- SALDO AWAL --}}
                @php
                    $saldoAwal = $details->whereNull('month')
                                        ->where('shu_detail_type_id', SHU_DETAIL_TYPE_SALDO_AWAL)
                                        ->first();
                    $total = $saldoAwal->pokok + $saldoAwal->wajib + $saldoAwal->sukarela + $saldoAwal->saldo_khusus;
                @endphp
                <td>{{ $saldoAwal->pokok }}</td>
                <td>{{ $saldoAwal->wajib }}</td>
                <td>{{ $saldoAwal->sukarela }}</td>
                <td>{{ $saldoAwal->saldo_khusus }}</td>
                <td>{{ $total }}</td>

                {{-- POKOK --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('pokok');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->pokok }}</td>
                @endforeach
                <td>{{ $total }}</td>
                @foreach (ARR_BULAN as $month)
                    <td>0</td>
                @endforeach
                <th>0</th>
                {{-- WAJIB --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('wajib');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->wajib }}</td>
                @endforeach
                <td>{{ $total }}</td>
                @foreach (ARR_BULAN as $month)
                    <td>0</td>
                @endforeach
                <th>0</th>
                {{-- sukarela --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('sukarela');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->sukarela }}</td>
                @endforeach
                <td>{{ $total }}</td>
                @foreach (ARR_BULAN as $month)
                    <td>0</td>
                @endforeach
                <th>0</th>
                {{-- khusus --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('saldo_khusus');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->saldo_khusus }}</td>
                @endforeach
                <td>{{ $total }}</td>
                @foreach (ARR_BULAN as $month)
                    <td>0</td>
                @endforeach
                <th>0</th>
                {{-- saldo akhir --}}
                @php
                    $saldoAkhir = $details->whereNull('month')
                                            ->where('shu_detail_type_id', SHU_DETAIL_TYPE_JUMLAH)
                                            ->first();
                    $total = $saldoAkhir->pokok + $saldoAkhir->wajib + $saldoAkhir->sukarela + $saldoAkhir->saldo_khusus;
                @endphp
                <td>{{ $saldoAkhir->pokok }}</td>
                <td>{{ $saldoAkhir->wajib }}</td>
                <td>{{ $saldoAkhir->sukarela }}</td>
                <td>{{ $saldoAkhir->saldo_khusus }}</td>
                <td>{{ $total }}</td>
                {{-- saldo pws --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('saldo_pws');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->saldo_pws }}</td>
                @endforeach
                <td>{{ $total }}</td>
                {{-- shu pws --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('shu_pws');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->shu_pws }}</td>
                @endforeach
                <td>{{ $total }}</td>
                {{-- khusus --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('saldo_khusus');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->saldo_khusus }}</td>
                @endforeach
                <td>{{ $total }}</td>
                {{-- shu jsk --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('shu_khusus');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->shu_khusus }}</td>
                @endforeach
                <th>{{ $total }}</th>
                {{-- cashback --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('cashback');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->cashback }}</td>
                @endforeach
                <th>{{ $total }}</th>
                <th>{{ $saldoAkhir->kontribusi }}</th>
                <th>0</th>
                {{-- shu sebelum pajak --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('total_shu_sebelum_pajak');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->total_shu_sebelum_pajak }}</td>
                @endforeach
                <th>{{ $total }}</th>
                {{-- shu pajak --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('pajak_pph');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->pajak_pph }}</td>
                @endforeach
                <th>{{ $total }}</th>
                {{-- shu 100 --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('total_shu_setelah_pajak');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->total_shu_setelah_pajak }}</td>
                @endforeach
                <th>{{ $total }}</th>
                {{-- shu 75 --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('shu_dibagi');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->shu_dibagi }}</td>
                @endforeach
                <th>{{ $total }}</th>
                {{-- shu 25 --}}
                @php
                    $detailBulan = $details->whereNotNull('month')
                                            ->values();
                    $total = $detailBulan->sum('shu_disimpan');
                @endphp
                @foreach (ARR_BULAN as $key => $month)
                    @php
                        $bulan = $detailBulan->where('month', $key)->first();
                    @endphp
                    <td>{{ $bulan->shu_disimpan }}</td>
                @endforeach
                <th>{{ $total }}</th>
                <th></th>
            </tr>
        @endforeach
    </tbody>
</table>
