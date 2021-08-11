<style>
    table{
        border-collapse: collapse;
    }
    td{
        padding: 0 3px;
    }
</style>

        <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor</th>
                        <th>Tipe Jurnal</th>
                        <th>Akun Debet</th>
                        <th >Debet</th>
                        <th>Akun Kredit</th>
                        <th >Kredit</th>
                        <th>Keterangan</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totaldebet=0;
                    $totalkredit=0;
                    @endphp
                    @foreach($jurnal as $item)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$item->nomer}}</td>
                        <td>{{$item->tipeJurnal->name}}</td>
                        <td>{{$item->akun_debet}}</td>
                        <td>{{$item->debet}}</td>
                        <td>{{$item->akun_kredit}}</td>
                        <td>{{$item->kredit}}</td>
                        <td>{{$item->keterangan}}</td>
                        
                    </tr>
                    @php
                    $totaldebet +=$item->debet;
                    $totalkredit += $item->kredit;
                    @endphp
                    @endforeach
                    <tr>
                        <td colspan="3">Total</td>
                        <td></td>
                        <td>{{$totaldebet}}</td>
                        <td></td>
                        <td>{{$totalkredit}}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            