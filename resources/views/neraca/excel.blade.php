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
                            <th>Jenis</th>
                            <th>Rek</th>
                            <th>Nama Rekening</th>
                            <th >Bulan ini</th>
                            <th >Bulan lalu</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($list as $item)
                            <tr>
                                <td>{{ $item['Kategori'] }}</td>
                                <td>{{ $item['CODE'] }}</td>
                                <td>{{ $item['NAMA_TRANSAKSI'] }}</td>
                                <td>{{$item['saldo'] }}</td>
                                <td>{{$item['saldoLalu'] }}</td>
                            </tr>
                        @endforeach




                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="5"  style="font-style: italic;">&copy; escndl printed on {{\Carbon\Carbon::now()->format('d M Y his')}}</th>
                      </tr>
                    </tfoot>
                </table>
