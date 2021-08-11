<style>
    table{
        border-collapse: collapse;
    }
    td{
        padding: 0 3px;
    }
</style>
<table id="table_work" class="table table-bordered table-striped ">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">Kode</a></th>
                    <th><a href="#">Nama Transaksi</a></th>
                    <th><a href="#">Tipe</a></th>
                    <th><a href="#">Kategory</a></th>
                    <th><a href="#">Normal Balance</a></th>
                    <th><a href="#">Induk</a></th>
                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($codetrans as $codetrans)
                <tr>
                    <td class="text-center">{{$loop->iteration}}</td>
                    <td >{{$codetrans->CODE}}</td>
                    <td >{{$codetrans->NAMA_TRANSAKSI}}</td>
                    <td >{{$codetrans->codeType->name}}</td>
                    <td >{{$codetrans->codeCategory->name}}</td>
                    <td >{{$codetrans->normalBalance->name}}</td>
                    <td >{{($codetrans->is_parent==1)?'Induk':'Anak'}}</td>
                    <td class="text-center">
                        <a class="btn btn-info" href="{{ route('kode-transaksi-edit', $codetrans->id) }}"><i class="glyphicon glyphicon-subtitles"></i>edit</a>
                        <a class="btn btn-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="{{ route('kode-transaksi-delete', $codetrans->id) }}"><i class="glyphicon glyphicon-subtitles"></i>hapus</a>
                    </td>

                </tr>
                <?php $i++; ?>
                @endforeach
            </tbody> </table>