<style>
    table{
        border-collapse: collapse;
    }
    td{
        padding: 0 3px;
    }
</style>
<table class="table table-striped table-aktiva">
                    <thead>
                       <tr>
                         <th colspan="5" style="text-align: center; font-weight: bold;"> Neraca Periode {{$request->period}}</th>
                       </tr>
                        <tr>
                            <th></th>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th >Bulan ini</th>
                            <th >Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                                <td colspan="5">AKTIVA</td>
                             </tr>
                        <tr>

                            <td>I</td>
                            <td>AKTIVA LANCAR</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($aktivalancar as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLastMonth'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Aktiva Lancar</td>
                            <td>{{$aktivalancar->sum('saldo') }}</td>
                            <td>{{$aktivalancar->sum('saldoLastMonth') }}</td>
                        </tr>
                        <tr>
                            <td>II</td>
                            <td>AKTIVA TETAP</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($aktivatetap as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLastMonth'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Aktiva Tetap</td>
                            <td>{{$aktivatetap->sum('saldo') }}</td>
                            <td>{{$aktivatetap->sum('saldoLastMonth') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>TOTAL AKTIVA</td>
                            <td>{{$aktivatetap->sum('saldo')+$aktivalancar->sum('saldo') }}</td>
                            <td>{{$aktivatetap->sum('saldoLastMonth')+$aktivalancar->sum('saldoLastMonth') }}</td>
                        </tr>
                            <tr>
                                <td colspan="5">PASIVA</td>
                             </tr>
                        <tr>
                            <td>III</td>
                            <td>KEWAJIBAN LANCAR</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($kewajibanlancar as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLastMonth'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kewajiban Lancar</td>
                            <td>{{$kewajibanlancar->sum('saldo') }}</td>
                            <td>{{$kewajibanlancar->sum('saldoLastMonth') }}</td>
                        </tr>
                        <tr>
                            <td>IV</td>
                            <td>KEWAJIBAN JANGKA PANJANG</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($kewajibanjangkapanjang as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLastMonth'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kewajiban Jangka Panjang</td>
                            <td>{{$kewajibanjangkapanjang->sum('saldo') }}</td>
                            <td>{{$kewajibanjangkapanjang->sum('saldoLastMonth') }}</td>
                        </tr>
                        <tr>
                            <td>V</td>
                            <td>KEKAYAAN BERSIH</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($kekayaanbersih as $item)
                            <tr>
                                <td></td>
                                <td>{{ substr($item['code']->CODE, 0, 3) }}</td>
                                <td>{{ $item['code']->NAMA_TRANSAKSI }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLastMonth'] }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Jumlah Kekayaan Bersih</td>
                            <td>{{$kekayaanbersih->sum('saldo') }}</td>
                            <td>{{$kekayaanbersih->sum('saldoLastMonth') }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>TOTAL PASIVA</td>
                            <td>{{$kekayaanbersih->sum('saldo')+$kewajibanjangkapanjang->sum('saldo')+$kewajibanlancar->sum('saldo') }}</td>
                            <td>{{$kekayaanbersih->sum('saldoLastMonth')+$kewajibanjangkapanjang->sum('saldoLastMonth')+$kewajibanlancar->sum('saldoLastMonth') }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th style="font-style: italic;">&copy; escndl printed on {{\Carbon\Carbon::now()->format('d M Y his')}}</th>
                      </tr>
                    </tfoot>
                </table>
