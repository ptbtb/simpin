<style>
    table{
        border-collapse: collapse;
    }
    td{
        padding: 0 3px;
    }
</style>
@php
                            $saldoUntilBeforeMonthPend = 0;
                            $saldoPend = 0;
                            $saldoUntilMonthPend =0;
                        @endphp
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th rowspan="2">Rek</th>
                            <th rowspan="2">Nama</th>
                            <th rowspan="2" >Anggaran Tahun {{ $request->year }}</th>
                            <th rowspan="2" >Anggaran Triwulan</th>
                            <th rowspan="2" >S/D Bulan Lalu</th>
                            <th rowspan="2" >Bulan Ini</th>
                            <th rowspan="2" >S/D Bulan Ini</th>
                            <th colspan="2" >TREND</th>
                        </tr>
                        <tr>
                            <th >7/3</th>
                            <th >7/4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>PENDAPATAN</b></td>
                        </tr>
                        @foreach ($pendapatan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPend += $item['saldoUntilBeforeMonth'];
                            $saldoPend += $item['saldo'];
                            $saldoUntilMonthPend += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Pendapatan</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthPend}}</td>
                            <td>{{$saldoPend}}</td>
                            <td>{{$saldoUntilMonthPend}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA</b></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PEGAWAI</b></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPegawai = 0;
                            $saldoPegawai = 0;
                            $saldoUntilMonthPegawai = 0;
                        @endphp
                        @foreach ($biayapegawai as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPegawai += $item['saldoUntilBeforeMonth'];
                            $saldoPegawai += $item['saldo'];
                            $saldoUntilMonthPegawai += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya Pegawai</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthPegawai}}</td>
                            <td>{{$saldoPegawai}}</td>
                            <td>{{$saldoUntilMonthPegawai}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA OPERASIONAL</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthOp = 0;
                            $saldoOp = 0;
                            $saldoUntilMonthOp = 0;
                        @endphp
                        @foreach ($biayaoperasional as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthOp += $item['saldoUntilBeforeMonth'];
                            $saldoOp += $item['saldo'];
                            $saldoUntilMonthOp += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya Operasional</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthOp}}</td>
                            <td>{{$saldoOp}}</td>
                            <td>{{$saldoUntilMonthOp}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PERAWATAN</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthPrwt = 0;
                            $saldoPrwt = 0;
                            $saldoUntilMonthPrwt = 0;
                        @endphp
                        @foreach ($biayaperawatan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPrwt += $item['saldoUntilBeforeMonth'];
                            $saldoPrwt += $item['saldo'];
                            $saldoUntilMonthPrwt += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya Perawatan</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthPrwt}}</td>
                            <td>{{$saldoPrwt}}</td>
                            <td>{{$saldoUntilMonthPrwt}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA PENYUSUTAN</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthPnyust = 0;
                            $saldoPnyust = 0;
                            $saldoUntilMonthPnyust = 0;
                        @endphp
                         @foreach ($biayapenyusutan as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthPnyust += $item['saldoUntilBeforeMonth'];
                            $saldoPnyust += $item['saldo'];
                            $saldoUntilMonthPnyust += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya Penyusutan</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthPnyust}}</td>
                            <td>{{$saldoPnyust}}</td>
                            <td>{{$saldoUntilMonthPnyust}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>BIAYA ADMINISTRASI DAN UMUM</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthAdm = 0;
                            $saldoAdm = 0;
                            $saldoUntilMonthAdm = 0;
                        @endphp
                        @foreach ($biayaadminum as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthAdm += $item['saldoUntilBeforeMonth'];
                            $saldoAdm += $item['saldo'];
                            $saldoUntilMonthAdm += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya Administrasi dan Umum</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthAdm}}</td>
                            <td>{{$saldoAdm}}</td>
                            <td>{{$saldoUntilMonthAdm}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthTotalBiaya = $saldoUntilBeforeMonthAdm + $saldoUntilBeforeMonthPegawai + $saldoUntilBeforeMonthOp + $saldoUntilBeforeMonthPrwt + $saldoUntilBeforeMonthPnyust;
                            $saldoTotalBiaya = $saldoAdm + $saldoPegawai + $saldoOp + $saldoPrwt + $saldoPnyust;
                            $saldoUntilMonthTotalBiaya = $saldoUntilMonthAdm + $saldoUntilMonthPegawai + $saldoUntilMonthOp + $saldoUntilMonthPrwt + $saldoUntilMonthPnyust;
                        @endphp
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Jumlah Biaya</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthTotalBiaya}}</td>
                            <td>{{$saldoTotalBiaya}}</td>
                            <td>{{$saldoUntilMonthTotalBiaya}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Laba/Rugi sebelum luar usaha</td>
                            <td></td>
                            <td></td>
                            <td> {{$saldoUntilBeforeMonthPend - $saldoUntilBeforeMonthTotalBiaya}}</td>
                            <td> {{$saldoPend - $saldoTotalBiaya}}</td>
                            <td> {{$saldoUntilMonthPend - $saldoUntilMonthTotalBiaya}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="8"><b>Pend dan Biaya di Luar Usaha</b></td>
                        </tr>
                        @php
                            $saldoUntilBeforeMonthLu = 0;
                            $saldoLu = 0;
                            $saldoUntilMonthLu = 0;
                        @endphp
                        @foreach ($luarusaha as $item)
                        <tr>
                            <td>{{ substr($item['code']->CODE, 0, 6) }}</td>
                            <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                            <td></td>
                            <td></td>
                            <td>{{$item['saldoUntilBeforeMonth']}}</td>
                            <td>{{$item['saldo']}}</td>
                            <td>{{$item['saldoUntilMonth']}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                         @php
                            $saldoUntilBeforeMonthLu += $item['saldoUntilBeforeMonth'];
                            $saldoLu += $item['saldo'];
                            $saldoUntilMonthLu += $item['saldoUntilMonth'];
                        @endphp
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Sls Pend dan Biaya di Luar Usaha</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthLu}}</td>
                            <td>{{$saldoLu}}</td>
                            <td>{{$saldoUntilMonthLu}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align:right;">Laba/Rugi setelah luar usaha</td>
                            <td></td>
                            <td></td>
                            <td>{{$saldoUntilBeforeMonthPend-($saldoUntilBeforeMonthLu + $saldoUntilBeforeMonthTotalBiaya) }} </td>
                            <td>{{$saldoPend-($saldoLu + $saldoTotalBiaya )}} </td>
                            <td>{{$saldoUntilMonthPend-($saldoUntilMonthLu + $saldoUntilMonthTotalBiaya)}}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            